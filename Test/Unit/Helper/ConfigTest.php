<?php

namespace ClawRock\ProductShipping\Test\Unit\Helper;

use ClawRock\ProductShipping\Helper\Config;
use ClawRock\ProductShipping\Model\Config\Source\SortOrder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var \ClawRock\ProductShipping\Helper\Config
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigMock;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
                                      ->getMockForAbstractClass();

        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->expects($this->any())
            ->method('getScopeConfig')
            ->willReturn($this->scopeConfigMock);

        $this->helper = $objectManager->getObject(
            Config::class,
            [
                'context' => $contextMock,
            ]
        );
    }

    public function testIsEnabled()
    {
        $this->scopeConfigMock->method('getValue')
                              ->with(Config::CONFIG_ENABLED)
                              ->willReturn(1);
        $this->assertEquals(1, $this->helper->isEnabled());
    }

    public function testGetCustomMessage()
    {
        $this->scopeConfigMock->method('getValue')
                              ->with(Config::CONFIG_CUSTOM_MESSAGE)
                              ->willReturn('Shipping methods not found.');
        $this->assertEquals('Shipping methods not found.', $this->helper->getCustomMessage());
    }

    public function testGetOptionsCustomMessage()
    {
        $this->scopeConfigMock->method('getValue')
                              ->with(Config::CONFIG_OPTIONS_CUSTOM_MESSAGE)
                              ->willReturn('Please select options.');
        $this->assertEquals('Please select options.', $this->helper->getOptionsCustomMessage());
    }

    public function testGetCountryCode()
    {
        $this->scopeConfigMock->method('getValue')
                              ->with(Config::CONFIG_COUNTRY_CODE)
                              ->willReturn('PL');
        $this->assertEquals('PL', $this->helper->getCountryCode());
    }

    public function testGetShippingMethodsSortOrder()
    {
        $this->scopeConfigMock->method('getValue')
                              ->with(Config::CONFIG_METHODS_SORT_ORDER)
                              ->willReturn(SortOrder::SORT_ASCENDING);
        $this->assertEquals(
            SortOrder::SORT_ASCENDING,
            $this->helper->getShippingMethodsSortOrder()
        );
    }

    public function testGetPostcode()
    {
        $this->scopeConfigMock->method('getValue')
                              ->with(Config::CONFIG_POSTCODE)
                              ->willReturn('9403');
        $this->assertEquals('9403', $this->helper->getPostcode());
    }
}
