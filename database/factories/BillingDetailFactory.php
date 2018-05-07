<?php

use App\BillingDetail;
use Faker\Generator as Faker;

$factory->define(BillingDetail::class, function (Faker $faker) {
  return [
    'billing_id'  =>  '1',
    'product_category_id' =>  '1',
    'cost_per_unit'       =>  '500',
    'amount'              =>  '500',
    'qty'                 =>  3,
  ];
});
