<?php

namespace putyourlightson\elementstatusevents\commands;

use craft\elements\Entry;
use craft\helpers\Db;
use putyourlightson\elementstatusevents\ElementStatusEvents;
use putyourlightson\elementstatusevents\events\StatusChangeEvent;
use yii\base\Event;
use yii\base\Module;
use yii\caching\CacheInterface;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\helpers\BaseConsole as ConsoleHelper;

/**
 * Status change
 */
class ScheduledElements extends Controller
{
    // Constants
    // =========================================================================

    const LAST_CHECK_CACHE_KEY = 'lastScheduledCheck';
    const LAST_CHECK_DEFAULT_INTERVAL = '-24 hours';
    const DATE_FORMAT = 'Y-m-d H:i';

    // Properties
    // =========================================================================

    /**
     * @var CacheInterface
     */
    protected $cache;

    // Public Methods
    // =========================================================================

    /**
     * Element Status Change
     *
     * @param string $id
     * @param Module $module
     * @param CacheInterface $cache
     * @param array $config
     */
    public function __construct(string $id, Module $module, CacheInterface $cache, array $config = [])
    {
        $this->cache = $cache;
        parent::__construct($id, $module, $config);
    }


    /**
     * Checks for scheduled Entries, call this command via cron
     *
     * @param string $forcedCheckInterval Time string of lower bound of the range, e.g. '-2 hours'
     *
     * @return int
     */
    public function actionScheduled($forcedCheckInterval = null)
    {
        $lastCheck = $this->cache->exists(self::LAST_CHECK_CACHE_KEY)
            ? $this->cache->get(self::LAST_CHECK_CACHE_KEY)
            : Db::prepareDateForDb((new \DateTime())->modify(self::LAST_CHECK_DEFAULT_INTERVAL));

        if ($forcedCheckInterval) {
            $lastCheck = Db::prepareDateForDb((new \DateTime())->modify($forcedCheckInterval));
        }

        $now       = Db::prepareDateForDb(new \DateTime());
        $published = $this->getPublishedEntries($lastCheck, $now);
        $expired   = $this->getExpiredEntries($lastCheck, $now);
        $entries   = array_merge($published, $expired);

        // Remember this check
        $this->cache->set(self::LAST_CHECK_CACHE_KEY, $now);

        // Print info
        ConsoleHelper::output(sprintf("> Expired Entries: %d", count($expired)));
        ConsoleHelper::output(sprintf("> Published Entries: %d", count($published)));
        ConsoleHelper::output(sprintf("> Range: %s to %s", $lastCheck, $now));

        if (!count($entries)) {
            return ExitCode::OK;
        }

        $this->fireEvent($published, Entry::STATUS_PENDING);
        $this->fireEvent($expired, Entry::STATUS_LIVE);

        $this->drawTable($entries);

        return ExitCode::OK;
    }

    /**
     * @param array $entries
     */
    protected function drawTable(array $entries)
    {
        $rows = [];

        foreach ($entries as $entry) {
            /** @var Entry $entry */
            $postDateString   = $entry->postDate ? $entry->postDate->format(self::DATE_FORMAT) : '-';
            $expiryDateString = $entry->expiryDate ? $entry->expiryDate->format(self::DATE_FORMAT) : '-';
            $rows[]           = [$entry->title, $postDateString, $expiryDateString];
        };

        echo Table::widget([
            'headers' => ['Title', 'PostDate', 'ExpiryDate'],
            'rows'    => $rows,
        ]);

    }

    /**
     * @param array  $elements
     * @param string $previousStatus
     */
    protected function fireEvent(array $elements, $previousStatus = '')
    {
        if (count($elements) === 0) {
            return;
        }
        foreach ($elements as $element) {
            Event::trigger(
                ElementStatusEvents::class,
                ElementStatusEvents::EVENT_STATUS_CHANGED,
                new StatusChangeEvent([
                    'element'          => $element,
                    'statusBeforeSave' => $previousStatus
                ])
            );
        }
    }


    /**
     * @param $rangeStart
     * @param $rangeEnd
     *
     * @return array
     */
    protected function getPublishedEntries($rangeStart, $rangeEnd): array
    {
        // TODO: Support Product and other Elements with postDate

        // Entries published within time frame
        $entries = (Entry::find()
            ->where(['not', ['postDate' => null]])
            ->andWhere(['between', 'postDate', $rangeStart, $rangeEnd])
            ->withStructure(false)
            ->orderBy(null)
            ->anyStatus()
            ->enabledForSite(true))->all();

        // Exclude manually published entries (postDate ≅ dateUpdated)
        return array_filter($entries, function (Entry $item) {
            $diffInSeconds = abs($item->postDate->getTimestamp() - $item->dateUpdated->getTimestamp());
            return ($diffInSeconds > 60);
        });
    }

    /**
     * @param $rangeStart
     * @param $rangeEnd
     *
     * @return Entry[]
     */
    protected function getExpiredEntries($rangeStart, $rangeEnd): array
    {
        // TODO: Support Product and other Elements with expiryDate

        return (Entry::find()
            ->where(['not', ['expiryDate' => null]])
            ->andWhere(['between', 'expiryDate', $rangeStart, $rangeEnd])
            ->withStructure(false)
            ->orderBy(null)
            ->anyStatus()
            ->enabledForSite(true)
        )->all();
    }
}
