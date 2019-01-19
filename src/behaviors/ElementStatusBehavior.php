<?php

namespace putyourlightson\elementstatusevents\behaviors;

use Craft;
use craft\base\Element;
use putyourlightson\elementstatusevents\ElementStatusChange;
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


    // Public Methods
    // =========================================================================

    /**
     * Saves the status of an element before it is saved
     */
    public function rememberPreviousStatus()
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
    public function fireEventOnChange()
    {
        /** @var Element $element */
        $element = $this->owner;

        if ($this->statusBeforeSave != $element->getStatus()) {
            // Trigger a 'statusChanged' event
            if (Event::hasHandlers(ElementStatusChange::class, ElementStatusChange::EVENT_STATUS_CHANGED)) {
                Event::trigger(
                    ElementStatusChange::class,
                    ElementStatusChange::EVENT_STATUS_CHANGED,
                    new StatusChangeEvent([
                        'element'          => $element,
                        'statusBeforeSave' => $this->statusBeforeSave
                    ])
                );
            }
        }
    }
}
