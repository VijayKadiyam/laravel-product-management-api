<?php

namespace Tests\Feature\Billing;

use App\Billing;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BillingTest extends TestCase
{
  use DatabaseTransactions;

  protected $customer, $productCategory, $tax, $discount, $billing, $billing_detail, $billing_tax, $billing_discount;

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
  function user_must_be_logged_in()
  {
    $this->json('post', '/api/billings')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_customerId_and_subTotal()
  {
    $this->json('post', '/api/billings', [], $this->headers)
      ->assertStatus(422); 
  }

  /** @test */
  function it_requires_productCategoryId_Amount_Qty_CostPerUnit_gstIncluding()
  {
    $payload = [
      'customer_id'     =>  '1',
      'billing_details' =>  [
        0 =>  [
          'product_category_id' =>  $this->productCategory->id,
          'amount'              =>  '500' 
        ]
      ]
    ];

    $this->json('post', '/api/billings', $payload, $this->headers)
      ->assertStatus(422); 
  }

  /** @test */
  function it_requires_tax_id_and_amount()
  {
    $payload = [
      'customer_id'     =>  '1',
      'billing_details' =>  [
        0 =>  [
          'product_category_id' =>  $this->productCategory->id,
          'amount'              =>  '500' 
        ]
      ],
      'billing_taxes' =>  [
        0 =>  [
          'tax_id'  =>  $this->tax->id
        ]
      ]
    ];

    $this->json('post', '/api/billings', $payload, $this->headers)
      ->assertStatus(422); 
  }

  /** @test */
  function it_requires_discount_id_and_amount()
  {
    $payload = [
      'customer_id'     =>  '1',
      'billing_details' =>  [
        0 =>  [
          'product_category_id' =>  $this->productCategory->id,
          'amount'              =>  '500' 
        ]
      ],
      'billing_taxes' =>  [
        0 =>  [
          'tax_id'  =>  $this->tax->id
        ]
      ],
      'billing_discounts' =>  [
        0 =>  [
          'discount_id'  =>  $this->discount->id
        ]
      ]
    ];

    $this->json('post', '/api/billings', $payload, $this->headers)
      ->assertStatus(422); 
  }

  /** @test */
  function bills_fetched_successfully()
  {
    $this->json('get', '/api/billings', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          0 =>  [
            'customer_id' =>  $this->customer->id,
            'billing_details' =>  [
              0 =>  [
                'product_category_id' =>  $this->productCategory->id,
                'amount'              =>  '500',
                'qty'                 =>  3
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
          ]
        ]
      ]);
  }

  /** @test */
  function bill_saved_successfully()
  {
    $this->disableEH();

    $payload = [
      'customer_id' =>  $this->customer->id,
      'sub_total'   =>  '2000',
      'bill_no'     =>  1,
      'gst_including' =>  true,
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
          'sub_total'   =>  '2000',
          'bill_no'     =>  1,
        ]
      ]);

    $billing = Billing::where('id', '=', 2)->first();

    $this->assertCount(1, $billing->billing_details);
    $this->assertCount(1, $billing->billing_taxes);
    $this->assertCount(1, $billing->billing_discounts);
  }

  /** @test */
  function single_bill_fetched_successfully()
  {
    $this->json('get', '/api/billings/' . $this->billing->id, [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'customer_id' =>  $this->customer->id,
          'billing_details' =>  [
            0 =>  [
              'product_category_id' =>  $this->productCategory->id,
              'amount'              =>  '500',
              'qty'                 =>  3
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
        ]
      ]); 
  }

  /** @test */
  function bill_updated_successfully()
  {
    $this->disableEH();

    $payload = [
      'id'          =>  $this->billing->id,
      'customer_id' =>  $this->customer->id,
      'billing_details' =>  [
        0 =>  [
          'id'                  =>  $this->billing_detail->id,
          'product_category_id' =>  $this->productCategory->id,
          'amount'              =>  '5000',
          'qty'                 =>  3
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
              'qty'                 =>  3
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
  }

  /** @test */
  function billing_details_added_to_bill()
  {
    $payload = [
      'product_category_id' =>  $this->productCategory->id,
      'cost_per_unit'       =>  '200',
      'amount'              =>  '500',
      'qty'                 =>  3,
      'gst_including'       =>  true
    ];

    $this->billing->addDetail($payload);
    $this->billing->addDetail($payload);

    $this->assertCount(3, $this->billing->billing_details);
  }

  /** @test */
  function billing_taxes_added_to_bill()
  {
    $payload = [
      'tax_id' =>  $this->tax->id,
      'amount' =>  '500',
    ];

    $this->billing->addTax($payload);
    $this->billing->addTax($payload);
    $this->billing->addTax($payload);

    $this->assertCount(4, $this->billing->billing_taxes);
  }

  /** @test */
  function billing_discount_added_to_the_bill()
  {
    $payload = [
      'discount_id' =>  $this->discount->id,
      'amount' =>  '500',
    ];

    $this->billing->addDiscount($payload);

    $this->assertCount(2, $this->billing->billing_discounts);
  }

  /** @test */
  function get_latest_bill_no()
  {
    $this->disableEH();

    $this->json('get', '/api/billings/get-latest-bill-no', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'bill_no' =>  $this->billing->bill_no
        ]
      ]); 
  }
}
