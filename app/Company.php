<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
  protected $fillable = [
    'name', 'email', 'contact1', 'pan_no', 'gstn_no', 'address', 'state_code', 'acc_name', 'acc_no', 'ifsc_code', 'branch' 
  ]; 

  /*
   * A company belongs to users 
   *
   *@
   */
  public function users()
  {
    return $this->belongsToMany(User::class);
  }

  /*
   * To store a new company
   *
   *@
   */
  public function store()
  {
    $this->save();
    $this->assignUser(\Auth::guard('api')->user());

    return $this;
  }

  /*
   * A company has many settings
   *
   *@
   */
  public function settings()
  {
    return $this->hasMany(Setting::class);
  }

  /*
   * To assign a user
   *
   *@
   */
  public function assignUser($user)
  {
    $user->companies()->find($this->id) ?  '' : $user->companies()->attach($this);

    return $this; 
  }

  /*
   * A company has many roles
   *
   *@
   */
  public function roles()
  {
    return $this->hasMany(Role::class)
      ->with('permissions', 'permissions.module');
  }

  /*
   * A company has many units
   *
   *@
   */
  public function units()
  {
    return $this->hasMany(Unit::class);
  }

  /*
   * A company has many taxes
   *
   *@
   */
  public function taxes()
  {
    return $this->hasMany(Tax::class);
  }

  /*
   * A company has many discounts
   *
   *@
   */
  public function discounts()
  {
    return $this->hasMany(Discount::class);
  }

  /*
   * A company has many suppliers
   *
   *@
   */
  public function suppliers()
  {
    return $this->hasMany(Supplier::class)
      ->latest();
  }

  /*
   * A company has many customers
   *
   *@
   */
  public function customers()
  {
    return $this->hasMany(Customer::class)
      ->latest();
  }

  /*
   * A company has many stock categories
   *
   *@
   */
  public function stock_categories()
  {
    return $this->hasMany(StockCategory::class)
      ->with('unit');
  }

  /*
   * A company has many stocks
   *
   *@
   */
  public function stocks()
  {
    return $this->hasMany(Stock::class)
      ->with('stock_category', 'supplier');
  }

  /*
   * A company has many product categories
   *
   *@
   */
  public function product_categories()
  {
    return $this->hasMany(ProductCategory::class)
      ->with('stock_categories')
      ->latest();
  }

  /*
   * A company has many products
   *
   *@
   */
  public function products()
  {
    return $this->hasMany(Product::class)
      ->with('product_category')
      ->latest();
  }

  /*
   * A company has many bills
   *
   *@
   */
  public function billings()
  {
    return $this->hasMany(Billing::class)
      ->with('billing_details', 'billing_taxes', 'billing_discounts', 'customer', 'company')
      ->latest();
  }
}
