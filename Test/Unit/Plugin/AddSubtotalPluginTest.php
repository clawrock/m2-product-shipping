<?php

namespace ClawRock\ProductShipping\Test\Unit\Plugin;

use PHPUnit\Framework\TestCase;

class AddSubtotalPluginTest extends TestCase
{
    /**
     * @var \ClawRock\ProductShipping\Plugin\AddSubtotalPlugin
     */
    protected $plugin;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var \Magento\Quote\Model\ShippingAssignment
     */
    protected $shippingAssignment;

    /**
     * @var \Magento\Quote\Model\Quote\Address\Total
     */
    protected $total;

    /**
     * @var \Magento\Quote\Model\Quote\Address
     */
    protected $address;

    /**
     * @var \Magento\Quote\Model\Shipping
     */
    protected $shipping;

    /**
     * @var \Closure
     */
    protected $proceed;

    /**
     * @var \Magento\Tax\Model\Sales\Total\Quote\Subtotal
     */
    protected $subject;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->plugin = $objectManager->getObject(\ClawRock\ProductShipping\Plugin\AddSubtotalPlugin::class);

        $this->quote = $this->createMock(\Magento\Quote\Model\Quote::class);

        $this->shippingAssignment = $this->getMockBuilder(\Magento\Quote\Model\ShippingAssignment::class)
                                         ->setMethods(['getShipping'])
                                         ->disableOriginalConstructor()
                                         ->getMock();

        $this->total = $this->getMockBuilder(\Magento\Quote\Model\Quote\Address\Total::class)
                            ->setMethods(['getBaseSubtotalInclTax'])
                            ->disableOriginalConstructor()
                            ->getMock();

        $this->address = $this->createPartialMock(\Magento\Quote\Model\Quote\Address::class, []);

        $this->shipping = $this->getMockBuilder(\Magento\Quote\Shipping::class)
                               ->setMethods(['getShipping', 'getAddress'])
                               ->disableOriginalConstructor()
                               ->getMock();

        $this->proceed = function () {
            return $this->subject;
        };

        $this->subject = $this->getMockBuilder(\Magento\Tax\Model\Sales\Total\Quote\Subtotal::class)
                              ->setMethods(
                                  [
                                      'collect'
                                  ]
                              )->disableOriginalConstructor()
                              ->getMock();
    }

    public function testAroundCollectAssignedBaseSubtotal()
    {
        $baseSubtotal = 4.00;

        $this->total->expects($this->once())->method('getBaseSubtotalInclTax')->willReturn($baseSubtotal);

        $this->shippingAssignment->expects($this->exactly(2))->method('getShipping')->willReturn($this->shipping);
        $this->shipping->expects($this->exactly(2))->method('getAddress')->willReturn($this->address);

        $this->plugin->aroundCollect(
            $this->subject,
            $this->proceed,
            $this->quote,
            $this->shippingAssignment,
            $this->total
        );

        $this->assertEquals($baseSubtotal, $this->address->getBaseSubtotalTotalInclTax());
    }
}
