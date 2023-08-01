<?php
/**
 * @author MagentoBrain Team
 * @copyright Copyright (c) 2022 MagentoBrain (https://www.magentobrain.com)
 * @package MagentoBrain_Core
 */


namespace MagentoBrain\Core\Plugin\Notifications;

/**
 * Class MagentoBrainNotificationLogoAdd
 * @package MagentoBrain\Core\Plugin\Notifications
 */
class MagentoBrainNotificationLogoAdd
{

    /**
     * @param \Magento\AdminNotification\Block\Grid\Renderer\Notice $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\DataObject $row
     * @return mixed|string
     */
    public function aroundRender(
        \Magento\AdminNotification\Block\Grid\Renderer\Notice $subject,
        \Closure $proceed,
        \Magento\Framework\DataObject $row
    ) {
        $result = $proceed($row);

        if ($row->getData(\MagentoBrain\Core\Api\ColumnInterface::MAGENTOBRAIN_NOTIFICATION_FIELD)) {
            return '<div class="magentobrain-grid-message"><div class="magentobrain-notif-logo"></div>' . $result . '</div>';
        } else {
            return $result;
        }
    }
}
