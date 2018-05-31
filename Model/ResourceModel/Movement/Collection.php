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

namespace KiwiCommerce\InventoryLog\Model\ResourceModel\Movement;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'movement_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'KiwiCommerce\InventoryLog\Model\Movement',
            'KiwiCommerce\InventoryLog\Model\ResourceModel\Movement'
        );
    }

    /**
     * Add field filter to collection
     *
     * @param array|string $field
     * @param string|int|array|null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        return parent::addFieldToFilter($field, $condition);
    }
}
