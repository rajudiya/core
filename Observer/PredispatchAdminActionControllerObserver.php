<?php
/**
 * @author MagentoBrain Team
 * @copyright Copyright (c) 2022 MagentoBrain (https://www.magentobrain.com)
 * @package MagentoBrain_Core
 */


namespace MagentoBrain\Core\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * AdminNotification observer
 *
 */
class PredispatchAdminActionControllerObserver implements ObserverInterface
{
    /**
     * @var \Magento\AdminNotification\Model\FeedFactory
     */
    protected $_feedFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @param \MagentoBrain\Core\Model\FeedFactory $feedFactory
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     */
    public function __construct(
        \MagentoBrain\Core\Model\FeedFactory $feedFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession
    ) {
        $this->_feedFactory = $feedFactory;
        $this->_backendAuthSession = $backendAuthSession;
    }

    /**
     * Predispatch admin action controller
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_backendAuthSession->isLoggedIn()) {
            $feedModel = $this->_feedFactory->create();
            /* @var $feedModel \Magento\AdminNotification\Model\Feed */
            $feedModel->checkUpdate();
        }
    }
}
