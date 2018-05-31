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
use Magento\Framework\Event\Observer as EventObserver;
use KiwiCommerce\InventoryLog\Helper\Data as InventoryLogHelper;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Catalog inventory module observer
 */
class RevertQuoteInventoryObserver implements ObserverInterface
{
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistryInterface;
    
    /**
     * @var \KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface
     */
    private $movementRepository;

    /**
     * @var InventoryLogHelper
     */
    public $inventoryLogHelper;

    /**
     * @var ProductRepositoryInterface
     */
    public $productRepositoryInterface;

    /**
     * RevertQuoteInventoryObserver constructor.
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface
     * @param InventoryLogHelper $inventoryLogHelper
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param \KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface|null $movementRepository
     */
    public function __construct(
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface,
        InventoryLogHelper $inventoryLogHelper,
        ProductRepositoryInterface $productRepositoryInterface,
        \KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface $movementRepository = null
    ) {
        $this->stockRegistryInterface = $stockRegistryInterface;
        $this->movementRepository = $movementRepository
            ?: \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface::class);
        $this->inventoryLogHelper = $inventoryLogHelper;
        $this->productRepositoryInterface = $productRepositoryInterface;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        if ($this->inventoryLogHelper->isModuleEnabled()) {
            $quote = $observer->getEvent()->getQuote();
            if ($quote) {
                foreach ($quote->getAllItems() as $quoteItem) {
                    $productId = $quoteItem->getProductId();
                    $product = $this->productRepositoryInterface->getById($productId);
                    $productType = $product->getTypeId();
                    if ($productType == ProductType::TYPE_SIMPLE) {
                        $stockItem = $this->stockRegistryInterface->getStockItem($quoteItem->getProductId());
                        $oldQty = $stockItem->getQty();
                        $stockItem->setOldQty($oldQty);
                        $stockItem->setQty($stockItem->getQty() + $quoteItem->getQty());
                        $this->movementRepository->insertStockMovement(
                            $stockItem,
                            __('Revert quote inventory(quote: %1)', $quote->getId())
                        );
                    }
                }
                $this->inventoryLogHelper->unRegisterAllData();
            }
        }
    }
}
