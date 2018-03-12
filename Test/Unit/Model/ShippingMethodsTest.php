<?php

namespace ClawRock\ProductShipping\Test\Unit\Model;

use ClawRock\ProductShipping\Exception\RequiredOptionsException;
use ClawRock\ProductShipping\Helper\Config;
use ClawRock\ProductShipping\Model\ShippingMethods;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Registry;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Quote\Model\Quote\TotalsCollector;
use PHPUnit\Framework\TestCase;

class ShippingMethodsTest extends TestCase
{
    /**
     * @var \ClawRock\ProductShipping\Helper\Config
     */
    protected $configMock;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quoteMock;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactoryMock;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelperMock;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registryMock;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productMock;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactoryMock;

    /**
     * @var \Magento\Quote\Model\Quote\TotalsCollector
     */
    protected $totalsCollectorMock;

    /**
     * @var ShippingMethods
     */
    protected $model;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->quoteFactoryMock = $this->getMockBuilder(QuoteFactory::class)
                                       ->disableOriginalConstructor()
                                       ->setMethods(['create'])
                                       ->getMock();

        $this->configMock = $this->getMockBuilder(Config::class)
                                 ->disableOriginalConstructor()
                                 ->getMock();

        $this->priceHelperMock = $this->getMockBuilder(Data::class)
                                      ->disableOriginalConstructor()
                                      ->getMock();

        $this->registryMock = $this->getMockBuilder(Registry::class)
                                   ->disableOriginalConstructor()
                                   ->getMock();

        $this->productFactoryMock = $this->getMockBuilder(ProductFactory::class)
                                         ->disableOriginalConstructor()
                                         ->setMethods(['create'])
                                         ->getMock();

        $this->productMock = $this->getMockBuilder(Product::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $this->totalsCollectorMock = $this->getMockBuilder(TotalsCollector::class)
                                          ->disableOriginalConstructor()
                                          ->getMock();

        $this->shippingAddressMock = $this->getMockBuilder(Address::class)
                                          ->disableOriginalConstructor()
                                          ->setMethods([
                                              'getCountryId',
                                              'getGroupedAllShippingRates',
                                              'collectShippingRates',
                                              'requestShippingRates',
                                              'setShippingMethod',
                                              'getShippingRateByCode',
                                              'addData',
                                              'setCollectShippingRates',
                                          ])
                                          ->getMock();

        $this->model = $objectManager->getObject(ShippingMethods::class, [
            'quoteFactory' => $this->quoteFactoryMock,
            'config' => $this->configMock,
            'priceHelper' => $this->priceHelperMock,
            'registry' => $this->registryMock,
            'productFactory' => $this->productFactoryMock,
            'totalsCollector' => $this->totalsCollectorMock
        ]);
    }

    public function testGetCountryCode()
    {
        $this->configMock->expects($this->once())
                         ->method('getCountryCode')
                         ->willReturn('PL');
        $this->assertEquals('PL', $this->model->getCountryCode());
    }

    public function testGetProduct()
    {
        $this->productFactoryMock->expects($this->once())
                                 ->method('create')
                                 ->willReturn($this->productMock);

        $this->productMock->expects($this->once())
                          ->method('loadByAttribute')
                          ->willReturnSelf();

        $this->assertInstanceOf(
            Product::class,
            $this->model->getProduct('simple')
        );
    }

    public function testGetShippingMethods()
    {
        $this->productFactoryMock->expects($this->once())
                                 ->method('create')
                                 ->willReturn($this->productMock);

        $this->productMock->expects($this->once())
                          ->method('loadByAttribute')
                          ->willReturnSelf();

        $this->quoteMock = $this->getMockBuilder(Quote::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $this->quoteFactoryMock->expects($this->once())
                                 ->method('create')
                                 ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->exactly(2))
                        ->method('getShippingAddress')
                        ->willReturn($this->shippingAddressMock);

        $this->shippingAddressMock->expects($this->once())
                                  ->method('setCollectShippingRates')
                                  ->with(true)
                                  ->willReturnSelf();

        $this->totalsCollectorMock->expects($this->once())
                                  ->method('collectAddressTotals')
                                  ->with($this->quoteMock, $this->shippingAddressMock)
                                  ->willReturnSelf();

        $this->priceHelperMock->expects($this->once())
                              ->method('currency')
                              ->willReturn('$0.00');

        $rate = $this->getMockBuilder(Rate::class)
                     ->disableOriginalConstructor()
                     ->setMethods(['getMethodTitle', 'getPrice'])
                     ->getMock();

        $rate->expects($this->once())
             ->method('getMethodTitle')
             ->willReturn('Free Shipping');

        $rate->expects($this->once())
             ->method('getPrice')
             ->willReturn(0.00);

        $expectedRates = [['title' => 'Free Shipping', 'price' => '$0.00']];

        $this->shippingAddressMock->expects($this->once())
            ->method('getGroupedAllShippingRates')
            ->willReturn([[$rate]]);

        $carriersRates = $this->model->getShippingMethods(['sku' => 'simple', 'qty' => 1]);

        $this->assertEquals($expectedRates, $carriersRates);
    }

    public function testGetShippingMethodsInvalidRequest()
    {
        $this->expectException(RequiredOptionsException::class);

        $carriersRates = $this->model->getShippingMethods(['qty' => 1]);
    }
}
