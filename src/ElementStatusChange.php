<?php

namespace putyourlightson\elementstatusevents;

use craft\web\Application as CraftWebApp;
use craft\console\Application as CraftConsoleApp;
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

    /**
     * @event Event
     */
    const EVENT_STATUS_CHANGED = 'statusChanged';

    // Public Methods
    // =========================================================================


    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        // Make sure it's Craft
        if (!($app instanceof CraftWebApp || $app instanceof CraftConsoleApp)) {
            return;
        }

        // Before saving an element
        Event::on(Elements::class, Elements::EVENT_BEFORE_SAVE_ELEMENT, function (ElementEvent $event) {
            /** @var Element|ElementStatusBehavior $element */
            $element = $event->element;

            // Attach behavior to element
            $element->attachBehavior('elementStatusEvents', ElementStatusBehavior::class);

            // Call onBeforeSaveStatus if not a new element
            if (!$event->isNew) {
                $element->rememberPreviousStatus();
            }
        });

        // After saving an element
        Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, function (ElementEvent $event) {
            /** @var Element|ElementStatusBehavior $element */
            $element = $event->element;

            // Call onAfterSaveStatus if element has the behavior
            if ($element->getBehavior('elementStatusEvents') !== null) {
                $element->fireEventOnChange();
            }
        });
    }
}
