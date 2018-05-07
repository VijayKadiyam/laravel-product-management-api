<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingDiscount extends Model
{
  protected $fillable = [
    'discount_id', 'amount'
  ];

  /*
   * A discount belongs to a bill
   *
   *@
   */
  public function billins()
  {
    return $this->belongsTo(Billing::class);
  }

  /*
   * A billing discount belongs to discount
   *
   *@
   */
  public function discount()
  {
    return $this->belongsTo(Discount::class);
  }
}
