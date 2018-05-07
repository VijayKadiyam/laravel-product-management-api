<?php

namespace Tests\Feature\Stock;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockQuantityTest extends TestCase
{
  use DatabaseTransactions;

  protected $stockCategory, $supplier, $stock, $productCategory, $product;
  public function setUp()
  {
    parent::setUp();

    $this->stockCategory = factory(\App\StockCategory::class)->create([
      'company_id'  =>  $this->company->id,
      'quantity_left' =>  50
    ]);

    $this->supplier = factory(\App\Supplier::class)->create([
      'company_id'  => $this->company->id
    ]);

    $this->stock = factory(\App\Stock::class)->create([
      'company_id'  =>  $this->company->id,
      'supplier_id' => $this->supplier->id,
      'stock_category_id' => $this->stockCategory->id,
      'price'  => 200,
      'qty'    => 50
    ]);

    $this->productCategory = factory(\App\ProductCategory::class)->create([
      'company_id'  =>  $this->company->id,
      'name'  =>  'Silicoplast',
      'quantity_left' =>  50
    ]); 

    $value = "2"; 

    $this->productCategory->addStockCategory($this->stockCategory, $value);

    $this->product = factory(\App\Product::class)->create([
      'company_id'  =>  $this->company->id,
      'product_category_id' =>  $this->productCategory->id,
      'qty'   =>  5
    ]);
  }

  /** @test */
  function update_the_stock_quantity()
  {
    $this->stockCategory->updateQuantity($this->stock->qty);

    $this->assertEquals(100, $this->stockCategory->quantity_left);
  }

  /** @test */
  function stock_category_quantity_updated_when_any_new_stock_is_added()
  {
    $payload = [
      'supplier_id' => $this->supplier->id,
      'stock_category_id' => $this->stockCategory->id,
      'price'  => 200,
      'qty'    => 10
    ];

    $this->json('post', '/api/stocks', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'supplier_id' => $this->supplier->id,
          'stock_category_id' => $this->stockCategory->id,
          'price'  => 200,
          'qty'    => 10
        ]
      ]);

    $this->stockCategory->refresh();

    $this->assertEquals(60, $this->stockCategory->quantity_left);
  }

  /** @test */
  function remove_the_stock_quantity()
  {
    $this->stockCategory->updateQuantity($this->stock->qty);
    $this->stockCategory->removeQuantity($this->stock->qty);

    $this->assertEquals(50, $this->stockCategory->quantity_left);
  }

  /** @test */
  function stock_updated_successfully()
  { 
    $this->disableEH();

    $this->stock->qty = 100;

    $this->json('patch', "/api/stocks/" .$this->stock->id, $this->stock->toArray(), $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'company_id'  =>  $this->company->id,
          'supplier_id' => $this->supplier->id,
          'stock_category_id' => $this->stockCategory->id,
          'price'  => 200,
          'qty'    => 100
        ]
      ]);

    $this->stockCategory->refresh();

    $this->assertEquals(100, $this->stockCategory->quantity_left);
  }

  /** @test */
  function remove_stock_when_the_product_is_added()
  {
    $this->disableEH(); 

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

    $this->stockCategory->refresh();
    $this->assertEquals(40, $this->stockCategory->quantity_left);
  }

  /** @test */
  function remove_stock_when_the_product_is_updated()
  { 
    $this->stockCategory->quantity_left = 40;
    $this->stockCategory->save();

    $this->product->qty = 10;

    $this->json('patch', '/api/products/' . $this->product->id, $this->product->toArray(), $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'product_category_id' =>  $this->productCategory->id,
          'qty'   =>  10
        ]
      ]);

    $this->stockCategory->refresh();
    $this->assertEquals(30, $this->stockCategory->quantity_left);
  }

  /** @test */
  function refresh_the_stock_quantity()
  {
    $this->stockCategory->refreshQuantity();
    $this->stockCategory->refresh();

    $this->assertEquals(40, $this->stockCategory->quantity_left);

  }


}
