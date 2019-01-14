<?php
namespace putyourlightson\elementstatusevents\events;

use yii\base\Event;

class ElementStatusesChangedEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var ElementInterface[]
     */
    public $elements;
}