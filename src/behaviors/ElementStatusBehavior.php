<?php
namespace putyourlightson\elementstatusevents\behaviors;

use Craft;
use craft\base\Element;
use yii\base\Behavior;

class ElementStatusBehavior extends Behavior
{
    // Constants
    // =========================================================================

    /**
     * @event Event
     */
    const EVENT_STATUS_CHANGED = 'statusChanged';

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
            $this->statusChanged = true;

            // Trigger a 'statusChanged' event
            if ($this->owner->hasEventHandlers(self::EVENT_STATUS_CHANGED)) {
                $this->owner->trigger(self::EVENT_STATUS_CHANGED);
            }
        }
    }
}