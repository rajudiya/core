<?php
/**
 * @author MagentoBrain Team
 * @copyright Copyright (c) 2022 MagentoBrain (https://www.magentobrain.com)
 * @package MagentoBrain_Core
 */


namespace MagentoBrain\Core\Components\Model\Source;

/**
 * doesn't contain "-- Please Select--" option
 */
class WebsitesOptionsMultiselect extends WebsitesOptions
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return $this->store->getWebsiteValuesForForm(true);
    }
}
