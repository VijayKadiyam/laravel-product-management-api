<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingDetail extends Model
{
  protected $fillable = [
    'product_category_id', 'cost_per_unit', 'amount', 'qty'
  ];

  /*
   * A billing detail belongs to a bill
   *
   *@
   */
  public function billing()
  {
    return $this->belongsTo(Billing::class);
  }

  /*
   * A billing detail belongs to product category
   *
   *@
   */
  public function product_category()
  {
    return $this->belongsTo(ProductCategory::class)
      ->with('stock_categories');
  }
}
