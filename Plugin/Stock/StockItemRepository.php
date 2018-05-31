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

namespace KiwiCommerce\InventoryLog\Plugin\Stock;

use KiwiCommerce\InventoryLog\Helper\Data as InventoryLogHelper;

class StockItemRepository
{
    /**
     * @var InventoryLogHelper
     */
    public $helper;

    /**
     * @var \KiwiCommerce\InventoryLog\Model\ResourceModel\Movement
     */
    private $movementResourceModel;

    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * @var \KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface
     */
    public $movementRepository;

    /**
     * StockItemRepository constructor.
     * @param InventoryLogHelper $helper
     * @param \KiwiCommerce\InventoryLog\Model\ResourceModel\Movement $movementResourceModel
     * @param \Magento\Framework\Registry $registry
     * @param \KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface $movementRepository
     */
    public function __construct(
        InventoryLogHelper $helper,
        \KiwiCommerce\InventoryLog\Model\ResourceModel\Movement $movementResourceModel,
        \Magento\Framework\Registry $registry,
        \KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface $movementRepository
    ) {
        $this->helper = $helper;
        $this->movementResourceModel = $movementResourceModel;
        $this->registry = $registry;
        $this->movementRepository = $movementRepository;
    }

    /**
     * @param $subject
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @return array
     */
    public function beforeSave(
        $subject,
        \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
    ) {
        if ($this->helper->isModuleEnabled()) {
            $uKey = $this->helper->getRandomUniqueString($stockItem->getProductId());
            $stockItem->setUkey($uKey);

            /*Fetch Stock item qty from Table */
            $stockItemTable = $this->movementResourceModel->getStockQty($stockItem->getItemId());
            $stockItem->setOldQty($stockItemTable);
        }
        return [$stockItem];
    }

    /**
     * @param $subject
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $result
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    public function afterSave(
        $subject,
        \Magento\CatalogInventory\Api\Data\StockItemInterface $result
    ) {
        if ($this->helper->isModuleEnabled()) {
            $stockItem = $result;
            $movementSection = $this->registry->registry(InventoryLogHelper::MOVEMENT_SECTION);
            $newProduct = $this->registry->registry(InventoryLogHelper::NEW_PRODUCT);
            if ($movementSection == InventoryLogHelper::STOCK_UPDATE) {
                $message = __('Stock saved manually');
                $stockAutoFlag = $stockItem->getStockStatusChangedAutomaticallyFlag();
                if (!$stockAutoFlag || $stockItem->getOldQty() != $stockItem->getQty() || $newProduct == 1) {
                    $this->movementRepository->insertStockMovement($stockItem, $message, $newProduct);
                }
            } elseif ($movementSection == InventoryLogHelper::ORDER_CANCEL) {
                $movementData = $this->registry->registry(InventoryLogHelper::MOVEMENT_DATA);
                if (!empty($movementData[InventoryLogHelper::ORDER_CANCEL]['order_id'])) {
                    $msg = __('Product restocked after order cancellation (order: %s)');
                    $message = sprintf(
                        $msg,
                        $movementData[InventoryLogHelper::ORDER_CANCEL]['order_id']
                    );
                } else {
                    $message = __('Product restocked after order cancellation');
                }
                $this->movementRepository->insertStockMovement($stockItem, $message);
            }
            $this->helper->unRegisterAllData();
        }
        return $result;
    }
}
