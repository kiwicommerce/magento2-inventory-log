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

namespace KiwiCommerce\InventoryLog\Observer;

use Magento\Framework\Event\ObserverInterface;
use KiwiCommerce\InventoryLog\Helper\Data as InventoryLogHelper;

class SaveInventoryDataObserver implements ObserverInterface
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
     * SaveInventoryDataObserver constructor.
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
     * Register flag for inventory log update
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->inventoryLogHelper->isModuleEnabled()) {
            $movementSection = InventoryLogHelper::MOVEMENT_SECTION;
            $newProduct = InventoryLogHelper::NEW_PRODUCT;

            $product = $observer->getEvent()->getProduct();
            if (!$this->registry->registry($newProduct)) {
                if (!$product->getId()) {
                    $this->registry->register($newProduct, 1);
                } else {
                    $this->registry->register($newProduct, 0);
                }
            }

            if (!$this->registry->registry($movementSection)) {
                $this->registry->register($movementSection, InventoryLogHelper::STOCK_UPDATE);
            }
        }
        return $this;
    }
}
