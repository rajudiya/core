<?php
/**
 * @author MagentoBrain Team
 * @copyright Copyright (c) 2022 MagentoBrain (https://www.magentobrain.com)
 * @package MagentoBrain_Core
 */


namespace MagentoBrain\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const MAGENTOBRAIN_CORE_XML_PATH_NOTIFICATIONS = 'magentobrain_core/notifications/';
    const MAGENTOBRAIN_CORE_XML_PATH_EXTENSIONS = 'magentobrain_core/extensions/';
    const MAGENTOBRAIN_CORE_XML_PATH_MENU = 'magentobrain_core/menu/';

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->remoteAddress = $context->getRemoteAddress();
    }

    /**
     * @param $path
     * @param int $storeId
     * @return mixed
     */
    public function getModuleConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return string
     */
    public function getCurrentIp()
    {
        return $this->remoteAddress->getRemoteAddress();
    }

    /**
     * @return boolean
     */
    public function getNotificationsEnable()
    {
        return (bool)$this->getModuleConfig(self::MAGENTOBRAIN_CORE_XML_PATH_NOTIFICATIONS . 'notice_enable');
    }

    /**
     * @return array
     */
    public function getNotificationsType()
    {
        $data = $this->getModuleConfig(self::MAGENTOBRAIN_CORE_XML_PATH_NOTIFICATIONS . 'notice_type');

        return $data ? explode(',', $data) : [];
    }

    /**
     * @return mixed
     */
    public function getNotificationsFrequency()
    {
        return $this->getModuleConfig(self::MAGENTOBRAIN_CORE_XML_PATH_NOTIFICATIONS . 'frequency');
    }

    /**
     * @return mixed
     */
    public function getMenuEnable()
    {
        return $this->getModuleConfig(self::MAGENTOBRAIN_CORE_XML_PATH_MENU . 'menu_enable');
    }
}
