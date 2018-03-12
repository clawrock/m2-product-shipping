<?php

namespace ClawRock\ProductShipping\Block;

use Magento\Framework\View\Element\Template;

class ShippingMethods extends Template
{
    const OPTIONS_PRODUCT_TYPES = ['configurable', 'bundle'];

    /**
     * @var \ClawRock\ProductShipping\Api\ShippingMethodsInterface
     */
    protected $shippingMethods;

    /**
     * @var \ClawRock\ProductShipping\Helper\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\View\Element\Template\Context       $context
     * @param \ClawRock\ProductShipping\Api\ShippingMethodsInterface $shippingMethods
     * @param \ClawRock\ProductShipping\Helper\Config                $config
     * @param \Magento\Framework\Registry                            $registry
     * @param array                                                  $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ClawRock\ProductShipping\Api\ShippingMethodsInterface $shippingMethods,
        \ClawRock\ProductShipping\Helper\Config $config,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->shippingMethods = $shippingMethods;
        $this->config = $config;
        $this->registry = $registry;
    }

    protected function _construct()
    {
        parent::_construct();

        if (!$this->hasData('template')) {
            $this->setTemplate('shipping_methods.phtml');
        }
    }

    /**
     * @return /Magento/Catalog/Model/Product
     */
    public function getProduct()
    {
        $product = $this->registry->registry('product');
        if ($product && $product->getTypeInstance()->getStoreFilter($product) === null) {
            $product->getTypeInstance()->setStoreFilter($this->_storeManager->getStore(), $product);
        }
        return $product;
    }

    /**
     * @return string
     */
    public function getCustomMessage()
    {
        if (in_array($this->getProduct()->getTypeId(), self::OPTIONS_PRODUCT_TYPES)) {
            return $this->config->getOptionsCustomMessage();
        }
        return $this->config->getCustomMessage();
    }

    /**
     * @param  string $sku
     * @return mixed[]
     */
    public function getShippingMethods($sku)
    {
        return $this->shippingMethods->getShippingMethods($sku);
    }

    /**
     * @return string
     */
    public function getShippingMethodsUrl()
    {
        return $this->getUrl('rest/V1/product-shipping-methods');
    }

    /**
     * @return array
     */
    public function getDefaultOptions()
    {
        return [
            'sku' => $this->getProduct()->getSku(),
            'qty' => 1
        ];
    }
}
