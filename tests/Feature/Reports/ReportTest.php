<?php

namespace Tests\Feature\Reports;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTest extends TestCase
{
  protected $customer, $productCategory, $tax, $discount, $billing, $billing_detail, $billing_tax, $billing_discount;

  use DatabaseTransactions;

  public function setUp()
  {
    parent::setUp();

    $this->customer = factory(\App\Customer::class)->create([
      'company_id'  =>  $this->company->id
    ]);

    $this->productCategory = factory(\App\ProductCategory::class)->create([
      'company_id'  =>  $this->company->id,
      'name'  =>  'Silicoplast'
    ]);

    $this->tax = factory(\App\Tax::class)->create([
      'company_id'  =>  $this->company->id,
      'name'        =>  'GST',
      'tax_percent' =>  '18'
    ]);

    $this->discount = factory(\App\Discount::class)->create([
      'company_id'          =>  $this->company->id,
      'name'                =>  '5 percent',
      'discount_percent'    =>  '5'
    ]);

    $this->billing = factory(\App\Billing::class)->create([
      'company_id'  =>  $this->company->id,
      'customer_id' =>  $this->customer->id,
      'gst_including'       =>  true
    ]);

    $this->billing_detail = factory(\App\BillingDetail::class)->create([
      'billing_id'  =>  $this->billing->id,
      'product_category_id' =>  $this->productCategory->id,
      'cost_per_unit'       =>  '100',
      'amount'              =>  '500',
      'qty'                 =>  3,
    ]);

    $this->billing_tax = factory(\App\BillingTax::class)->create([
      'billing_id'  =>  $this->billing->id,
      'tax_id' =>  $this->tax->id,
      'amount' =>  '500' 
    ]);
    
    $this->billing_discount = factory(\App\BillingDiscount::class)->create([
      'billing_id'  =>  $this->billing->id,
      'discount_id' =>  $this->discount->id,
      'amount' =>  '500' 
    ]);
  }

  /** @test */
  function to_get_the_customer_ledger_report()
  {
    $this->json('get', '/api/customer-ledger?company_id=' . $this->company->id . '&customer_id=' . $this->customer->id);
  }
}
