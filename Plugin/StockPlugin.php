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

class StockPlugin
{
    /**
     * @var InventoryLogHelper
     */
    public $helper;
    
    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;
    
    /**
     * @var \KiwiCommerce\InventoryLog\Model\ResourceModel\Movement
     */
    private $movement;

    /**
     * StockPlugin constructor.
     * @param \KiwiCommerce\InventoryLog\Model\ResourceModel\Movement $movement
     * @param InventoryLogHelper $helper
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \KiwiCommerce\InventoryLog\Model\ResourceModel\Movement $movement,
        InventoryLogHelper $helper,
        \Magento\Framework\Registry $registry
    ) {
        $this->helper = $helper;
        $this->registry = $registry;
        $this->movement = $movement;
    }

    /**
     * @param $subject
     * @param \Closure $proceed
     * @param array $items
     * @param $websiteId
     * @param $operator
     */
    public function aroundCorrectItemsQty($subject, \Closure $proceed, array $items, $websiteId, $operator)
    {
        if ($this->helper->isModuleEnabled()) {
            //$proceed($items, $websiteId, $operator);
            $this->movement->correctItemsQty($items, $websiteId, $operator);
        }
    }
}
