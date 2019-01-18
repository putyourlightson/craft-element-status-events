<?php

namespace putyourlightson\elementstatusevents\behaviors;

use Craft;
use craft\base\Element;
use putyourlightson\elementstatusevents\ElementStatusEvents;
use putyourlightson\elementstatusevents\events\StatusChangeEvent;
use yii\base\Behavior;
use yii\base\Event;

class ElementStatusBehavior extends Behavior
{

    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public $statusBeforeSave = '';

    /**
     * @var bool
     */
    public $statusChanged = false;

    // Public Methods
    // =========================================================================

    /**
     * Saves the status of an element before it is saved
     */
    public function onBeforeSaveStatus()
    {
        /** @var Element $element */
        $element = $this->owner;

        $originalElement = Craft::$app->getElements()->getElementById(
            $element->id,
            get_class($element),
            $element->siteId
        );

        $this->statusBeforeSave = $originalElement === null ?: $originalElement->getStatus();
    }

    /**
     * Triggers an event if the status has changed
     */
    public function onAfterSaveStatus()
    {
        /** @var Element $element */
        $element = $this->owner;

        if ($this->statusBeforeSave != $element->getStatus()) {
            // Trigger a 'statusChanged' event
            if (Event::hasHandlers(ElementStatusEvents::class, ElementStatusEvents::EVENT_STATUS_CHANGED)) {
                Event::trigger(
                    ElementStatusEvents::class,
                    ElementStatusEvents::EVENT_STATUS_CHANGED,
                    new StatusChangeEvent([
                        'element'          => $element,
                        'statusBeforeSave' => $this->statusBeforeSave
                    ])
                );
            }
        }
    }
}
