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
namespace KiwiCommerce\InventoryLog\Test\Unit\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use KiwiCommerce\InventoryLog\Model\ResourceModel\Movement as MovementResourceModel;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\App\ResourceConnection;

class MovementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Model\ResourceModel\Db\Context
     */
    public $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\KiwiCommerce\InventoryLog\Helper\Data
     */
    public $movementDataHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\KiwiCommerce\InventoryLog\Model\ResourceModel\Movement
     */
    public $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\EntityManager\EntityManager
     */
    public $entityManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\ResourceConnection
     */
    public $resourcesMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\KiwiCommerce\InventoryLog\Model\ResourceModel\Movement
     */
    public $movementResourceMock;

    /**
     * Initialize Resource Model
     */
    protected function setUp()
    {
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->movementDataHelper = $this->getMockBuilder('KiwiCommerce\InventoryLog\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->movementResourceMock = $this->getMockBuilder(MovementResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resourcesMock = $this->getMockBuilder(ResourceConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contextMock->expects($this->once())
            ->method('getResources')
            ->willReturn($this->resourcesMock);

        $this->model = (new ObjectManager($this))->getObject(MovementResourceModel::class, [
            'context' => $this->contextMock,
            'helper' => $this->movementDataHelper
        ]);
    }

    /**
     * @test
     */
    public function testGetStockQty()
    {
        $stockId = '123';
        $this->movementResourceMock->expects($this->any())
            ->method('getStockQty')
            ->with($stockId)
            ->willReturn($stockId);
    }

    /**
     * @test
     */
    public function testGetStockItemByProduct()
    {
        $productId = '123';
        $this->movementResourceMock->expects($this->any())
            ->method('getStockItemByProduct')
            ->with($productId)
            ->willReturn($productId);
    }
}
