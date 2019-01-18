<?php
namespace putyourlightson\elementstatusevents;

use putyourlightson\elementstatusevents\services\ElementStatusService;
use yii\base\InvalidConfigException;
use yii\base\Module;

/**
 *
 * @property ElementStatusService $elementStatus
 */
class ElementStatusEvents extends Module
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        // Register service
        $this->set('elementStatus', ElementStatusService::class);

        // Registers event listeners
        $this->elementStatus->registerEventListeners();
    }
}