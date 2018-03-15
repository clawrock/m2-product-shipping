<?php

namespace ClawRock\ProductShipping\Test\Unit\Model;

use ClawRock\ProductShipping\Model\Config\Source\SortOrder;
use PHPUnit\Framework\TestCase;

class ConfigSourceSortOrderTest extends TestCase
{
    /**
     * @var \ClawRock\ProductShipping\Model\Config\Source\SortOrder
     */
    protected $model;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->model = $objectManager->getObject(SortOrder::class);
    }

    public function testToOptionArray()
    {
        $this->assertInternalType('array', $this->model->toOptionArray());
    }
}
