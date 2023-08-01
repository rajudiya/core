<?php
/**
 * @author MagentoBrain Team
 * @copyright Copyright (c) 2022 MagentoBrain (https://www.magentobrain.com)
 * @package MagentoBrain_Core
 */


namespace MagentoBrain\Core\Model\Config\Source;

class Frequency implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => 1,
                'label' => __('1 Day')
            ],
            [
                'value' => 2,
                'label' => __('2 Days')
            ],
            [
                'value' => 3,
                'label' => __('3 Days')
            ],
            [
                'value' => 4,
                'label' => __('4 Days')
            ],
            [
                'value' => 5,
                'label' => __('5 Days')
            ],
            [
                'value' => 6,
                'label' => __('6 Days')
            ],
            [
                'value' => 7,
                'label' => __('7 Days')
            ],
            [
                'value' => 8,
                'label' => __('8 Days')
            ],
            [
                'value' => 9,
                'label' => __('9 Days')
            ],
            [
                'value' => 10,
                'label' => __('10 Days')
            ]
        ];

        return $options;
    }
}
