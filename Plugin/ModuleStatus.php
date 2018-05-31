<?php
/**
 * KiwiCommerce
 *
 * Do not edit or add to this file if you wish to upgrade to newer versions in the future.
 * If you wish to customise this module for your needs.
 * Please contact us https://kiwicommerce.co.uk/contacts.
 *
 * @category   KiwiCommerce
 * @package    KiwiCommerce_InventoryLog
 * @copyright  Copyright (C) 2018 KiwiCommerce Ltd (https://kiwicommerce.co.uk/)
 * @license    https://kiwicommerce.co.uk/magento2-extension-license/
 */
namespace KiwiCommerce\InventoryLog\Plugin;

use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use KiwiCommerce\InventoryLog\Helper\Data as StockMovementHelper;

class ModuleStatus
{
    /**
     * @var ConfigResource
     */
    public $configResource;

    /**
     * ModuleStatus constructor.
     * @param ConfigResource $configResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Config\Model\ResourceModel\Config $configResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->configResource = $configResource;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Module\Status $subject
     * @param $isEnabled
     * @param $modules
     * @return array
     */
    public function beforeSetIsEnabled(
        \Magento\Framework\Module\Status $subject,
        $isEnabled,
        $modules
    ) {
        $deleteParams = [];

        $deleteParams['default'][] = 0;
        $stores = $this->_storeManager->getStores();
        if (!empty($stores)) {
            foreach ($stores as $storeKey => $storeVal) {
                $deleteParams['stores'][] = $storeKey;
            }
        }

        $websites = $this->_storeManager->getWebsites();
        if (!empty($websites)) {
            foreach ($websites as $websiteKay => $websiteVal) {
                $deleteParams['websites'][] = $websiteKay;
            }
        }
        
        if (!empty($deleteParams)) {
            foreach ($deleteParams as $deleteParamKay => $deleteParamVals) {
                if (!empty($deleteParamVals)) {
                    foreach ($deleteParamVals as $deleteParamVals) {
                        $this->configResource->deleteConfig(
                            StockMovementHelper::CONFIG_ENABLE_PATH,
                            $deleteParamKay,
                            $deleteParamVals
                        );
                    }
                }
            }
        }
        return [$isEnabled, $modules];
    }
}
