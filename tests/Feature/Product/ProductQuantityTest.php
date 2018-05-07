<?php

namespace Tests\Feature\Product;

use App\Billing;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductQuantityTest extends TestCase
{
  use DatabaseTransactions;

  protected $productCategory, $product, $customer, $tax, $discount, $billing, $billing_detail, $billing_tax, $billing_discount;

  public function setUp()
  {
    parent::setUp();

    $this->productCategory = factory(\App\ProductCategory::class)->create([
      'company_id'  =>  $this->company->id,
      'name'  =>  'Silicoplast',
      'quantity_left' =>  50
    ]);

    $this->product = factory(\App\Product::class)->create([
      'company_id'  =>  $this->company->id,
      'product_category_id' =>  $this->productCategory->id,
      'qty'   =>  50
    ]);

    $this->customer = factory(\App\Customer::class)->create([
      'company_id'  =>  $this->company->id
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
      'qty'                 =>  50,
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
  function update_the_product_quantity()
  {
    $this->productCategory->updateQuantity($this->product->qty);
    $this->productCategory->updateQuantity($this->product->qty);

    $this->assertEquals(150, $this->productCategory->quantity_left);
  }

  /** @test */
  function product_category_quantity_updated_when_any_new_product_is_added()
  {
    $payload = [
      'product_category_id' =>  $this->productCategory->id,
      'qty'                 =>  '5'
    ];

    $this->json('post', '/api/products', $payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'  =>  [
          'product_category_id' =>  $this->productCategory->id,
          'qty'                 =>  '5'
        ]
      ]);

    $this->productCategory->refresh();

    $this->assertEquals(55, $this->productCategory->quantity_left);
  }

  /** @test */
  function remove_the_product_quantity()
  {
    $this->productCategory->updateQuantity($this->product->qty);
    $this->productCategory->removeQuantity($this->product->qty);

    $this->assertEquals(50, $this->productCategory->quantity_left);
  }

  /** @test */
  function product_category_quantity_updated_when_any_product_is_updated()
  {
    $this->product->qty = 100;

    $this->json('patch', '/api/products/' . $this->product->id, $this->product->toArray(), $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'product_category_id' =>  $this->productCategory->id,
          'qty'                 =>  100
        ]
      ]);

    $this->productCategory->refresh();

    $this->assertEquals(100, $this->productCategory->quantity_left);
  }

  /** @test */
  function remove_product_when_the_product_is_sold()
  {
    $this->disableEH();

    $payload = [
      'customer_id' =>  $this->customer->id,
      'bill_no'     =>  '1',
      'gst_including'       =>  true,
      'billing_details' =>  [
        0 =>  [
          'product_category_id' =>  $this->productCategory->id,
          'cost_per_unit'       =>  '200',
          'amount'              =>  '500',
          'qty'                 =>  3,
        ]
      ],
      'billing_taxes' =>  [
        0 =>  [
          'tax_id' =>  $this->tax->id,
          'amount' =>  '500' 
        ]
      ],
      'billing_discounts' =>  [
        0 =>  [
          'discount_id' =>  $this->discount->id,
          'amount' =>  '500' 
        ]
      ]
    ];

    $this->json('post', '/api/billings', $payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'  =>  [
          'customer_id' =>  $this->customer->id,
        ]
      ]);

    $billing = Billing::where('id', '=', 2)->first();

    $this->assertCount(1, $billing->billing_details);
    $this->assertCount(1, $billing->billing_taxes);
    $this->assertCount(1, $billing->billing_discounts);

    $this->productCategory->refresh();

    $this->assertEquals(47, $this->productCategory->quantity_left);
  }

  /** @test */
  function remove_product_when_the_product_sold_is_updated()
  {
    $this->productCategory->quantity_left = 0;
    $this->productCategory->save();

     $payload = [
      'id'          =>  $this->billing->id,
      'customer_id' =>  $this->customer->id,
      'billing_details' =>  [
        0 =>  [
          'id'                  =>  $this->billing_detail->id,
          'product_category_id' =>  $this->productCategory->id,
          'amount'              =>  '5000',
          'qty'                 =>  4
        ]
      ],
      'billing_taxes' =>  [
        0 =>  [
          'id'      =>  $this->billing_tax->id,
          'tax_id' =>  $this->tax->id,
          'amount' =>  '500' 
        ],
        1 =>  [ 
          'tax_id' =>  $this->tax->id,
          'amount' =>  '500' 
        ]
      ],
      'billing_discounts' =>  [
        0 =>  [
          'id'          =>  $this->billing_discount->id,
          'discount_id' =>  $this->discount->id,
          'amount' =>  '500' 
        ]
      ]
    ];

    $this->json('patch', '/api/billings/' . $this->billing->id, $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'customer_id' =>  $this->customer->id,
          'billing_details' =>  [
            0 =>  [
              'product_category_id' =>  $this->productCategory->id,
              'amount'              =>  '5000',
              'qty'                 =>  4
            ]
          ],
          'billing_taxes' =>  [
            0 =>  [
              'tax_id' =>  $this->tax->id,
              'amount' =>  '500' 
            ],
            1 =>  [ 
              'tax_id' =>  $this->tax->id,
              'amount' =>  '500' 
            ]
          ],
          'billing_discounts' =>  [
            0 =>  [
              'discount_id' =>  $this->discount->id,
              'amount' =>  '500' 
            ]
          ]
        ]
      ]); 

    $this->productCategory->refresh();
    $this->assertEquals(46, $this->productCategory->quantity_left);

  }

  /** @test */
  function refresh_the_product_quantity()
  {
    $this->productCategory->refreshQuantity();
    $this->productCategory->refresh();

    $this->assertEquals(0, $this->productCategory->quantity_left);

  }

  /** @test */
  function update_the_product_quantity_after_refreshing()
  {
    $this->json('get', '/api/product-categories/' . $this->productCategory->id. '/refresh-quantity', [], $this->headers)
      ->assertStatus(200);

    $this->productCategory->refresh();

    $this->assertEquals(0, $this->productCategory->quantity_left);
  }
}
