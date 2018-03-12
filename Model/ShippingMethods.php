<?php

namespace ClawRock\ProductShipping\Model;

use ClawRock\ProductShipping\Api\ShippingMethodsInterface;
use ClawRock\ProductShipping\Exception\RequiredOptionsException;
use Magento\Framework\DataObject;
use Magento\Framework\Phrase;

class ShippingMethods implements ShippingMethodsInterface
{
     /**
     * @var \ClawRock\ProductShipping\Helper\Config
     */
    protected $config;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Quote\Model\Quote\TotalsCollector
     */
    protected $totalsCollector;

    /**
     * @param \ClawRock\ProductShipping\Helper\Config    $config
     * @param \Magento\Quote\Model\QuoteFactory          $quoteFactory
     * @param \Magento\Framework\Pricing\Helper\Data     $priceHelper
     * @param \Magento\Framework\Registry                $registry
     * @param \Magento\Catalog\Model\ProductFactory      $productFactory
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     */
    public function __construct(
        \ClawRock\ProductShipping\Helper\Config $config,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->config = $config;
        $this->priceHelper = $priceHelper;
        $this->registry = $registry;
        $this->productFactory = $productFactory;
        $this->totalsCollector = $totalsCollector;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->config->getCountryCode();
    }

    /**
     * @param  string $sku
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct($sku)
    {
        if (!$this->product) {
            $this->product = $this->productFactory->create()->loadByAttribute('sku', $sku);
        }
        return $this->product;
    }

    /**
     *
     * @api
     * @param  mixed $options
     * @return mixed[]
     * @throws \ClawRock\ProductShipping\Exception\RequiredOptionsExtension
     */
    public function getShippingMethods($options)
    {
        $output = [];

        $options = is_array($options) ? $options : json_decode($options, true);
        $this->validateRequest($options);
        $this->getProduct($options['sku']);

        $params = new DataObject($options);

        /** @var \Magento\Quote\Model\Quote */
        $quote = $this->quoteFactory->create();
        $quote->setCurrency();

        $quote->addProduct($this->product, $params);
        $quote->getShippingAddress()->setCountryId($this->getCountryCode());

        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true);

        $this->totalsCollector->collectAddressTotals($quote, $shippingAddress);
        $shippingRates = $shippingAddress->getGroupedAllShippingRates();
        foreach ($shippingRates as $carrierRates) {
            foreach ($carrierRates as $rate) {
                /** @var \Magento\Quote\Model\Quote\Address\Rate $rate */
                $output[] = [
                    'title' => $rate->getMethodTitle(),
                    'price' => $this->priceHelper->currency($rate->getPrice(), true, false)
                ];
            }
        }
        asort($output);
        return $output;
    }

    /**
     * @param  array $data
     * @return bool
     * @throws \ClawRock\ProductShipping\Exception\RequiredOptionsExtension
     */
    protected function validateRequest($data)
    {
        if (!isset($data['sku']) || !isset($data['qty'])) {
            throw new RequiredOptionsException(new Phrase('Sku or qty is missing.'));
        }
        return true;
    }
}
