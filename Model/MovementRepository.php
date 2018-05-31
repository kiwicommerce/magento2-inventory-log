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


namespace KiwiCommerce\InventoryLog\Model;

use KiwiCommerce\InventoryLog\Api\MovementRepositoryInterface;
use KiwiCommerce\InventoryLog\Api\Data\MovementSearchResultsInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use KiwiCommerce\InventoryLog\Model\ResourceModel\Movement as ResourceMovement;
use Magento\Framework\Api\SortOrder;
use KiwiCommerce\InventoryLog\Model\ResourceModel\Movement\CollectionFactory as MovementCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use KiwiCommerce\InventoryLog\Api\Data\MovementInterfaceFactory;
use KiwiCommerce\InventoryLog\Helper\Data as MovementHelper;
use Magento\CatalogInventory\Api\Data\StockItemInterface;

class MovementRepository implements MovementRepositoryInterface
{
    /**
     * @var ResourceMovement
     */
    public $resource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var MovementSearchResultsInterfaceFactory
     */
    public $searchResultsFactory;

    /**
     * @var MovementInterfaceFactory
     */
    public $dataMovementFactory;

    /**
     * @var MovementCollectionFactory
     */
    public $movementCollectionFactory;

    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    public $dataObjectProcessor;

    /**
     * @var MovementFactory
     */
    public $movementFactory;

    /**
     * @var MovementHelper
     */
    public $movementHelper;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    public $productMetadata;

    /**
     * MovementRepository constructor.
     * @param ResourceMovement $resource
     * @param MovementFactory $movementFactory
     * @param MovementInterfaceFactory $dataMovementFactory
     * @param MovementCollectionFactory $movementCollectionFactory
     * @param MovementSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param MovementHelper $movementHelper
     */
    public function __construct(
        ResourceMovement $resource,
        MovementFactory $movementFactory,
        MovementInterfaceFactory $dataMovementFactory,
        MovementCollectionFactory $movementCollectionFactory,
        MovementSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        MovementHelper $movementHelper,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->resource = $resource;
        $this->movementFactory = $movementFactory;
        $this->movementCollectionFactory = $movementCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataMovementFactory = $dataMovementFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->movementHelper = $movementHelper;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param \KiwiCommerce\InventoryLog\Api\Data\MovementInterface $movement
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     * @throws CouldNotSaveException
     */
    public function save(
        \KiwiCommerce\InventoryLog\Api\Data\MovementInterface $movement
    ) {
        try {
            $this->resource->save($movement);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the movement: %1',
                $exception->getMessage()
            ));
        }
        return $movement;
    }

    /**
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @param string $message
     * @return $this|bool
     */
    public function insertStockMovement(
        \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem,
        $message = '',
        $newProduct = 0,
        $qty = 0
    ) {
        if ($this->movementHelper->isModuleEnabled()) {
            if ($stockItem || $newProduct == 1) {
                if ($this->productMetadata->getVersion() == '2.2.4') {
                    if ($qty > 0) {
                        $qty = $stockItem->getQty() + $qty;
                    } else {
                        $qty = $stockItem->getQty();
                    }
                } else {
                    $qty = $stockItem->getQty();
                }

                $oldQty = $stockItem->getOldQty();
                $qtyDifference = ($qty - $oldQty);

                if ($qtyDifference != 0 || $newProduct == 1) {
                    if ($newProduct == 1 && $oldQty == 0 && $qty == 0 && $qtyDifference == 0) {
                        return $this;
                    }
                    $model = $this->movementFactory->create();
                    $model->setStockItemId($stockItem->getItemId());
                    $model->setProductId($stockItem->getProductId());
                    $model->setUserId($this->movementHelper->getUserId());
                    $model->setUsername($this->movementHelper->getUsername());
                    $model->setIsadmin($this->movementHelper->isAdminLoggedIn());
                    $model->setCurrentQty($qty);
                    $model->setQtyMovement($qtyDifference);
                    $model->setOldQty($oldQty);
                    $model->setIsInStock((int)$stockItem->getIsInStock());
                    $model->setMessage($message);
                    $model->setUkey($stockItem->getUkey());
                    $model->setIp($this->movementHelper->getRemoteAddress());
                    $this->save($model);
                }
                return $this;
            }
            return false;
        }
    }

    /**
     * @param string $movementId
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($movementId)
    {
        $movement = $this->movementFactory->create();
        $movement->load($movementId);
        if (!$movement->getId()) {
            throw new NoSuchEntityException(__('Movement with id "%1" does not exist.', $movementId));
        }
        return $movement;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return mixed
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->movementCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * @param \KiwiCommerce\InventoryLog\Api\Data\MovementInterface $movement
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(
        \KiwiCommerce\InventoryLog\Api\Data\MovementInterface $movement
    ) {
        try {
            $this->resource->delete($movement);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the movement: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @param string $movementId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($movementId)
    {
        return $this->delete($this->getById($movementId));
    }
}
