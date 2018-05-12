<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
  protected $fillable = [
    'name', 'hsn_code', 'quantity_left'
  ];

  /*
   * A product belongs to a company
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
  public function stock_categories()
  {
    return $this->belongsToMany(StockCategory::class, 'product_stocks', 'product_category_id', 'stock_category_id')
      ->with('unit')
      ->withPivot('company_id', 'value')
      ->withTimeStamps(); 
  }

  /*
   * To store  a product category
   *
   *@
   */
  public function store()
  {
    if(request()->header('company-id')) {
      $company = Company::find(request()->header('company-id'));
      if($company)
        $company ? $company->product_categories()->save($this) : '';
    } 

    return $this;
  }

  /*
   * Update the product quantity when any product is added
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
   * Remove the product quantoty when any product is updated
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
    $products = Product::where('product_category_id', '=', $this->id)->get();
    foreach($products as $product) {
      $quantity += $product->qty;
    }

    $billingDetails = BillingDetail::where('product_category_id', '=', $this->id)->get();
    foreach($billingDetails as $billingDetail) {
      $quantity -= $billingDetail->qty;
    }

    $this->quantity_left = $quantity;
    $this->update();
  }

  /*
   * To add a stock category to a product category
   *
   *@
   */
  public function addStockCategory($stockCategory, $value = 0)
  { 
    return $this->stock_categories()->syncWithoutDetaching([
      $stockCategory->id =>  [ 'company_id'  =>  $this->company_id, 'value' =>  $value ]
    ]); 
  }

  /*
   * A product category has many products
   *
   *@
   */
  public function products()
  {
    return $this->hasMany(Product::class);
  }
}
