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

namespace KiwiCommerce\InventoryLog\Api\Data;

interface MovementSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get movement list.
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface[]
     */
    public function getItems();

    /**
     * Set entity_id list.
     * @param \KiwiCommerce\InventoryLog\Api\Data\MovementInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
