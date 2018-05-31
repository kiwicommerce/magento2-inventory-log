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

/**
 * Inventory log module observer
 */
class ImportProductData implements ObserverInterface
{

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Product
     */
    public $import;
    
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
     * @var \KiwiCommerce\InventoryLog\Model\ResourceModel\Movement
     */
    private $movementResourceModel;

    /**
     * ImportProductData constructor.
     * @param InventoryLogHelper $helper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface
     * @param \KiwiCommerce\InventoryLog\Model\ResourceModel\Movement $movementResourceModel
     * @param \Magento\ImportExport\Model\Import $import
     * @param \KiwiCommerce\InventoryLog\Model\MovementFactory|null $movementFactory
     * @param \KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface|null $movementRepository
     */
    public function __construct(
        InventoryLogHelper $helper,
        \Magento\Framework\Registry $registry,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface,
        \KiwiCommerce\InventoryLog\Model\ResourceModel\Movement $movementResourceModel,
        \Magento\ImportExport\Model\Import $import,
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
        $this->movementResourceModel = $movementResourceModel;
        $this->import=$import;
    }

    /**
     * Insert inventory log for stock item
     * @param EventObserver $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isModuleEnabled()) {
            $adapter = $observer->getEvent()->getAdapter();
            $this->import->getDataSourceModel()->getIterator()->rewind();
            $productData = $this->registry->registry(InventoryLogHelper::MOVEMENT_DATA);
            while ($bunch = $adapter->getNextBunch()) {
                foreach ($bunch as $rowData) {
                    $newSku = $adapter->getNewSku($rowData['sku']);
                    if (!empty($newSku) && isset($newSku['entity_id'])) {
                        $stockItem = $this->stockRegistryInterface->getStockItem($newSku['entity_id']);
                        $oldQty = 0;
                        if (isset($stockItem['product_id'])) {
                            if (isset($productData[$stockItem['product_id']])) {
                                $oldQty = $productData[$stockItem['product_id']];
                            }
                            $stockItem->setOldQty($oldQty);
                            $this->movementRepository->insertStockMovement($stockItem, __('Product import'));
                        }
                    }
                }
                $this->helper->unRegisterAllData();
            }
            $this->helper->unRegisterAllData();
        }
    }
}
