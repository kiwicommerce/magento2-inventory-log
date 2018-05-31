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

namespace KiwiCommerce\InventoryLog\Plugin;

use KiwiCommerce\InventoryLog\Helper\Data as InventoryLogHelper;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Api\ProductRepositoryInterface;

class CancelOrderItem
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
     * @var ProductRepositoryInterface
     */
    public $productRepositoryInterface;

    /**
     * CancelOrderItem constructor.
     * @param InventoryLogHelper $helper
     * @param \Magento\Framework\Registry $registry
     * @param ProductRepositoryInterface $productRepositoryInterface
     */
    public function __construct(
        InventoryLogHelper $helper,
        \Magento\Framework\Registry $registry,
        ProductRepositoryInterface $productRepositoryInterface
    ) {
    
        $this->helper = $helper;
        $this->registry = $registry;
        $this->productRepositoryInterface = $productRepositoryInterface;
    }

    /**
     * @param $subject
     * @param EventObserver $observer
     * @return array
     */
    public function beforeExecute($subject, EventObserver $observer)
    {
        if ($this->helper->isModuleEnabled()) {
            $movementSection = InventoryLogHelper::MOVEMENT_SECTION;
            $movementData = InventoryLogHelper::MOVEMENT_DATA;

            if (!$this->registry->registry($movementSection)) {
                $this->registry->register($movementSection, InventoryLogHelper::ORDER_CANCEL);
            }

            $item = $observer->getEvent()->getItem();
            if (!$this->registry->registry($movementData)) {
                $productId = $item->getProductId();
                $product = $this->productRepositoryInterface->getById($productId);
                $productType = $product->getTypeId();

                if ($item->getId() && $productType == ProductType::TYPE_SIMPLE) {
                    $data = [
                        InventoryLogHelper::ORDER_CANCEL => ['order_id' => $item->getOrder()->getIncrementId()]
                    ];
                    $this->registry->register($movementData, $data);
                }
            }
        }
        return [$observer];
    }
}
