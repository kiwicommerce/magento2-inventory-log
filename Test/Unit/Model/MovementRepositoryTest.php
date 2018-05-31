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
namespace KiwiCommerce\InventoryLog\Test\Unit\Model;

use KiwiCommerce\InventoryLog\Model\MovementRepository;

/**
 * Test for KiwiCommerce\InventoryLog\Model\MovementRepository
 */
class MovementRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\KiwiCommerce\InventoryLog\Model\ResourceModel\Movement
     */
    public $movementResource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Api\DataObjectHelper
     */
    public $dataHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Reflection\DataObjectProcessor
     */
    public $dataObjectProcessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\KiwiCommerce\InventoryLog\Helper\Data
     */
    public $movementDataHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\KiwiCommerce\InventoryLog\Model\Movement
     */
    public $movement;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\KiwiCommerce\InventoryLog\Api\Data\MovementInterface
     */
    public $movementData;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\KiwiCommerce\InventoryLog\Model\ResourceModel\Movement\CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\KiwiCommerce\InventoryLog\Api\Data\MovementSearchResultsInterface
     */
    public $movementSearchResult;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\KiwiCommerce\InventoryLog\Model\ResourceModel\Movement\Collection
     */
    public $collection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    public $stockItemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\KiwiCommerce\InventoryLog\Model\MovementRepository
     */
    public $movementRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\KiwiCommerce\InventoryLog\Model\MovementFactory
     */
    public $movementFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\KiwiCommerce\InventoryLog\Model\MovementRepository
     */
    public $repository;

    /**
     * Initialize repository
     */
    protected function setUp()
    {
        $this->movementResource = $this->getMockBuilder('KiwiCommerce\InventoryLog\Model\ResourceModel\Movement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->movementFactory = $this->getMockBuilder('KiwiCommerce\InventoryLog\Model\MovementFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $movementDataFactory = $this->getMockBuilder('KiwiCommerce\InventoryLog\Api\Data\MovementInterfaceFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $collectionFactory = $this->getMockBuilder('KiwiCommerce\InventoryLog\Model\ResourceModel\Movement\CollectionFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $movementSearchResultsInterfaceFactory = $this->getMockBuilder('KiwiCommerce\InventoryLog\Api\Data\MovementSearchResultsInterfaceFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->dataHelper = $this->getMockBuilder('Magento\Framework\Api\DataObjectHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataObjectProcessor = $this->getMockBuilder('Magento\Framework\Reflection\DataObjectProcessor')
            ->disableOriginalConstructor()
            ->getMock();

        $this->storeManager = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->movementDataHelper = $this->getMockBuilder('KiwiCommerce\InventoryLog\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $store = $this->getMockBuilder('\Magento\Store\Api\Data\StoreInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $store->expects($this->any())->method('getId')->willReturn(0);
        $this->storeManager->expects($this->any())->method('getStore')->willReturn($store);

        $this->movement = $this->getMockBuilder('KiwiCommerce\InventoryLog\Model\Movement')
                            ->disableOriginalConstructor()
                            ->getMock();
        
        $this->movementData = $this->getMockBuilder('KiwiCommerce\InventoryLog\Api\Data\MovementInterface')
                                ->disableOriginalConstructor()
                                ->getMock();

        $this->stockItemMock = $this->getMockBuilder('Magento\CatalogInventory\Api\Data\StockItemInterface')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getItemId',
                    'setItemId',
                    'getProductId',
                    'setProductId',
                    'getStockId',
                    'setStockId',
                    'getQty',
                    'setQty',
                    'getIsInStock',
                    'setIsInStock',
                    'getIsQtyDecimal',
                    'setIsQtyDecimal',
                    'getShowDefaultNotificationMessage',
                    'getUseConfigMinQty',
                    'setUseConfigMinQty',
                    'getMinQty',
                    'setMinQty',
                    'getUseConfigMinSaleQty',
                    'setUseConfigMinSaleQty',
                    'getMinSaleQty',
                    'setMinSaleQty',
                    'getUseConfigMaxSaleQty',
                    'setUseConfigMaxSaleQty',
                    'getMaxSaleQty',
                    'setMaxSaleQty',
                    'getUseConfigBackorders',
                    'setUseConfigBackorders',
                    'getBackorders',
                    'setBackorders',
                    'getUseConfigNotifyStockQty',
                    'setUseConfigNotifyStockQty',
                    'getNotifyStockQty',
                    'setNotifyStockQty',
                    'getUseConfigQtyIncrements',
                    'setUseConfigQtyIncrements',
                    'getQtyIncrements',
                    'setQtyIncrements',
                    'getUseConfigEnableQtyInc',
                    'setUseConfigEnableQtyInc',
                    'getEnableQtyIncrements',
                    'setEnableQtyIncrements',
                    'getUseConfigManageStock',
                    'setUseConfigManageStock',
                    'getManageStock',
                    'setManageStock',
                    'getLowStockDate',
                    'setLowStockDate',
                    'getIsDecimalDivided',
                    'setIsDecimalDivided',
                    'getStockStatusChangedAuto',
                    'setStockStatusChangedAuto',
                    'getExtensionAttributes',
                    'setExtensionAttributes',
                    'getOldQty',
                    'setOldQty',
                    'getUkey',
                    'setUkey'
                ]
            )
            ->getMock();

        $this->movementSearchResult = $this->getMockBuilder('KiwiCommerce\InventoryLog\Api\Data\MovementSearchResultsInterface')
            ->getMock();

        $this->collection = $this->getMockBuilder('KiwiCommerce\InventoryLog\Model\ResourceModel\Movement\Collection')
            ->disableOriginalConstructor()
            ->setMethods(['addFieldToFilter', 'getSize', 'setCurPage', 'setPageSize', 'load', 'addOrder'])
            ->getMock();

        $this->movementFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->movement);

        $movementDataFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->movementData);

        $collectionFactory->expects($this->any())
        ->method('create')
        ->willReturn($this->collection);

        $movementSearchResultsInterfaceFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->movementSearchResult);

        $this->dataHelper = $this->getMockBuilder('Magento\Framework\Api\DataObjectHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->movementRepositoryMock = $this->getMockBuilder('KiwiCommerce\InventoryLog\Model\MovementRepository')
                                        ->disableOriginalConstructor()
                                        ->getMock();

        $this->repository = new MovementRepository(
            $this->movementResource,
            $this->movementFactory,
            $movementDataFactory,
            $collectionFactory,
            $movementSearchResultsInterfaceFactory,
            $this->dataHelper,
            $this->dataObjectProcessor,
            $this->storeManager,
            $this->movementDataHelper
        );
    }

    /**
     * @test
     */
    public function testSave()
    {
        $this->movementResource->expects($this->once())
            ->method('save')
            ->with($this->movement)
            ->willReturnSelf();
        $this->assertEquals($this->movement, $this->repository->save($this->movement));
    }

    /**
     * @test
     */
    public function testDeleteById()
    {
        $movementId = '123';

        $this->movement->expects($this->once())
            ->method('getId')
            ->willReturn(true);
        $this->movement->expects($this->once())
            ->method('load')
            ->with($movementId)
            ->willReturnSelf();
        $this->movementResource->expects($this->once())
            ->method('delete')
            ->with($this->movement)
            ->willReturnSelf();

        $this->assertTrue($this->repository->deleteById($movementId));
    }

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSaveException()
    {
        $this->movementResource->expects($this->once())
            ->method('save')
            ->with($this->movement)
            ->willThrowException(new \Exception());
        $this->repository->save($this->movement);
    }

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function testDeleteException()
    {
        $this->movementResource->expects($this->once())
            ->method('delete')
            ->with($this->movement)
            ->willThrowException(new \Exception());
        $this->repository->delete($this->movement);
    }

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetByIdException()
    {
        $movementId = '123';

        $this->movement->expects($this->once())
            ->method('getId')
            ->willReturn(false);
        $this->movement->expects($this->once())
            ->method('load')
            ->with($movementId)
            ->willReturnSelf();
        $this->repository->getById($movementId);
    }

    /**
     * @test
     */
    public function testInsertStockMovement()
    {
        $message = 'Mock Test';
        $isNew = 0;
        $this->movementRepositoryMock->expects($this->any())
            ->method('insertStockMovement')
            ->with($this->stockItemMock, $message, $isNew)
            ->willReturn($this->repository);

        //$this->assertEquals($this->repository, $this->repository->insertStockMovement($this->stockItemMock, $message, $isNew));
        $this->assertNull($this->repository->insertStockMovement($this->stockItemMock, $message, $isNew));
    }

    /**
     * @test
     */
    public function testInsertStockMovementWithData()
    {
        $itemId = '10';
        $productId = '15';
        $isInStock = 1;
        $uKey = 'AB454CD';
        $qty = 100;
        $oldQty = 10;
        $message = 'Mock Test';
        $isNew = 0;

        $this->movementDataHelper->expects($this->once())
            ->method('isModuleEnabled')
            ->willReturn(true);

        $this->stockItemMock->expects($this->once())->method('getOldQty')->willReturn($oldQty);
        $this->stockItemMock->expects($this->once())->method('getQty')->willReturn($qty);
        $this->stockItemMock->expects($this->once())->method('getItemId')->willReturn($itemId);
        $this->stockItemMock->expects($this->once())->method('getProductId')->willReturn($productId);
        $this->stockItemMock->expects($this->once())->method('getIsInStock')->willReturn($isInStock);
        $this->stockItemMock->expects($this->once())->method('getUkey')->willReturn($uKey);

        $this->assertEquals(
            $this->repository,
            $this->repository->insertStockMovement($this->stockItemMock, $message, $isNew)
        );
    }
}
