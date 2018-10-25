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
namespace KiwiCommerce\InventoryLog\Test\Unit\Helper;

class DataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Event\ManagerInterface
     */
    public $eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\UrlInterface
     */
    public $urlBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\RequestInterface
     */
    public $httpRequestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Model\Session
     */
    public $customerSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Backend\Model\Auth\Session
     */
    public $adminSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Registry
     */
    public $registryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Authorization\PolicyInterface
     */
    public $policyMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Backend\Model\Auth\Session
     */
    public $authSessionMock;

    /**
     * @var \KiwiCommerce\InventoryLog\Helper\Data
     */
    public $object;

    /**
     * Initialize Data
     */
    protected function setUp()
    {
        $this->eventManagerMock = $this->getMockBuilder(\Magento\Framework\Event\ManagerInterface::class)
            ->getMockForAbstractClass();

        $this->urlBuilderMock = $this->getMockBuilder(\Magento\Framework\UrlInterface::class)
            ->getMockForAbstractClass();

        $this->httpRequestMock = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->getMockForAbstractClass();

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $context = $objectManager->getObject(
            \Magento\Framework\App\Helper\Context::class,
            [
                'eventManager' => $this->eventManagerMock,
                'urlBuilder' => $this->urlBuilderMock,
                'httpRequest' => $this->httpRequestMock,
            ]
        );

        $this->customerSessionMock = $this->getMockBuilder(\Magento\Customer\Model\Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->adminSessionMock = $this->getMockBuilder(\Magento\Backend\Model\Auth\Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->registryMock = $this->getMockBuilder(\Magento\Framework\Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->policyMock = $this->getMockBuilder(\Magento\Framework\Authorization\PolicyInterface::class)
            ->getMockForAbstractClass();

        $this->authSessionMock = $this->getMockBuilder(\Magento\Backend\Model\Auth\Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = $objectManager->getObject(
            \KiwiCommerce\InventoryLog\Helper\Data::class,
            [
                'context' => $context,
                'customerSession' => $this->customerSessionMock,
                'backendAuthSession' => $this->adminSessionMock,
                'registry' => $this->registryMock,
                'policyInterface' => $this->policyMock,
                'authSession' => $this->authSessionMock
            ]
        );
    }

    /**
     * @test
     */
    public function testGenerateRandomString()
    {
        $length = 5;
        $expectedResult = 'uL8XL';
        $this->assertNotEquals($expectedResult, $this->object->generateRandomString($length));
    }
}
