<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Model;

use Oro\Bundle\CheckoutBundle\Entity\Checkout;
use Oro\Bundle\CurrencyBundle\Rounding\RoundingServiceInterface;
use Oro\Bundle\PricingBundle\Model\ProductPriceScopeCriteriaRequestHandler;
use Oro\Bundle\PricingBundle\Provider\MatchingPriceProvider;
use Oro\Bundle\PricingBundle\SubtotalProcessor\Model\Subtotal;
use Oro\Bundle\PricingBundle\SubtotalProcessor\Provider\AbstractSubtotalProvider;

class OrderDetails
{

    /**
     * @var AbstractSubtotalProvider
     */
    protected $subtotalProvider;

    /**
     * @var MatchingPriceProvider
     */
    protected $matchingPriceProvider;

    /**
     * @var ProductPriceScopeCriteriaRequestHandler
     */
    protected $scopeCriteriaRequestHandler;

    /** @var RoundingServiceInterface */
    protected $rounding;

    /**
     * @param AbstractSubtotalProvider $subtotalProvider
     * @param RoundingServiceInterface $rounding
     * @param MatchingPriceProvider $matchingPriceProvider
     * @param ProductPriceScopeCriteriaRequestHandler $scopeCriteriaRequestHandler
     */
    public function __construct(
        AbstractSubtotalProvider $subtotalProvider,
        RoundingServiceInterface $rounding,
        MatchingPriceProvider $matchingPriceProvider,
        ProductPriceScopeCriteriaRequestHandler $scopeCriteriaRequestHandler
    ) {
        $this->subtotalProvider = $subtotalProvider;
        $this->rounding = $rounding;
        $this->matchingPriceProvider = $matchingPriceProvider;
        $this->scopeCriteriaRequestHandler = $scopeCriteriaRequestHandler;
    }

    /**
     * @param Checkout $checkout
     *
     * @return array
     */
    public function getOrderDetailsArray(Checkout $checkout)
    {
        $subtotals = $this->subtotalProvider->getSubtotals($checkout);
        $total = $this->subtotalProvider->getTotalForSubtotals($checkout, $subtotals);
        $totalCents = $this->rounding->round($this->toCents($total->getAmount()));
        $totalBeforeDiscount = $this->getTotalBeforeDiscount($totalCents, $subtotals);
        $lineItems = $checkout->getLineItems();
        $currency = $total->getCurrency();

        return [
            'amount_cents' => (string) $this->rounding->round($this->toCents($total->getAmount())),
            'amount_cents_before_discount' => (string) $totalBeforeDiscount,
            'currency' => $currency,
            'shipping_amount_cents' => (string) $this->getShippingAmount($subtotals),
            'checkout_items' => $this->getCheckoutItems($lineItems, $currency),
        ];
    }

    private function toCents($amount)
    {
        return $amount * 100;
    }

    private function getTotalBeforeDiscount($totalCents, $subtotals)
    {
        $discountSubtotals = $this->getSubtotalsByType($subtotals, 'discount');
        $totalBeforeDiscount = $totalCents;
        foreach ($discountSubtotals as $discountSubtotal) {
            $discountAmount = $this->toCents($discountSubtotal->getAmount());
            $totalBeforeDiscount += $discountAmount;
        }
        return $this->rounding->round($totalBeforeDiscount);
    }

    private function getShippingAmount($subtotals)
    {
        $shippingSubtotals = $this->getSubtotalsByType($subtotals, 'shipping_cost');
        $shippingSubtotalAmount = 0;
        foreach ($shippingSubtotals as $shippingSubtotal) {
            $shippingSubtotalAmount += $this->toCents($shippingSubtotal->getAmount());
        }
        return $this->rounding->round($shippingSubtotalAmount);
    }

    private function getCheckoutItems($lineItems, $currency)
    {
        $mappedLineItems = $this->getMappedLineItemsArray($lineItems, $currency);
        $matchingPrices = $this->getMatchedPrices($mappedLineItems);
        $checkoutItems = [];
        foreach ($lineItems as $i => $lineItem) {

            $product = $lineItem->getProduct();

            $name = $product->getDefaultName() ? $product->getDefaultName()->getString() : '';
            $description = $product->getDefaultShortDescription()->getString() ?? '';
            $sku = $product->getSku();
            $quantity = $lineItem->getQuantity();
            $item_amount_cents = $this->rounding->round(
                $this->toCents($this->getMatchingPrice($matchingPrices, $mappedLineItems[$i]))
            );
            $total_amount_cents = $this->rounding->round($item_amount_cents * $quantity);

            $checkoutItems[$i] = [
                'name' => $name,
                'description' => $description,
                'sku' => $sku,
                'quantity' => $quantity,
                'item_amount_cents' => (string) $item_amount_cents,
                'total_amount_cents' => (string) $total_amount_cents,
                'currency' => $currency,
            ];
        }
        return $checkoutItems;
    }

    private function getMatchingPrice($matchingPrices, $mappedLineItem)
    {
        return $matchingPrices[
            $mappedLineItem['product'] . '-' . $mappedLineItem['unit'] . '-' . $mappedLineItem['qty'] . '-' . $mappedLineItem['currency']
        ]['value'];
    }

    private function getMappedLineItemsArray($lineItems, $currency)
    {
        return $lineItems->map(
            function ($lineItem) use ($currency) {
                $product = $lineItem->getProduct();
                return [
                    'product' => $product ? $product->getId() : null,
                    'unit' => $lineItem->getProductUnit() ? $lineItem->getProductUnit()->getCode() : null,
                    'qty' => $lineItem->getQuantity(),
                    'currency' => $currency,
                ];
            }
        );
    }

    private function getMatchedPrices($mappedLineItems)
    {
        return $this->matchingPriceProvider->getMatchingPrices(
            $mappedLineItems->getValues(),
            $this->scopeCriteriaRequestHandler->getPriceScopeCriteria()
        );
    }

    /**
     * @param ArrayCollection $subtotals
     * @param string $type
     *
     * @return Subtotal
     */
    private function getSubtotalsByType($subtotals, $type)
    {
        return $subtotals->filter(function ($subtotal) use ($type) {
            return $subtotal->getType() == $type;
        });
    }
}
