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
class AfterImportProductData implements ObserverInterface
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
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    public $stockRegistry;
    
    /**
     * @var \KiwiCommerce\InventoryLog\Model\ResourceModel\Movement
     */
    private $movementResourceModel;

    /**
     * AfterImportProductData constructor.
     * @param InventoryLogHelper $helper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface
     * @param \KiwiCommerce\InventoryLog\Model\ResourceModel\Movement $movementResourceModel
     * @param \KiwiCommerce\InventoryLog\Model\MovementFactory|null $movementFactory
     * @param \KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface|null $movementRepository
     */
    public function __construct(
        InventoryLogHelper $helper,
        \Magento\Framework\Registry $registry,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface,
        \KiwiCommerce\InventoryLog\Model\ResourceModel\Movement $movementResourceModel,
        \KiwiCommerce\InventoryLog\Model\MovementFactory $movementFactory = null,
        \KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface $movementRepository = null
    ) {
        $this->registry = $registry;
        $this->helper = $helper;
        $this->stockRegistry = $stockRegistry;
        $this->stockRegistryInterface = $stockRegistryInterface;
        $this->movementFactory = $movementFactory
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\KiwiCommerce\InventoryLog\Model\MovementFactory::class);
        $this->movementRepository = $movementRepository
            ?: \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface::class);
        $this->movementResourceModel = $movementResourceModel;
    }

    /**
     * Insert inventory log for stock item
     * @param EventObserver $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isModuleEnabled()) {
            $this->import = $observer->getEvent()->getAdapter();
            if ($products = $observer->getEvent()->getBunch()) {
                $data = [];
                foreach ($products as $product) {
                    $newSku = $this->import->getNewSku($product['sku']);
                    if (!empty($newSku) && isset($newSku['entity_id'])) {
                        $stockItem = $this->movementResourceModel->getStockItemByProduct($newSku['entity_id']);
                        $data[$newSku['entity_id']] = $stockItem;
                    }
                }
                $this->registry->register(InventoryLogHelper::MOVEMENT_DATA, $data);
            }
        }
    }
}
