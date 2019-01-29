<?php

namespace putyourlightson\elementstatusevents;

use Craft;
use craft\web\Application as CraftWebApp;
use craft\console\Application as CraftConsoleApp;
use putyourlightson\elementstatusevents\commands\ScheduledElements;
use yii\base\Application as YiiApp;
use yii\base\BootstrapInterface;
use yii\base\Component;
use craft\base\Element;
use craft\events\ElementEvent;
use craft\services\Elements;
use putyourlightson\elementstatusevents\behaviors\ElementStatusBehavior;
use yii\base\Event;
use yii\caching\CacheInterface;

/**
 * Class ElementStatusEvents
 *
 * @package putyourlightson\elementstatusevents
 */
class ElementStatusEvents extends Component implements BootstrapInterface
{
    // Constants
    // =========================================================================

    const EVENT_STATUS_CHANGED = 'statusChanged';

    // Public Methods
    // =========================================================================

    /**
     * Register console command
     *
     * @param CraftConsoleApp $app
     * @param string $group
     */
    public static function registerScheduledCommand(CraftConsoleApp $app, $group = 'element-status-events')
    {
        $app->controllerMap[$group] = ScheduledElements::class;
    }

    /**
     * Bootstrap the extension
     *
     * @param YiiApp $app
     */
    public function bootstrap($app)
    {
        // Make sure it's Craft
        if (!($app instanceof CraftWebApp || $app instanceof CraftConsoleApp)) {
            return;
        }

        Event::on(Elements::class, Elements::EVENT_BEFORE_SAVE_ELEMENT, [$this, 'rememberPreviousStatus']);
        Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, [$this, 'fireEventOnChange']);

        if ($app instanceof CraftConsoleApp) {
            // Tell Craft about the concrete implementation of CacheInterface
            Craft::$container->set(CacheInterface::class, Craft::$app->getCache());
            self::registerScheduledCommand($app);
        }
    }

    /**
     * Register event listener
     *
     * @param ElementEvent $event
     */
    public function rememberPreviousStatus(ElementEvent $event)
    {
        /** @var Element|ElementStatusBehavior $element */
        $element = $event->element;

        // Attach behavior to access the status later
        $element->attachBehavior('elementStatusEvents', ElementStatusBehavior::class);

        // No need to remember anything
        if ($event->isNew) {
            return;
        }

        $element->rememberPreviousStatus();
    }

    /**
     * Register event listener
     *
     * @param ElementEvent $event
     */
    public function fireEventOnChange(ElementEvent $event)
    {
        /** @var Element|ElementStatusBehavior $element */
        $element = $event->element;

        // Fire ElementStatusEvents::EVENT_STATUS_CHANGED
        if ($element->getBehavior('elementStatusEvents') !== null) {
            $element->fireEventOnChange();
        }
    }

}
