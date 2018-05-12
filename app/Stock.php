<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
  protected $fillable = [
    'supplier_id', 'stock_category_id', 'price', 'qty'
  ];

  /*
   * A stock belongs to company
   *
   *@
   */
  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  /*
   * To store a new stock
   *
   *@
   */
  public function store()
  {
    if(request()->header('company-id')) {
      $company = Company::find(request()->header('company-id'));
      if($company)
        $company ? $company->stocks()->save($this) : '';
    } 

    return $this;
  }

  /*
   * A stock belongs to stock categories
   *
   *@
   */
  public function stock_category()
  {
    return $this->belongsTo(StockCategory::class);
  }

  /*
   * A stock belongs to supplier
   *
   *@
   */
  public function supplier()
  {
    return $this->belongsTo(Supplier::class);
  }
}
