<?php

namespace App\Helpers\ProductHelper;

class PriceCalculator
{

    public static function calculatePrices(float $price, float $taxPercentage, bool $priceIncludesTax = true): array
    {
//        if ($taxType === null) {
//            return [
//                'base_price'   => round($price, 2),
//                'tax'          => 0.00,
//                'final_price'  => round($price, 2),
//            ];
//        }
//
//        // If the tax type is 'NONE', no tax is applied.
//        elseif ($taxType === EventTicketTaxCast::NONE) {
//            return [
//                'base_price'   => round($price, 2),
//                'tax'          => 0.00,
//                'final_price'  => round($price, 2),
//            ];
//        }
//
//        // Retrieve the tax percentage from the tax type.
//        $taxPercentage = $taxType->percentage();

        // Initialize variables.
        $basePrice = $price;
        $taxAmount = 0.00;
        $finalPrice = $price;

        if ($priceIncludesTax) {
            // If the entered price includes tax, extract the base price.
            $basePrice = $price / (1 + ($taxPercentage / 100));
            $taxAmount = $price - $basePrice;
            $finalPrice = $price; // The final price remains as entered since it includes tax.
        } else {
            // If the entered price excludes tax, calculate tax and final price.
            $taxAmount = $basePrice * ($taxPercentage / 100);
            $finalPrice = $basePrice + $taxAmount;
        }

        // Round the results to two decimal places for currency representation.
        return [
            'base_price'   => round($basePrice, 2),
            'tax'          => round($taxAmount, 2),
            'final_price'  => round($finalPrice, 2),
        ];
    }


}
