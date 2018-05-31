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

use KiwiCommerce\InventoryLog\Api\Data\MovementInterface;

class Movement extends \Magento\Framework\Model\AbstractModel implements MovementInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('KiwiCommerce\InventoryLog\Model\ResourceModel\Movement');
    }

    /**
     * Get movement_id
     * @return string
     */
    public function getMovementId()
    {
        return $this->getData(self::MOVEMENT_ID);
    }

    /**
     * Set movement_id
     * @param string $movementId
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setMovementId($movementId)
    {
        return $this->setData(self::MOVEMENT_ID, $movementId);
    }

    /**
     * Get entity_id
     * @return string
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set entity_id
     * @param string $entityId
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get stock_item_id
     * @return string
     */
    public function getStockItemId()
    {
        return $this->getData(self::STOCK_ITEM_ID);
    }

    /**
     * Set stock_item_id
     * @param string $stockItemId
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setStockItemId($stockItemId)
    {
        return $this->setData(self::STOCK_ITEM_ID, $stockItemId);
    }

    /**
     * Get product_id
     * @return string
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * Set product_id
     * @param string $productId
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Get user_id
     * @return string
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * Set user_id
     * @param string $userId
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * Get username
     * @return string
     */
    public function getUsername()
    {
        return $this->getData(self::USERNAME);
    }

    /**
     * Set username
     * @param string $username
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setUsername($username)
    {
        return $this->setData(self::USERNAME, $username);
    }

    /**
     * Get Is_admin
     * @return string
     */
    public function getIsAdmin()
    {
        return $this->getData(self::IS_ADMIN);
    }

    /**
     * Set Is_admin
     * @param string $isAdmin
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setIsAdmin($isAdmin)
    {
        return $this->setData(self::IS_ADMIN, $isAdmin);
    }

    /**
     * Get current_qty
     * @return string
     */
    public function getCurrentQty()
    {
        return $this->getData(self::CURRENT_QTY);
    }

    /**
     * Set current_qty
     * @param string $currentQty
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setCurrentQty($currentQty)
    {
        return $this->setData(self::CURRENT_QTY, $currentQty);
    }

    /**
     * Get qty_movement
     * @return string
     */
    public function getQtyMovement()
    {
        return $this->getData(self::QTY_MOVEMENT);
    }

    /**
     * Set qty_movement
     * @param string $qtyMovement
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setQtyMovement($qtyMovement)
    {
        return $this->setData(self::QTY_MOVEMENT, $qtyMovement);
    }

    /**
     * Get old_qty
     * @return string
     */
    public function getOldQty()
    {
        return $this->getData(self::OLD_QTY);
    }

    /**
     * Set old_qty
     * @param string $oldQty
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setOldQty($oldQty)
    {
        return $this->setData(self::OLD_QTY, $oldQty);
    }

    /**
     * Get is_in_stock
     * @return string
     */
    public function getIsInStock()
    {
        return $this->getData(self::IS_IN_STOCK);
    }

    /**
     * Set is_in_stock
     * @param string $isInStock
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setIsInStock($isInStock)
    {
        return $this->setData(self::IS_IN_STOCK, $isInStock);
    }

    /**
     * Get message
     * @return string
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * Set message
     * @param string $message
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * Get ukey
     * @return string
     */
    public function getUkey()
    {
        return $this->getData(self::UKEY);
    }

    /**
     * Set ukey
     * @param string $ukey
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setUkey($ukey)
    {
        return $this->setData(self::UKEY, $ukey);
    }

    /**
     * Get ip
     * @return string
     */
    public function getIp()
    {
        return $this->getData(self::IP);
    }

    /**
     * Set ip
     * @param string $ip
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setIp($ip)
    {
        return $this->setData(self::IP, $ip);
    }

    /**
     * Get created_at
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $createdAt
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
