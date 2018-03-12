<?php

namespace ClawRock\ProductShipping\Api;

/**
 * @api
 */
interface ShippingMethodsInterface
{
    /**
     * @param mixed $options
     * @return mixed[]
     */
    public function getShippingMethods($options);
}
