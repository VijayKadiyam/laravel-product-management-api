<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingTax extends Model
{
  protected $fillable = [
    'tax_id', 'amount'
  ];

  /*
   * A billing tax belongs to a bill
   *
   *@
   */
  public function billing()
  {
    return $this->belongsTo(Billing::class);
  }

  /*
   * A billing tax belongs to tax
   *
   *@
   */
  public function tax()
  {
    return $this->belongsTo(Tax::class);
  }
}
