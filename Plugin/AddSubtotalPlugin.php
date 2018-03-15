<?php

namespace ClawRock\ProductShipping\Plugin;

class AddSubtotalPlugin
{
    /**
     * @return void
     */
    public function aroundCollect(
        \Magento\Tax\Model\Sales\Total\Quote\Subtotal $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        $proceed($quote, $shippingAssignment, $total);
        if ($shippingAssignment->getShipping()->getAddress()->getBaseSubtotalTotalInclTax() === null) {
            $shippingAssignment->getShipping()
                               ->getAddress()
                               ->setBaseSubtotalTotalInclTax($total->getBaseSubtotalInclTax());
        }
    }
}
