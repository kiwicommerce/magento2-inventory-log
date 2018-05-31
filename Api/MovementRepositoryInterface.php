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

namespace KiwiCommerce\InventoryLog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface MovementRepositoryInterface
{
    /**
     * Save movement
     * @param \KiwiCommerce\InventoryLog\Api\Data\MovementInterface $movement
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \KiwiCommerce\InventoryLog\Api\Data\MovementInterface $movement
    );

    /**
     * Retrieve movement
     * @param string $movementId
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($movementId);

    /**
     * Retrieve movement matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \KiwiCommerce\InventoryLog\Api\Data\MovementSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete movement
     * @param \KiwiCommerce\InventoryLog\Api\Data\MovementInterface $movement
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \KiwiCommerce\InventoryLog\Api\Data\MovementInterface $movement
    );

    /**
     * Delete movement by ID
     * @param string $movementId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($movementId);
}
