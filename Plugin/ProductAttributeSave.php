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

use KiwiCommerce\InventoryLog\Helper\Data as InventoryLogHelper;

class ProductAttributeSave
{
    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * @var InventoryLogHelper
     */
    public $inventoryLogHelper;

    /**
     * ProductAttributeSave constructor.
     * @param \Magento\Framework\Registry $registry
     * @param InventoryLogHelper $inventoryLogHelper
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        InventoryLogHelper $inventoryLogHelper
    ) {
        $this->registry = $registry;
        $this->inventoryLogHelper = $inventoryLogHelper;
    }

    /**
     * @param $subject
     * @return array
     */
    public function beforeExecute(
        $subject
    ) {
        if ($this->inventoryLogHelper->isModuleEnabled()) {
            $movementSection = InventoryLogHelper::MOVEMENT_SECTION;
            if (!$this->registry->registry($movementSection)) {
                $this->registry->register($movementSection, InventoryLogHelper::STOCK_UPDATE);
            }
        }
        return [$subject];
    }
}
