<?php

namespace Database\Seeders;

use App\Models\Customer\CustomerGroup;
use App\Models\Promotion\Voucher;
use App\Models\Promotion\VoucherCode;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerGroups = CustomerGroup::where('status', true)->get();
        Voucher::factory(10)
            ->create()
            ->each(function ($voucher) use ($customerGroups) {
                // Add Customer Group
                $voucher->customer_groups()->attach($customerGroups->random());

                // Create Coupons
                $coupons = VoucherCode::factory(5)->create([
                    'starts_from' => $voucher->starts_from,
                    'ends_till' => $voucher->ends_till,
                    'coupon_usage_limit' => $voucher->coupon_usage_limit,
                    'usage_per_customer' => $voucher->usage_per_customer,
                    'voucher_id' => $voucher->id,
                ]);

            });
    }
}
