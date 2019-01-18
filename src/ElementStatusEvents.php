<?php
namespace putyourlightson\elementstatusevents;

use craft\base\Element;
use craft\events\ElementEvent;
use craft\services\Elements;
use putyourlightson\elementstatusevents\behaviors\ElementStatusBehavior;
use yii\base\BootstrapInterface;
use yii\base\Event;

class ElementStatusEvents implements BootstrapInterface
{
    // Constants
    // =========================================================================

    /**
     * @event Event
     */
    const EVENT_STATUS_CHANGED = 'statusChanged';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        // Before saving an element
        Event::on(Elements::class, Elements::EVENT_BEFORE_SAVE_ELEMENT,
            function(ElementEvent $event) {
                /** @var Element $element */
                $element = $event->element;

                // Attach behavior to element
                $element->attachBehavior('elementStatusEvents', ElementStatusBehavior::class);

                // Call onBeforeSaveStatus if not a new element
                if (!$event->isNew) {
                    $element->onBeforeSaveStatus();
                }
            }
        );

        // After saving an element
        Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT,
            function(ElementEvent $event) {
                /** @var Element $element */
                $element = $event->element;

                // Call onAfterSaveStatus if element has the behavior
                if ($element->getBehavior('elementStatusEvents') !== null) {
                    $element->onAfterSaveStatus();
                }
            }
        );
    }
}