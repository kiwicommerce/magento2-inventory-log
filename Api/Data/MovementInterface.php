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

interface MovementInterface
{
    const MOVEMENT_ID = 'movement_id';
    const QTY_MOVEMENT = 'qty_movement';
    const USER_ID = 'user_id';
    const STOCK_ITEM_ID = 'stock_item_id';
    const OLD_QTY = 'old_qty';
    const IS_ADMIN = 'is_admin';
    const ENTITY_ID = 'entity_id';
    const CURRENT_QTY = 'current_qty';
    const MESSAGE = 'message';
    const USERNAME = 'username';
    const CREATED_AT = 'created_at';
    const UKEY = 'ukey';
    const IP = 'ip';
    const IS_IN_STOCK = 'is_in_stock';
    const PRODUCT_ID = 'product_id';

    /**
     * Get movement_id
     * @return string|null
     */
    public function getMovementId();

    /**
     * Set movement_id
     * @param string $movementId
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setMovementId($movementId);

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param string $entityId
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setEntityId($entityId);

    /**
     * Get stock_item_id
     * @return string|null
     */
    public function getStockItemId();

    /**
     * Set stock_item_id
     * @param string $stockItemId
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setStockItemId($stockItemId);

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param string $productId
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setProductId($productId);

    /**
     * Get user_id
     * @return string|null
     */
    public function getUserId();

    /**
     * Set user_id
     * @param string $userId
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setUserId($userId);

    /**
     * Get username
     * @return string|null
     */
    public function getUsername();

    /**
     * Set username
     * @param string $username
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setUsername($username);

    /**
     * Get Is_admin
     * @return string|null
     */
    public function getIsAdmin();

    /**
     * Set Is_admin
     * @param string $isAdmin
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setIsAdmin($isAdmin);

    /**
     * Get current_qty
     * @return string|null
     */
    public function getCurrentQty();

    /**
     * Set current_qty
     * @param string $currentQty
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setCurrentQty($currentQty);

    /**
     * Get qty_movement
     * @return string|null
     */
    public function getQtyMovement();

    /**
     * Set qty_movement
     * @param string $qtyMovement
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setQtyMovement($qtyMovement);

    /**
     * Get old_qty
     * @return string|null
     */
    public function getOldQty();

    /**
     * Set old_qty
     * @param string $oldQty
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setOldQty($oldQty);

    /**
     * Get is_in_stock
     * @return string|null
     */
    public function getIsInStock();

    /**
     * Set is_in_stock
     * @param string $isInStock
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setIsInStock($isInStock);

    /**
     * Get message
     * @return string|null
     */
    public function getMessage();

    /**
     * Set message
     * @param string $message
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setMessage($message);

    /**
     * Get ukey
     * @return string|null
     */
    public function getUkey();

    /**
     * Set ukey
     * @param string $ukey
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setUkey($ukey);

    /**
     * Get ip
     * @return string|null
     */
    public function getIp();

    /**
     * Set ip
     * @param string $ip
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setIp($ip);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setCreatedAt($createdAt);
}
