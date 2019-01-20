<?php

namespace putyourlightson\elementstatusevents;

use craft\console\Application;
use craft\web\Application as CraftWebApp;
use craft\console\Application as CraftConsoleApp;
use putyourlightson\elementstatusevents\commands\ScheduledElements;
use yii\base\BootstrapInterface;
use yii\base\Component;
use craft\base\Element;
use craft\events\ElementEvent;
use craft\services\Elements;
use putyourlightson\elementstatusevents\behaviors\ElementStatusBehavior;
use yii\base\Event;

/**
 * Class ElementStatusChange
 *
 * @package putyourlightson\elementstatusevents
 */
class ElementStatusChange extends Component implements BootstrapInterface
{

    const EVENT_STATUS_CHANGED = 'statusChanged';

    /**
     * Register console command
     *
     * @param \craft\console\Application $app
     * @param string                     $group
     */
    public static function registerScheduledCommand(Application $app, $group = 'element-status-change')
    {
        $app->controllerMap[$group] = ScheduledElements::class;
    }

    /**
     * Bootstrap the extension
     *
     * @param \yii\base\Application $app
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
            self::registerScheduledCommand($app);
        }
    }

    /**
     * Register event listener
     *
     * @param \craft\events\ElementEvent $event
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
     * @param \craft\events\ElementEvent $event
     */
    public function fireEventOnChange(ElementEvent $event)
    {
        /** @var Element|ElementStatusBehavior $element */
        $element = $event->element;

        // Fire ElementStatusChange::EVENT_STATUS_CHANGED
        if ($element->getBehavior('elementStatusEvents') !== null) {
            $element->fireEventOnChange();
        }
    }

}
