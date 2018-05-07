<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $fillable = [
    'product_category_id', 'qty'
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
   * A product belongs to product category
   *
   *@
   */
  public function product_category()
  {
    return $this->belongsTo(ProductCategory::class)
      ->with('stock_categories');
  }

  /*
   * To store a new supplier
   *
   *@
   */
  public function store()
  {
    if(request()->header('company_id')) {
      $company = Company::find(request()->header('company_id'));
      if($company)
        $company ? $company->products()->save($this) : '';
    } 

    return $this;
  }
}
