<?php
/**
 * @author MagentoBrain Team
 * @copyright Copyright (c) 2022 MagentoBrain (https://www.magentobrain.com)
 * @package MagentoBrain_Core
 */


namespace MagentoBrain\Core\Plugin\Notifications;

use Magento\AdminNotification\Block\ToolbarEntry as NativeToolbarEntry;

class MagentoBrainNotificationLogoAddInToolbar
{
    /**
     * @param NativeToolbarEntry $subject
     * @param $html
     * @return mixed
     */
    public function afterToHtml(
        NativeToolbarEntry $subject,
        $html
    ) {
        return $this->getReplacedLogoWithHtml($subject, $html);
    }

    /**
     * @param NativeToolbarEntry $subject
     * @return \Magento\AdminNotification\Model\ResourceModel\Inbox\Collection
     */
    private function getMagentoBrainNotificationsCollection(NativeToolbarEntry $subject)
    {
        return $subject->getLatestUnreadNotifications()
            ->clear()
            ->addFieldToFilter(\MagentoBrain\Core\Api\ColumnInterface::MAGENTOBRAIN_NOTIFICATION_FIELD, 1);
    }

    /**
     * @param NativeToolbarEntry $subject
     * @param $html
     * @return mixed
     */
    private function getReplacedLogoWithHtml(NativeToolbarEntry $subject, $html)
    {
        foreach ($this->getMagentoBrainNotificationsCollection($subject) as $item) {
            $search = 'data-notification-id="' . $item->getId() . '"';
            $html = str_replace($search, $search . ' data-aitcore-logo="1"', $html);
        }

        return $html;
    }
}
