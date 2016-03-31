<?php

namespace DR\Gallery\Model\Gallery\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    const ENABLED = 1;
    const DISABLED = 0;

    protected $options = null;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                [
                    'label' => __('Enabled'),
                    'value' => static::ENABLED,
                ],
                [
                    'label' => __('Disabled'),
                    'value' => static::DISABLED,
                ]
            ];
        }

        return $this->options;
    }
}
