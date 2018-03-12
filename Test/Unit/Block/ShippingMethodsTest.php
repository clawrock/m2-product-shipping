<?php

namespace ClawRock\ProductShipping\Test\Unit\Block;

use ClawRock\ProductShipping\Block\ShippingMethods;
use ClawRock\ProductShipping\Helper\Config;
use ClawRock\ProductShipping\Model\ShippingMethods as Shipp;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;

class ShippingMethodsTest extends TestCase
{
    /**
     * @var \ClawRock\ProductShipping\Helper\Config
     */
    protected $configMock;

    /**
     * @var \ClawRock\ProductShipping\Api\ShippingMethodsInterface
     */
    protected $shippingMethodsMock;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registryMock;

    /**
     * @var ShippingMethods
     */
    protected $block;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->configMock = $this->getMockBuilder(Config::class)
                                 ->disableOriginalConstructor()
                                 ->getMock();

        $this->shippingMethodsMock = $this->getMockBuilder(Shipp::class)
                                          ->disableOriginalConstructor()
                                          ->getMock();

        $this->registryMock = $this->getMockBuilder(Registry::class)
                                   ->disableOriginalConstructor()
                                   ->getMock();

        $this->productMock = $this->getMockBuilder(Product::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $this->urlInterfaceMock = $this->createMock(UrlInterface::class);

        $this->storeManager = $this->createMock(StoreManagerInterface::class);

        $this->contextMock = $this->getMockBuilder(Context::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $this->contextMock->expects($this->once())
                          ->method('getStoreManager')
                          ->willReturn($this->storeManager);

        $this->contextMock->expects($this->once())
                          ->method('getUrlBuilder')
                          ->willReturn($this->urlInterfaceMock);

        $this->block = $objectManager->getObject(ShippingMethods::class, [
            'context' => $this->contextMock,
            'shippingMethods' => $this->shippingMethodsMock,
            'config' => $this->configMock,
            'registry' => $this->registryMock
        ]);
    }

    public function testGetCustomMessageForSimple()
    {
        $this->registryMock->expects($this->once())
                           ->method('registry')
                           ->with('product')
                           ->willreturn($this->productMock);

        $this->productMock->expects($this->exactly(2))
                          ->method('getTypeInstance')
                          ->willReturnSelf();

        $this->productMock->expects($this->once())
                          ->method('getTypeId')
                          ->willReturn('simple');

        $this->configMock->expects($this->once())
                         ->method('getCustomMessage')
                         ->willReturn('Shipping methods not found.');

        $this->assertEquals('Shipping methods not found.', $this->block->getCustomMessage());
    }

    public function testGetCustomMessageForBundleAndConfigurable()
    {
         $this->registryMock->expects($this->once())
                           ->method('registry')
                           ->with('product')
                           ->willreturn($this->productMock);

        $this->productMock->expects($this->exactly(2))
                          ->method('getTypeInstance')
                          ->willReturnSelf();

        $this->productMock->expects($this->once())
                          ->method('getTypeId')
                          ->willReturn('bundle');

        $this->configMock->expects($this->once())
                         ->method('getOptionsCustomMessage')
                         ->willReturn('Please select options.');

        $this->assertEquals('Please select options.', $this->block->getCustomMessage());
    }

    /**
     * Method will return empty arrray when options are not provided, same for bundle
     */
    public function testGetShippingMethodsForConfigurableProduct()
    {
        $sku = 'configurable-sku';

        $this->shippingMethodsMock->expects($this->once())
                                  ->method('getShippingMethods')
                                  ->willreturn([]);
        $this->assertEquals([], $this->block->getShippingMethods($sku));
    }

    public function testGetShippingMtehodsForSimpleProduct()
    {
        $sku = 'simple-sku';

        $output = [
            ['title' => 'Fixed', 'price' => '$10.00'],
            ['title' => 'Table Rate', 'price' => '$20.00'],
        ];

        $this->shippingMethodsMock->expects($this->once())
                                  ->method('getShippingMethods')
                                  ->with($sku)
                                  ->willReturn($output);

        $this->assertEquals($output, $this->block->getShippingMethods($sku));
    }

    public function testGetProduct()
    {
        $this->productMock->expects($this->exactly(2))
                          ->method('getTypeInstance')
                          ->willReturnSelf();

        $this->registryMock->expects($this->once())
                           ->method('registry')
                           ->with('product')
                           ->willReturn($this->productMock);

        $this->assertInstanceOf(
            Product::class,
            $this->block->getProduct()
        );
    }

    public function testGetDefaultOptions()
    {
        $this->registryMock->expects($this->once())
                           ->method('registry')
                           ->with('product')
                           ->willreturn($this->productMock);

        $this->productMock->expects($this->exactly(2))
                          ->method('getTypeInstance')
                          ->willReturnSelf();

        $this->productMock->expects($this->once())
                          ->method('getSku')
                          ->willReturn('simple-sku');

        $this->assertEquals(['sku' => 'simple-sku', 'qty' => 1], $this->block->getDefaultOptions());
    }

    public function testGetShippingMethodsUrl()
    {
        $this->urlInterfaceMock->expects($this->once())->method('getUrl')
            ->with('rest/V1/product-shipping-methods')
            ->willReturn('http://magento.com/rest/V1/product-shipping-methods');
        $this->assertEquals('http://magento.com/rest/V1/product-shipping-methods', $this->block->getShippingMethodsUrl());
    }
}
