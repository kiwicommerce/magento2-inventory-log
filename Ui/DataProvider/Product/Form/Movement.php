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

namespace KiwiCommerce\InventoryLog\Ui\DataProvider\Product\Form;

use Magento\Framework\App\RequestInterface;
use KiwiCommerce\InventoryLog\Model\ResourceModel\Movement\CollectionFactory;

class Movement extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \KiwiCommerce\InventoryLog\Model\ResourceModel\Movement\CollectionFactory
     */
    public $collection;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    public $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    public $addFilterStrategies;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * @var RequestInterface
     */
    public $request;
    
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        \Magento\Framework\Registry $coreRegistry,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        $this->request = $request;
        $this->coreRegistry = $coreRegistry;
    }
    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        /** @var Collection $collection */
        $collection = parent::getCollection();
        $productId = $this->request->getParam('current_product_id', 0);
        if ($productId) {
            $collection->addFieldToFilter('product_id', $productId);
        }
        return $collection;
    }
}
