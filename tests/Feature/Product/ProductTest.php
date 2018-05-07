<?php

namespace Tests\Feature\Product;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductTest extends TestCase
{
  use DatabaseTransactions;

  protected $productCategory, $product;

  public function setUp()
  {
    parent::setUp();

    $this->productCategory = factory(\App\ProductCategory::class)->create([
      'company_id'  =>  $this->company->id,
      'name'  =>  'Silicoplast'
    ]);

    $this->product = factory(\App\Product::class)->create([
      'company_id'  =>  $this->company->id,
      'product_category_id' =>  $this->productCategory->id,
      'qty'   =>  50
    ]);
  }

  /** @test */
  function user_must_be_logged_in()
  {
    $this->json('post', '/api/products')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_productCategoryId_and_qty()
  {
    $this->json('post', '/api/products', [], $this->headers)
      ->assertStatus(422); 
  }

  /** @test */
  function products_fetched_successfully()
  {
    $this->disableEH();

    $this->json('get', '/api/products', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          0 =>  [
            'product_category_id' =>  $this->productCategory->id,
            'qty'   =>  50
          ]
        ]
      ]);
  }

  /** @test */
  function product_saved_successfully()
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
  }

  /** @test */
  function single_product_fetched_successfully()
  {
    $this->json('get', '/api/products/' . $this->product->id, [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'product_category_id' =>  $this->productCategory->id,
          'qty'   =>  50
        ]
      ]); 
  }

  /** @test */
  function it_requires_productCategoryId_and_qty_to_update_a_product()
  {
    $this->json('patch', '/api/products/' . $this->product->id, [], $this->headers)
      ->assertStatus(422);
  }

  /** @test */
  function product_updated_successfully()
  {
    $this->product->qty = 100;

    $this->json('patch', '/api/products/' . $this->product->id, $this->product->toArray(), $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'product_category_id' =>  $this->productCategory->id,
          'qty'   =>  100
        ]
      ]);
  }
}
