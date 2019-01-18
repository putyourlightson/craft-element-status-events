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
    // Properties
    // =========================================================================

    /**
     * @var bool
     */
    private $_eventListenersRegistered = false;

    // Public Methods
    // =========================================================================

    /**
     * Registers event listeners
     */
    public function registerEventListeners()
    {
        // Ensure event listeners have not already been registered
        if ($this->_eventListenersRegistered) {
            return;
        }

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
            }
        });

        $this->_eventListenersRegistered = true;
    }
}