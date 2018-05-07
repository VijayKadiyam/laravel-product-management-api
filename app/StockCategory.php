<?php

namespace App;

use App\StockCategory;
use App\ProductCategory;
use Illuminate\Database\Eloquent\Model;

class StockCategory extends Model
{
  protected $fillable = [
    'name', 'unit_id'
  ];

  /*
   * It belongs to company
   *
   *@
   */
  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  /*
   * A product has many stock categories
   *
   *@
   */
  public function product_categories()
  {
    return $this->belongsToMany(ProductCategory::class, 'product_stocks', 'stock_category_id', 'product_category_id') 
      ->withPivot('company_id', 'value')
      ->withTimeStamps(); 
  }

  /*
   * To store a new stock catoegory
   *
   *@
   */
  public function store()
  {
    if(request()->header('company_id')) {
      $company = Company::find(request()->header('company_id'));
      if($company)
        $company ? $company->stock_categories()->save($this) : '';
    } 

    return $this;
  }

  /*
   * Update the stock quantity when any stock is added
   *
   *@
   */
  public function updateQuantity($qty)
  {
    $this->quantity_left += $qty;
    $this->update();

    // $this->refreshQuantity();

    $this->refresh();
  }

  /*
   * Remove the stock quantoty when any stock is updated
   *
   *@
   */
  public function removeQuantity($qty)
  {
    $this->quantity_left -= $qty;
    $this->update();

    // $this->refreshQuantity();

    $this->refresh();
  }

  /*
   * Refresh Quantity
   *
   *@
   */
  public function refreshQuantity()
  {
    $quantity = 0;
    $stocks = Stock::where('stock_category_id', '=', $this->id)->get();
    foreach($stocks as $stock) {
      $quantity += $stock->qty;
    }

    // $stockCategory = StockCategory::where('id', '=', $this->id)->first();
    foreach($this->product_categories as $product_category)
    {
      $products = Product::where('product_category_id', '=', $product_category->id)->get();
      foreach($products as $product) {
        $quantity -= $product->qty * $product_category->pivot->value;
      }
    } 
    
    $this->quantity_left = $quantity;
    $this->update();
  }

  /*
   * A stock belongs to a unit
   *
   *@
   */
  public function unit()
  {
    return $this->belongsTo(Unit::class);
  }
}
