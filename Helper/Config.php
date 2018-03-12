<?php

namespace ClawRock\ProductShipping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const CONFIG_ENABLED      = 'clawrock_productshipping/general/enabled';
    const CONFIG_COUNTRY_CODE = 'clawrock_productshipping/general/country_code';
    const CONFIG_CUSTOM_MESSAGE = 'clawrock_productshipping/general/custom_message';
    const CONFIG_OPTIONS_CUSTOM_MESSAGE = 'clawrock_productshipping/general/options_custom_message';

    /**
     * @param  null|string  $store
     * @return boolean
     */
    public function isEnabled($store = null)
    {
        return (bool) $this->scopeConfig->getValue(self::CONFIG_ENABLED, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param  null|string $store
     * @return string
     */
    public function getCountryCode($store = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_COUNTRY_CODE, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param  null|stirng $store
     * @return string
     */
    public function getCustomMessage($store = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_CUSTOM_MESSAGE, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param  null|stirng $store
     * @return string
     */
    public function getOptionsCustomMessage($store = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_OPTIONS_CUSTOM_MESSAGE, ScopeInterface::SCOPE_STORE, $store);
    }
}
