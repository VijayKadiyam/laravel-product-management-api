<?php

namespace Tests\Feature\Product;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductCategoryTest extends TestCase
{
  use DatabaseTransactions;
  
  /** @test */
  function user_must_be_logged_in()
  {
    $this->json('post', '/api/product-categories')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_name()
  {
     $this->json('post', '/api/product-categories', [], $this->headers)
      ->assertStatus(422);
  }

  /** @test */
  // function product_categories_fetched_successfully()
  // {
  //   factory(\App\ProductCategory::class)->create([
  //     'company_id'  =>  $this->company->id,
  //     'name'  =>  'Silicoplast'
  //   ]);

  //   $this->json('get', '/api/product-categories', [], $this->headers)
  //     ->assertStatus(200)
  //     ->assertJson([
  //       'data'  =>  [
  //         0 =>  [
  //           'name'  =>  'Silicoplast'
  //         ]
  //       ]
  //     ]);
  // }

  /** @test */
  function product_category_saved_successfully()
  {
    $this->disableEH();
    $payload = [
      'name'  =>  'Silicoplast',
      'hsn_code'  =>  '1234'
    ];

    $this->json('post', '/api/product-categories', $payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'  =>  [
          'name'  =>  'Silicoplast',
          'hsn_code'  =>  '1234'
        ]
      ]); 
  }

  /** @test */
  function single_product_category_fetched_successfully()
  {
    $productCategory = factory(\App\ProductCategory::class)->create([
      'company_id'  =>  $this->company->id,
      'name'  =>  'Silicoplast'
    ]);

    $this->json('get', "/api/product-categories/$productCategory->id", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'name'  =>  'Silicoplast'
        ]
      ]);
  }

  /** @test */
  function it_requires_name_while_updating()
  {
    $productCategory = factory(\App\ProductCategory::class)->create([
      'company_id'  =>  $this->company->id,
      'name'  =>  'Silicoplast'
    ]);
    $productCategory->name = "Silica";

    $this->json('patch', "/api/product-categories/$productCategory->id", [], $this->headers)
      ->assertStatus(422);
  }

  /** @test */
  // function product_updated_successfully()
  // {
  //   $productCategory = factory(\App\ProductCategory::class)->create([
  //     'company_id'  =>  $this->company->id,
  //     'name'  =>  'Silicoplast'
  //   ]);
  //   $productCategory->name = "Silica";

  //   $this->json('patch', "/api/product-categories/$productCategory->id", $productCategory->toArray(), $this->headers)
  //     ->assertStatus(200)
  //     ->assertJson([
  //       'data'  =>  [
  //         'name'  =>  'Silica'
  //       ]
  //     ]);
  // }

  /** @test */
  function a_stock_category_can_be_added_to_the_product_category()
  {
    $productCategory = factory(\App\ProductCategory::class)->create([
      'company_id'  =>  $this->company->id,
      'name'  =>  'Silicoplast'
    ]);

    $stockCategory1 = factory(\App\StockCategory::class)->create([
      'name'        =>  'Fly Ash',
      'company_id'  =>  $this->company->id
    ]);

    $stockCategory2 = factory(\App\StockCategory::class)->create([
      'name'        =>  'Silica',
      'company_id'  =>  $this->company->id
    ]);

    $value1 = "20";
    $value2 = "20";

    $productCategory->addStockCategory($stockCategory1, $value1);
    $productCategory->addStockCategory($stockCategory2, $value2);

    $this->assertCount(2, $productCategory->stock_categories);

    $this->assertEquals('20' , $productCategory->stock_categories[0]->pivot->value);
  }

  /** @test */
  function product_fetched_successfully_with_stock_categories()
  {
    $productCategory = factory(\App\ProductCategory::class)->create([
      'company_id'  =>  $this->company->id,
      'name'  =>  'Silicoplast'
    ]);

    $stockCategory1 = factory(\App\StockCategory::class)->create([
      'name'        =>  'Fly Ash',
      'company_id'  =>  $this->company->id
    ]);

    $stockCategory2 = factory(\App\StockCategory::class)->create([
      'name'        =>  'Silica',
      'company_id'  =>  $this->company->id
    ]);

    $value1 = "20";
    $value2 = "20";

    $productCategory->addStockCategory($stockCategory1, $value1);
    $productCategory->addStockCategory($stockCategory2, $value2);

    $this->json('get', '/api/product-categories', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          0 =>  [
            'name'  =>  'Silicoplast',
            'stock_categories'  =>  [
              0 =>  [
                'name'  =>  'Fly Ash'
              ]
            ]
          ]
        ]
      ]);
  }

  /** @test */
  function product_added_successfully_with_stock_category()
  { 
    $this->disableEH();
    $stockCategory1 = factory(\App\StockCategory::class)->create([
      'name'        =>  'Fly Ash',
      'company_id'  =>  $this->company->id
    ]); 

    $payload = [
      'name'  =>  'Silicoplast',
      'stock_categories'  =>  [
        0 =>   [
        'id'    =>  $stockCategory1->id,
        'value' =>  '20'
        ]
      ]
    ]; 

    $this->json('post', '/api/product-categories', $payload, $this->headers)
      ->assertStatus(201);

    $productCategory = \App\ProductCategory::find(1);

    $this->assertCount(1, $productCategory->stock_categories);

    $this->assertEquals('20' , $productCategory->stock_categories[0]->pivot->value);
  }

  /** @test */
  function product_updated_successfully_with_stock_category()
  {
    $this->disableEH();
    $stockCategory1 = factory(\App\StockCategory::class)->create([
      'name'        =>  'Fly Ash',
      'company_id'  =>  $this->company->id
    ]); 

    $stockCategory2 = factory(\App\StockCategory::class)->create([
      'name'        =>  'Cement',
      'company_id'  =>  $this->company->id
    ]); 

    $productCategory = factory(\App\ProductCategory::class)->create([
      'company_id'  =>  $this->company->id,
      'name'  =>  'Silicoplast'
    ]);

    $payload = [
      'id'    =>  $productCategory->id,
      'name'  =>  'Silicoplast',
      'hsn_code'  =>  '1345',
      'stock_categories'  =>  [
        0 =>   [
          'id'    =>  $stockCategory1->id,
          'value' =>  '20'
        ],
        1 =>   [
          'id'    =>  $stockCategory2->id,
          'value' =>  '30'
        ],
      ]
    ]; 

    $this->json('patch', "/api/product-categories/$productCategory->id", $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [ 
          'name'  =>  'Silicoplast',
          'hsn_code'  =>  '1345',
          'stock_categories'  =>  [
            0 =>  [
              'name'  =>  'Fly Ash'
            ],
            1 =>   [
              'name'    =>  'Cement',
              'pivot'   =>  [
                'value' =>  30
              ]
            ],
          ] 
        ]
      ]);
  }
}
