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

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use KiwiCommerce\InventoryLog\Helper\Data as InventoryLogHelper;
use Magento\Catalog\Model\Product\Type as ProductType;

/**
 * Inventory log module observer
 */
class CheckoutAllSubmitAfter implements ObserverInterface
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
     * @var \KiwiCommerce\InventoryLog\Model\MovementFactory
     */
    private $movementFactory;
    
    /**
     * @var \KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface
     */
    private $movementRepository;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistryInterface;

    /**
     * @var ProductRepositoryInterface
     */
    public $productRepositoryInterface;

    /**
     * CheckoutAllSubmitAfter constructor.
     * @param InventoryLogHelper $helper
     * @param \Magento\Framework\Registry $registry
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface
     * @param \KiwiCommerce\InventoryLog\Model\MovementFactory|null $movementFactory
     * @param \KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface|null $movementRepository
     */
    public function __construct(
        InventoryLogHelper $helper,
        \Magento\Framework\Registry $registry,
        ProductRepositoryInterface $productRepositoryInterface,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface,
        \KiwiCommerce\InventoryLog\Model\MovementFactory $movementFactory = null,
        \KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface $movementRepository = null
    ) {
        $this->registry = $registry;
        $this->helper = $helper;
        $this->stockRegistryInterface = $stockRegistryInterface;
        $this->movementFactory = $movementFactory
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\KiwiCommerce\InventoryLog\Model\MovementFactory::class);
        $this->movementRepository = $movementRepository
            ?: \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface::class);

        $this->productRepositoryInterface = $productRepositoryInterface;
    }

    /**
     * Insert inventory log for stock item
     * @param EventObserver $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isModuleEnabled()) {
            if ($observer->getEvent()->hasOrders()) {
                $orders = $observer->getEvent()->getOrders();
            } else {
                $orders = [$observer->getEvent()->getOrder()];
            }
            $stockItems = [];
            foreach ($orders as $order) {
                if ($order) {
                    foreach ($order->getAllItems() as $orderItem) {
                        /** @var Mage_Sales_Model_Order_Item $orderItem */
                        $productId = $orderItem->getProductId();
                        $product = $this->productRepositoryInterface->getById($productId);
                        $productType = $product->getTypeId();
                        if ($orderItem->getQtyOrdered() && $productType == ProductType::TYPE_SIMPLE) {
                            $stockItem = $this->stockRegistryInterface->getStockItem($orderItem->getProductId());
                            $oldQty = $stockItem->getQty() + $orderItem->getQtyOrdered();
                            $stockItem->setOldQty($oldQty);
                            if (!isset($stockItems[$stockItem->getId()])) {
                                $stockItems[$stockItem->getId()] = [
                                    'item' => $stockItem,
                                    'orders' => [$order->getIncrementId()],
                                ];
                            } else {
                                $stockItems[$stockItem->getId()]['orders'][] = $order->getIncrementId();
                            }
                        }
                    }

                    if (!empty($stockItems)) {
                        foreach ($stockItems as $data) {
                            $this->movementRepository->insertStockMovement(
                                $data['item'],
                                __('Product ordered (order%1: %2)', count($data['orders']) > 1 ? 's' : '', implode(', ', $data['orders']))
                            );
                        }
                        $this->helper->unRegisterAllData();
                    }
                }
            }
        }
    }
}
