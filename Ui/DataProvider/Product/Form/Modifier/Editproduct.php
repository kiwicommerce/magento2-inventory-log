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

namespace KiwiCommerce\InventoryLog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\UrlInterface;
use KiwiCommerce\InventoryLog\Helper\Data as InventoryLogHelper;

/**
 * Data provider for advanced inventory form
 */
class Editproduct extends AbstractModifier
{
    const STOCK_DATA_FIELDS = 'stock_data85';

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var StockConfigurationInterface
     */
    private $stockConfiguration;

    /**
     * @var array
     */
    private $meta = [];

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var JsonValidator
     */
    private $jsonValidator;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var InventoryLogHelper
     */
    public $helper;

    /**
     * Editproduct constructor.
     * @param LocatorInterface $locator
     * @param StockRegistryInterface $stockRegistry
     * @param ArrayManager $arrayManager
     * @param StockConfigurationInterface $stockConfiguration
     * @param UrlInterface $urlBuilder
     * @param InventoryLogHelper $helper
     */
    public function __construct(
        LocatorInterface $locator,
        StockRegistryInterface $stockRegistry,
        ArrayManager $arrayManager,
        StockConfigurationInterface $stockConfiguration,
        UrlInterface $urlBuilder,
        InventoryLogHelper $helper
    ) {

        $this->locator = $locator;
        $this->stockRegistry = $stockRegistry;
        $this->arrayManager = $arrayManager;
        $this->stockConfiguration = $stockConfiguration;
        $this->urlBuilder = $urlBuilder;
        $this->helper = $helper;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        $productId = $this->locator->getProduct()->getId();

        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_product_id'] = $productId;
        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_store_id'] = $this->locator->getStore()->getId();

        return $data;
    }

    /**
     * Get Stock Data
     *
     * @param StockItemInterface $stockItem
     * @return array
     */
    private function getData(StockItemInterface $stockItem)
    {
        $result = $stockItem->getData();

        $result[StockItemInterface::MANAGE_STOCK] = (int)$stockItem->getManageStock();
        $result[StockItemInterface::QTY] = (float)$stockItem->getQty();
        $result[StockItemInterface::MIN_QTY] = (float)$stockItem->getMinQty();
        $result[StockItemInterface::MIN_SALE_QTY] = (float)$stockItem->getMinSaleQty();
        $result[StockItemInterface::MAX_SALE_QTY] = (float)$stockItem->getMaxSaleQty();
        $result[StockItemInterface::IS_QTY_DECIMAL] = (int)$stockItem->getIsQtyDecimal();
        $result[StockItemInterface::IS_DECIMAL_DIVIDED]= (int)$stockItem->getIsDecimalDivided();
        $result[StockItemInterface::BACKORDERS] = (int)$stockItem->getBackorders();
        $result[StockItemInterface::NOTIFY_STOCK_QTY] = (float)$stockItem->getNotifyStockQty();
        $result[StockItemInterface::ENABLE_QTY_INCREMENTS] = (int)$stockItem->getEnableQtyIncrements();
        $result[StockItemInterface::QTY_INCREMENTS] = (float)$stockItem->getQtyIncrements();
        $result[StockItemInterface::IS_IN_STOCK] = (int)$stockItem->getIsInStock();

        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $this->prepareMeta();

        return $this->meta;
    }

    /**
     * @return void
     */
    private function prepareMeta()
    {
        $fieldCode = 'quantity_and_stock_status';
        $pathField = $this->arrayManager->findPath($fieldCode, $this->meta, null, 'children');

        if ($pathField) {
            $labelField = $this->arrayManager->get(
                $this->arrayManager->slicePath($pathField, 0, -2) . '/arguments/data/config/label',
                $this->meta
            );
            $fieldSetPath = $this->arrayManager->slicePath($pathField, 0, -4);

            $this->meta = $this->arrayManager->merge(
                $pathField . '/arguments/data/config',
                $this->meta,
                [
                    'label' => __('Stock Status'),
                    'value' => '1',
                    'dataScope' => $fieldCode . '.is_in_stock',
                    'scopeLabel' => '[GLOBAL]',
                    'imports' => [
                        'visible' => '${$.provider}:data.product.stock_data.manage_stock',
                    ],
                ]
            );
            $this->meta = $this->arrayManager->merge(
                $this->arrayManager->slicePath($pathField, 0, -2) . '/arguments/data/config',
                $this->meta,
                [
                    'label' => __('Stock Status'),
                    'scopeLabel' => '[GLOBAL]',
                ]
            );

            $container['arguments']['data']['config'] = [
                'formElement' => 'container',
                'componentType' => 'container',
                'component' => "Magento_Ui/js/form/components/group",
                'label' => $labelField,
                'breakLine' => false,
                'dataScope' => $fieldCode,
                'scopeLabel' => '[GLOBAL]',
                'source' => 'product_details',
                'sortOrder' => (int) $this->arrayManager->get(
                    $this->arrayManager->slicePath($pathField, 0, -2) . '/arguments/data/config/sortOrder',
                    $this->meta
                ) - 1,
            ];
            $qty['arguments']['data']['config'] = [
                'component' => 'Magento_CatalogInventory/js/components/qty-validator-changer',
                'dataType' => 'number',
                'formElement' => 'input',
                'componentType' => 'field',
                'visible' => '1',
                'require' => '0',
                'additionalClasses' => 'admin__field-small',
                'label' => __('Quantity'),
                'scopeLabel' => '[GLOBAL]',
                'dataScope' => 'qty',
                'validation' => [
                    'validate-number' => true,
                    'less-than-equals-to' => StockDataFilter::MAX_QTY_VALUE,
                ],
                'imports' => [
                    'handleChanges' => '${$.provider}:data.product.stock_data.is_qty_decimal',
                ],
                'sortOrder' => 10,
            ];
            $advancedInventoryButton['arguments']['data']['config'] = [
                'displayAsLink' => true,
                'formElement' => 'container',
                'componentType' => 'container',
                'component' => 'Magento_Ui/js/form/components/button',
                'template' => 'ui/form/components/button/container',
                'actions' => [
                    [
                        'targetName' => 'product_form.product_form.advanced_inventory_modal',
                        'actionName' => 'toggleModal',
                    ],
                ],
                'title' => __('Advanced Inventory'),
                'provider' => false,
                'additionalForGroup' => true,
                'source' => 'product_details',
                'sortOrder' => 20,
            ];
            
            $targetName = 'product_form.product_form.product-details.stockmovementmodel.stock_movement_listing';

            if ($this->helper->isModuleEnabled() && $this->helper->isOutputEnabled() && $this->helper->isAllowed()) {
                $advancedInventoryButton1['arguments']['data']['config'] = [
                    'displayAsLink' => true,
                    'formElement' => 'container',
                    'componentType' => 'insertListing',
                    'autoRender' => false,
                    'dataScope' => 'stock_movement_listing',
                    'externalProvider' => 'stock_movement_listing.stock_movement_listing_data_source',
                    'selectionsProvider' => 'stock_movement_listing.stock_movement_listing.stock_movement_columns.ids',
                    'ns' => 'stock_movement_listing',
                    'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                    'realTimeLink' => true,
                    'component' => 'Magento_Ui/js/form/components/button',
                    'template' => 'ui/form/components/button/container',
                    'behaviourType' => 'simple',
                    'externalFilterMode' => true,
                    'dataLinks' => [
                        'imports' => false,
                        'exports' => true
                    ],
                    'actions' => [
                        [
                            'targetName' => 'product_form.product_form.product-details.stockmovementmodel',
                            'actionName' => 'toggleModal',
                        ],
                        [
                            'targetName' => $targetName,
                            'actionName' => 'render',
                        ],
                    ],
                    'imports' => [
                        'productId' => '${ $.provider }:data.product.current_product_id',
                        'storeId' => '${ $.provider }:data.product.current_store_id',
                    ],
                    'exports' => [
                        'productId' => '${ $.externalProvider }:params.current_product_id',
                        'storeId' => '${ $.externalProvider }:params.current_store_id',
                    ],
                    'title' => __('Inventory log'),
                    'provider' => false,
                    'additionalForGroup' => true,
                    'source' => 'product_details',
                    'sortOrder' => 21,
                ];
                $container['children'] = [
                    'qty' => $qty,
                    'advanced_inventory_button' => $advancedInventoryButton,
                    'al_inventory_log_button' => $advancedInventoryButton1,
                ];
            } else {
                $container['children'] = [
                    'qty' => $qty,
                    'advanced_inventory_button' => $advancedInventoryButton
                ];
            }

            $this->meta = $this->arrayManager->merge(
                $fieldSetPath . '/children',
                $this->meta,
                ['quantity_and_stock_status_qty' => $container]
            );
        }
    }
}
