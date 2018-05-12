<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
  protected $fillable = [
    'customer_id', 'sub_total', 'bill_no', 'gst_including', 'total_amount', 'delivery_note', 'delivery_note_date', 'supplier_reference', 'terms_of_payment', 'buyer_order_no', 'despatch_document_no', 'despatch_through', 'destination', 'terms_of_delivery'
  ];

  /*
   * A billing belongs to company
   *
   *@
   */
  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  /*
   * A bill belongs to a customer
   *
   *@
   */
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  /*
   * To store a new bill
   *
   *@
   */
  public function store()
  {
    if(request()->header('company-id')) {
      $company = Company::find(request()->header('company-id'));
      if($company)
        $company ? $company->billings()->save($this) : '';
    } 

    return $this;
  }

  /*
   * A bill has many billing details
   *
   *@
   */
  public function billing_details()
  {
    return $this->hasMany(BillingDetail::class)
      ->with('product_category');
  }

  /*
   * Add billing details to a bill
   *
   *@
   */
  public function addDetail($detail)
  {
    if(isset($detail['id']))
    {
      $billingDetail = BillingDetail::where('id', '=', $detail['id'])->first();

      // To reduce the product category quantity
      $productCategory = ProductCategory::where('id', '=', $billingDetail['product_category_id'])->first();
      $productCategory->updateQuantity($billingDetail['qty']);

      $billingDetail->update($detail);
      $productCategory->removeQuantity($billingDetail['qty']);

    }
    else {
      $billingDetail = new BillingDetail($detail);
      $this->billing_details()->save($billingDetail);
    }

    return $this;
  }

  /*
   * A bill has many billing taxes
   *
   *@
   */
  public function billing_taxes()
  {
    return $this->hasMany(BillingTax::class)
      ->with('tax');
  }

  /*
   * To add a tax to a bill
   *
   *@
   */
  public function addTax($tax)
  {
    if(isset($tax['id']))
    {
      $billingTax = BillingTax::where('id', '=', $tax['id'])->first();
      $billingTax->update($tax);
    }
    else {
      $billingTax = new BillingTax($tax);
      $this->billing_taxes()->save($billingTax);
    }

    return $this;
  }

  /*
   * A bill has many billing discounts
   *
   *@
   */
  public function billing_discounts()
  {
    return $this->hasMany(BillingDiscount::class)
      ->with('discount');
  }

  /*
   * To add the discount to a bill
   *
   *@
   */
  public function addDiscount($discount)
  {
    if(isset($discount['id']))
    {
      $billingDiscount = BillingDiscount::where('id', '=', $discount['id'])->first();
      $billingDiscount->update($discount);
    }
    else {
      $billingDiscount = new BillingDiscount($discount);
      $this->billing_discounts()->save($billingDiscount);
    }

    return $this;
  }
}
