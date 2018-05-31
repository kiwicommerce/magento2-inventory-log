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

namespace KiwiCommerce\InventoryLog\Plugin\Import;

use KiwiCommerce\InventoryLog\Helper\Data as InventoryLogHelper;
use Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor;
use Magento\Framework\Model\ResourceModel\Db\TransactionManagerInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Catalog\Model\Config as CatalogConfig;

class Product extends \Magento\CatalogImportExport\Model\Import\Product
{
    /**
     * @var InventoryLogHelper
     */
    public $helper;

    /**
     * Product constructor.
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\ImportExport\Helper\Data $importExportData
     * @param \Magento\ImportExport\Model\ResourceModel\Import\Data $importData
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\ImportExport\Model\Import\Config $importConfig
     * @param \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory $resourceFactory
     * @param \Magento\CatalogImportExport\Model\Import\Product\OptionFactory $optionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setColFactory
     * @param \Magento\CatalogImportExport\Model\Import\Product\Type\Factory $productTypeFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\LinkFactory $linkFactory
     * @param \Magento\CatalogImportExport\Model\Import\Proxy\ProductFactory $proxyProdFactory
     * @param \Magento\CatalogImportExport\Model\Import\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory $stockResItemFac
     * @param DateTime\TimezoneInterface $localeDate
     * @param DateTime $dateTime
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     * @param \Magento\CatalogImportExport\Model\Import\Product\StoreResolver $storeResolver
     * @param \Magento\CatalogImportExport\Model\Import\Product\SkuProcessor $skuProcessor
     * @param \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor $categoryProcessor
     * @param \Magento\CatalogImportExport\Model\Import\Product\Validator $validator
     * @param ObjectRelationProcessor $objectRelationProcessor
     * @param TransactionManagerInterface $transactionManager
     * @param \Magento\CatalogImportExport\Model\Import\Product\TaxClassProcessor $taxClassProcessor
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\Url $productUrl
     * @param InventoryLogHelper $helper
     * @param array $data
     * @param array $dateAttrCodes
     * @param CatalogConfig|null $catalogConfig
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Eav\Model\Config $config,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\ImportExport\Model\Import\Config $importConfig,
        \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory $resourceFactory,
        \Magento\CatalogImportExport\Model\Import\Product\OptionFactory $optionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setColFactory,
        \Magento\CatalogImportExport\Model\Import\Product\Type\Factory $productTypeFactory,
        \Magento\Catalog\Model\ResourceModel\Product\LinkFactory $linkFactory,
        \Magento\CatalogImportExport\Model\Import\Proxy\ProductFactory $proxyProdFactory,
        \Magento\CatalogImportExport\Model\Import\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory $stockResItemFac,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        DateTime $dateTime,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\CatalogImportExport\Model\Import\Product\StoreResolver $storeResolver,
        \Magento\CatalogImportExport\Model\Import\Product\SkuProcessor $skuProcessor,
        \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor $categoryProcessor,
        \Magento\CatalogImportExport\Model\Import\Product\Validator $validator,
        ObjectRelationProcessor $objectRelationProcessor,
        TransactionManagerInterface $transactionManager,
        \Magento\CatalogImportExport\Model\Import\Product\TaxClassProcessor $taxClassProcessor,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\Url $productUrl,
        \KiwiCommerce\InventoryLog\Helper\Data $helper,
        array $data = [],
        array $dateAttrCodes = [],
        CatalogConfig $catalogConfig = null
    ) {
        parent::__construct(
            $jsonHelper,
            $importExportData,
            $importData,
            $config,
            $resource,
            $resourceHelper,
            $string,
            $errorAggregator,
            $eventManager,
            $stockRegistry,
            $stockConfiguration,
            $stockStateProvider,
            $catalogData,
            $importConfig,
            $resourceFactory,
            $optionFactory,
            $setColFactory,
            $productTypeFactory,
            $linkFactory,
            $proxyProdFactory,
            $uploaderFactory,
            $filesystem,
            $stockResItemFac,
            $localeDate,
            $dateTime,
            $logger,
            $indexerRegistry,
            $storeResolver,
            $skuProcessor,
            $categoryProcessor,
            $validator,
            $objectRelationProcessor,
            $transactionManager,
            $taxClassProcessor,
            $scopeConfig,
            $productUrl,
            [],
            [],
            null
        );
        $this->helper = $helper;
    }
    
    /**
     * @param array $productIdsToReindex
     */
    private function reindexProducts($productIdsToReindex = [])
    {
        $indexer = $this->indexerRegistry->get('catalog_product_category');
        if (is_array($productIdsToReindex) && empty($productIdsToReindex) > 0 && !$indexer->isScheduled()) {
            $indexer->reindexList($productIdsToReindex);
        }
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _saveStockItem()
    {
        if ($this->helper->isModuleEnabled()) {
            /** @var $stockResource \Magento\CatalogInventory\Model\ResourceModel\Stock\Item */
            $stockResource = $this->_stockResItemFac->create();
            $entityTable = $stockResource->getMainTable();
            while ($bunch = $this->_dataSourceModel->getNextBunch()) {
                $stockData = [];
                $productIdsToReindex = [];
                // Format bunch to stock data rows
                foreach ($bunch as $rowNum => $rowData) {
                    if (!$this->isRowAllowedToImport($rowData, $rowNum)) {
                        continue;
                    }

                    $row = [];
                    $sku = $rowData[self::COL_SKU];
                    if ($this->skuProcessor->getNewSku($sku) !== null) {
                        $row['product_id'] = $this->skuProcessor->getNewSku($sku)['entity_id'];
                        $productIdsToReindex[] = $row['product_id'];

                        $row['website_id'] = $this->stockConfiguration->getDefaultScopeId();
                        $row['stock_id'] = $this->stockRegistry->getStock($row['website_id'])->getStockId();

                        $stockItemDo = $this->stockRegistry->getStockItem($row['product_id'], $row['website_id']);
                        $existStockData = $stockItemDo->getData();

                        $row = array_merge(
                            $this->defaultStockData,
                            array_intersect_key($existStockData, $this->defaultStockData),
                            array_intersect_key($rowData, $this->defaultStockData),
                            $row
                        );

                        if ($this->stockConfiguration->isQty(
                            $this->skuProcessor->getNewSku($sku)['type_id']
                        )
                        ) {
                            $stockItemDo->setData($row);
                            $row['is_in_stock'] = $this->stockStateProvider->verifyStock($stockItemDo);
                            if ($this->stockStateProvider->verifyNotification($stockItemDo)) {
                                $row['low_stock_date'] = $this->dateTime->gmDate(
                                    'Y-m-d H:i:s',
                                    (new \DateTime())->getTimestamp()
                                );
                            }
                            $row['stock_status_changed_auto'] =
                                (int)!$this->stockStateProvider->verifyStock($stockItemDo);
                        } else {
                            $row['qty'] = 0;
                        }

                        $randomString = $this->helper->generateRandomString(5);

                        $uKey = $randomString . $row['product_id'] . time();
                        $row['ukey'] = $uKey;
                    }

                    if (!isset($stockData[$sku])) {
                        $stockData[$sku] = $row;
                    }
                }

                // Insert rows
                if (!empty($stockData)) {
                    $this->_connection->insertOnDuplicate($entityTable, array_values($stockData));
                }

                $this->reindexProducts($productIdsToReindex);
            }
        }
        return $this;
    }
}
