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
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type as ProductType;
use KiwiCommerce\InventoryLog\Helper\Data as InventoryLogHelper;

class RefundOrderInventoryObserver implements ObserverInterface
{
    /**
     * @var StockConfigurationInterface
     */
    public $stockConfiguration;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    public $stockRegistryInterface;

    /**
     * @var ProductRepositoryInterface
     */
    public $productRepositoryInterface;

    /**
     * @var \KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface
     */
    public $movementRepository;

    /**
     * @var InventoryLogHelper
     */
    private $helper;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    public $productMetadata;

    /**
     * RefundOrderInventoryObserver constructor.
     * @param StockConfigurationInterface $stockConfiguration
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface
     * @param \KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface $movementRepository
     * @param InventoryLogHelper $helper
     */
    public function __construct(
        StockConfigurationInterface $stockConfiguration,
        ProductRepositoryInterface $productRepositoryInterface,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface,
        \KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface $movementRepository,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        InventoryLogHelper $helper
    ) {
        $this->stockConfiguration = $stockConfiguration;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->stockRegistryInterface = $stockRegistryInterface;
        $this->movementRepository = $movementRepository;
        $this->helper = $helper;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isModuleEnabled()) {
            /* @var $creditmemo \Magento\Sales\Model\Order\Creditmemo */
            $creditmemo = $observer->getEvent()->getCreditmemo();
            $itemsToUpdate = [];
            foreach ($creditmemo->getAllItems() as $item) {
                $productId = $item->getProductId();
                $product = $this->productRepositoryInterface->getById($productId);
                $productType = $product->getTypeId();
                $qty = $item->getQty();

                if (($item->getBackToStock() && $qty)) {
                    if ($qty && $productType == ProductType::TYPE_SIMPLE) {
                        $stockItem = $this->stockRegistryInterface->getStockItem($item->getProductId());
                        if ($this->productMetadata->getVersion() == '2.2.4') {
                            $oldQty = $stockItem->getQty();
                            $stockItem->setOldQty($oldQty);
                        } else {
                            $oldQty = $stockItem->getQty() - $qty;
                            $stockItem->setOldQty($oldQty);
                        }
                        
                        $msg = __('Product restocked after credit memo creation (credit memo: %s)');
                        $message = sprintf(
                            $msg,
                            $creditmemo->getId()
                        );
                        $this->movementRepository->insertStockMovement($stockItem, $message, 0, $qty);
                        $this->helper->unRegisterAllData();
                    }
                }
            }
        }
    }
}
