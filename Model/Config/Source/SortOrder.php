<?php

namespace ClawRock\ProductShipping\Model\Config\Source;

class SortOrder implements \Magento\Framework\Option\ArrayInterface
{
    const SORT_ASCENDING = 'asc';
    const SORT_DESCENDING = 'desc';
    const SORT_AS_IN_CONFIG = 'as_conf';

    /**
     * @var array
     */
    protected $options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                [
                    'value' => self::SORT_ASCENDING,
                    'label' => "Ascending"
                ],
                [
                    'value' => self::SORT_AS_IN_CONFIG,
                    'label' => "As in config"
                ],
                [
                    'value' => self::SORT_DESCENDING,
                    'label' => "Descending"
                ]
            ];
        }

        return $this->options;
    }
}
