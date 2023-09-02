<?php

namespace App\Helpers\Cart\Services\Voucher;

use App\Helpers\Cart\Contracts\CartServiceContract;
use App\Models\Promotion\Voucher;

class VoucherCartService
{


    private Voucher $voucher;
    private array $conditions = [];
    private CartServiceContract $cartService;

    public function __construct(Voucher $voucher,CartServiceContract $cartService)
    {
        $this->voucher = $voucher;
        $this->conditions = $this->voucher->conditions;
        $this->cartService = $cartService;
    }

    public function validate():bool
    {
        if (empty($this->conditions)) {
            return true;
        }
        $validConditionCount = 0;


        foreach ($this->conditions as $condition)
        {
            if (!empty($this->cartService->getErrors()))
            {
                // immediate return if error found
                return false;
            }

            if ($this->voucher->condition_type == Voucher::MATCH_ALL)
            {
                if (!$this->checkCondition($condition))
                {
                    // Return false if Single Condition Failed
                    return false;
                }
                $validConditionCount++;
            }


            if ($this->voucher->condition_type == Voucher::MATCH_ANY)
            {
                if ($this->checkCondition($condition))
                {
                    $validConditionCount++;
                    return true;
                }
                return false;
            }
        }


        // Validate Valid Condition Count
        if ($this->voucher->condition_type == Voucher::MATCH_ALL) {
            return $validConditionCount == count($this->conditions);
        } else {
            return $validConditionCount > 0;
        }


    }




    public function checkCondition(array $condition):bool
    {
        return true;
    }






}
