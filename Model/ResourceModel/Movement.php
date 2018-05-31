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

namespace KiwiCommerce\InventoryLog\Model\ResourceModel;

use \KiwiCommerce\InventoryLog\Helper\Data as InventoryLogHelper;

class Movement extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var InventoryLogHelper
     */
    private $helper;

    /**
     * Movement constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param InventoryLogHelper $helper
     * @param null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        InventoryLogHelper $helper,
        $resourcePrefix = null
    ) {
        $this->helper=$helper;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('kiwicommerce_stock_movement', 'movement_id');
    }

    /**
     * Get Stock current qty
     * @param integer $itemId
     * @return string
     */
    public function getStockQty($itemId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
            $this->getTable($this->getTable('cataloginventory_stock_item')),
            'qty'
        )->where(
            'item_id = ?',
            (int)$itemId
        );
        return $adapter->fetchOne($select);
    }

    /**
     * get product inventory data using product id
     * @param $productId
     * @return string
     */
    public function getStockItemByProduct($productId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
            $this->getTable($this->getTable('cataloginventory_stock_item')),
            'qty'
        )->where(
            'product_id = ?',
            (int)$productId
        );
        return $adapter->fetchOne($select);
    }

    /**
     * Revert product qty while exception occur during process
     * @param array $items
     * @param $websiteId
     * @param $operator
     */
    public function correctItemsQty(array $items, $websiteId, $operator)
    {
        if (empty($items)) {
            return;
        }

        $connection = $this->getConnection();
        $conditions = [];
        $uKeyConditions = [];

        foreach ($items as $productId => $qty) {
            $case = $connection->quoteInto('?', $productId);
            $result = $connection->quoteInto("qty{$operator}?", $qty);
            $conditions[$case] = $result;
            $uKey=$this->helper->getRandomUniqueString($productId);
            $uKeyConditions[$case]=$connection->quoteInto("?", (string)$uKey);
        }

        $value = $connection->getCaseSql('product_id', $conditions, 'qty');
        $uKeyValue = $connection->getCaseSql('product_id', $uKeyConditions);
        $where = ['product_id IN (?)' => array_keys($items), 'website_id = ?' => $websiteId];
        $connection->beginTransaction();
        $tableName = $this->getTable('cataloginventory_stock_item');
        $connection->update($tableName, ['qty' => $value,'ukey'=>$uKeyValue], $where);
        $connection->commit();
    }
}
