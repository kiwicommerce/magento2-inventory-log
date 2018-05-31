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

namespace KiwiCommerce\InventoryLog\Helper;

use \Magento\Customer\Model\Session as CustomerSession;
use \Magento\Backend\Model\Auth\Session as AdminSession;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const RANDOM_STRING_LENGTH = 5;
    const STOCK_UPDATE = 1;
    const ORDER_CANCEL = 2;
    const CREDIT_MEMO = 3;
    const MOVEMENT_SECTION = "movement_section";
    const NEW_PRODUCT = "new_product";
    const MOVEMENT_DATA = "movement_data";
    const RESOURCE_ID = "KiwiCommerce_InventoryLog::config";
    const CONFIG_ENABLE_PATH = 'inventory_log/general/inventory_enabled';
    /**
     * @var string $randomString
     */
    public $randomString='';
    /**
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    public $backendAuthSession;
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    public $remoteAddress;
    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * @var \Magento\Framework\Authorization\PolicyInterface
     */
    public $policyInterface;

    /**
     * @var AdminSession
     */
    public $authSession;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param CustomerSession $customerSession
     * @param AdminSession $backendAuthSession
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Authorization\PolicyInterface $policyInterface
     * @param AdminSession $authSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        CustomerSession $customerSession,
        AdminSession $backendAuthSession,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Authorization\PolicyInterface $policyInterface,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->customerSession = $customerSession;
        $this->backendAuthSession = $backendAuthSession;
        $this->remoteAddress = $context->getRemoteAddress();
        $this->registry = $registry;
        $this->policyInterface = $policyInterface;
        $this->authSession = $authSession;
        parent::__construct($context);
    }

    /**
     * Whether a module is enabled in the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isModuleEnabled()
    {
        if ($this->_moduleManager->isEnabled('KiwiCommerce_InventoryLog')) {
            if ($this->isEnabled()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Whether a module output is permitted by the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isOutputEnabled()
    {
        if ($this->_moduleManager->isOutputEnabled('KiwiCommerce_InventoryLog')) {
            if ($this->isEnabled()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $resourceId
     * @param null $user
     * @return bool
     */
    public function isAllowed($user = null)
    {
        if (!$user) {
            $user = $this->authSession->getUser();
        }
        $role = $user->getRole();
        $permission = $this->policyInterface->isAllowed($role->getId(), self::RESOURCE_ID);
        if ($permission) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::CONFIG_ENABLE_PATH, $storeScope)) {
            return true;
        }
        return false;
    }

    /**
     * Generate random Unique String
     * @param integer $productId
     * @return string
     */
    public function getRandomUniqueString($productId = null)
    {
        $this->randomString=$this->generateRandomString();
        if (!empty($productId)) {
            $this->randomString.=$productId;
        }
        $this->randomString.=time();
        return $this->randomString;
    }

    /**
     * @param int $length
     * @return bool|string
     */
    public function generateRandomString($length = self::RANDOM_STRING_LENGTH)
    {
        return substr(
            str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 5)),
            0,
            $length
        );
    }

    /**
     * Check admin isLoggedIn
     * @return int
     */
    public function isAdminLoggedIn()
    {
        return (int) $this->backendAuthSession->isLoggedIn();
    }

    /**
     * get logged userId
     * @return int
     */
    public function getUserId()
    {
        $userId = null;
        if ($this->customerSession->isLoggedIn()) {
            $userId = $this->customerSession->getCustomerId();
        } elseif ($this->backendAuthSession->isLoggedIn()) {
            $userId = $this->backendAuthSession->getUser()->getId();
        }

        return $userId;
    }

    /**
     * Get logged username
     * @return mixed|string
     */
    public function getUsername()
    {
        $username = '-';
        if ($this->customerSession->isLoggedIn()) {
            $username = $this->customerSession->getCustomer()->getName();
        } elseif ($this->backendAuthSession->isLoggedIn()) {
            $username = $this->backendAuthSession->getUser()->getUsername();
        }

        return $username;
    }

    /**
     * get remote computer address
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this->remoteAddress->getRemoteAddress();
    }

    /**
     * Clear registry
     * @return null
     */
    public function unRegisterAllData()
    {
        $keys = [self::MOVEMENT_SECTION, self::MOVEMENT_DATA, self::NEW_PRODUCT];
        array_walk($keys, [$this->registry, 'unregister']);
    }

    /**
     * write logfile
     * @param null $logFileName
     * @return \Zend\Log\Logger
     */
    public function callLogObj($logFileName = null)
    {
        if (!$logFileName) {
            $logFileName = 'custom.log';
        }
        if (!$logFileName) {
            $logFileName = 'custom.log';
        }
        $logFileName = '/var/log/'.$logFileName;
        $writer = new \Zend\Log\Writer\Stream(BP . $logFileName);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('--Log Start--');
        return $logger;
    }
}
