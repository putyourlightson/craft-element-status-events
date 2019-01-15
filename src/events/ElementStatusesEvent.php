<?php
namespace putyourlightson\elementstatusevents\events;

use craft\base\ElementInterface;
use yii\base\Event;

class ElementStatusesEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var ElementInterface[]
     */
    public $elements;
}