<?php
namespace putyourlightson\elementstatusevents\services;

use craft\base\Component;
use craft\base\Element;
use craft\events\ElementEvent;
use craft\services\Elements;
use putyourlightson\elementstatusevents\behaviors\ElementStatusBehavior;
use yii\base\Event;

class ElementStatusService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Registers event listeners
     */
    public function registerEventListeners()
    {
        parent::init();

        // Before saving an element
        Event::on(Elements::class, Elements::EVENT_BEFORE_SAVE_ELEMENT, function(ElementEvent $event) {
            /** @var Element $element */
            $element = $event->element;

            // Attach behavior to element
            $element->attachBehavior('elementStatusEvents', ElementStatusBehavior::class);

            // Call onBeforeSaveStatus if not a new element
            if (!$event->isNew) {
                $element->onBeforeSaveStatus();
            }
        });

        // After saving an element
        Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, function(ElementEvent $event) {
            /** @var Element $element */
            $element = $event->element;

            // Call onAfterSaveStatus if element has the behavior
            if ($element->getBehavior('elementStatusEvents') !== null) {
                $element->onAfterSaveStatus();

                if ($element->statusChanged) {
                    $this->_elementsChanged[] = $element;
                }
            }
        });
    }
}