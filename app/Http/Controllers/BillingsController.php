<?php

namespace App\Http\Controllers;

use App\Billing;
use App\Company;
use App\Setting;
use App\ProductCategory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class BillingsController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api')
      ->except('view', 'print', 'printChallan');
  }

  /*
   * To get all the bills
   *
   *@
   */
  public function index()
  {
    $company = Company::where('id', '=', request()->header('company-id'))->first();
    if($company)
      $billings = $company->billings;
    else
      $billings = "";

    return response()->json([
      'data'  =>  $billings
    ], 200);
  }

  /*
   * To store a new bill
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'customer_id' =>  'required',
      'gst_including' =>  'required',
      'billing_details.*.product_category_id' =>  'required',
      'billing_details.*.cost_per_unit' =>  'required',
      'billing_details.*.amount' =>  'required',
      'billing_details.*.qty' =>  'required',
      'billing_taxes.*.tax_id'  =>  'required',
      'billing_taxes.*.amount'  =>  'required',
      'billing_discounts.*.discount_id' =>  'required',
      'billing_discounts.*.amount'      =>  'required'
    ]); 

    $billing = new Billing($request->all());
    $billing->store();

    // To store the billing details 
    if($request->billing_details) { 
      foreach($request->billing_details as $billingDetail) {  
        $billing->addDetail($billingDetail);

        // To reduce the product category quantity
        $productCategory = ProductCategory::where('id', '=', $billingDetail['product_category_id'])->first();
        $productCategory->removeQuantity($billingDetail['qty']);
      }
    } 

    // To store the billing taxes 
    if($request->billing_taxes) {
      foreach($request->billing_taxes as $billingTax) {  
        $billing->addTax($billingTax);
      }
    } 

    // To store the billing discounts 
    if($request->billing_discounts) {
      foreach($request->billing_discounts as $billingDiscount) {  
        $billing->addDiscount($billingDiscount);
      }
    } 

    return response()->json([
      'data'  =>  $billing->toArray()
    ], 201);
  }

  /*
   * To fetch a single bill
   *
   *@
   */
  public function show(Billing $billing)
  {
    $company = Company::where('id', '=', request()->header('company-id'))->first();
    // dd($company->billings()->find($billing->id)->toArray());

    return response()->json([
      'data'  =>  $company->billings()->find($billing->id)->toArray()
    ], 200);
  }

  /*
   * TO update a bill
   *
   *@
   */
  public function update(Billing $billing, Request $request)
  {
    $request->validate([
      'customer_id' =>  'required' 
    ]); 

    $billing->update($request->all());

    // To store the billing details 
    if($request->billing_details) {
      foreach($request->billing_details as $billingDetail) {  
        $billing->addDetail($billingDetail);
      }
    } 

    // To store the billing taxes 
    if($request->billing_taxes) {
      foreach($request->billing_taxes as $billingTax) {  
        $billing->addTax($billingTax);
      }
    } 

    // To store the billing discounts 
    if($request->billing_discounts) {
      foreach($request->billing_discounts as $billingDiscount) {  
        $billing->addDiscount($billingDiscount);
      }
    } 

    $company = Company::where('id', '=', request()->header('company-id'))->first();

    return response()->json([
      'data'  =>  $company->billings()->find($billing->id)->toArray()
    ], 200);
  }

  /*
   * Get the latest bill no
   *
   *@
   */
  public function getLatestBillNo()
  {

    $latestBill = Billing::where('company_id', '=', request()->header('company-id'))->latest()->first();

    return response()->json([
      'data'  =>  $latestBill
    ]);
  }

  /*
   * To view the bill
   *
   *@
   */
  public function view($billing)
  {
    $billing = Billing::find($billing);
    $company = Company::where('id', '=', $billing->company_id)->first();
    $bill = $company->billings()->find($billing->id); 

    $settings = Setting::where('company_id', '=', $company->id)->first(); 

    return view('pdfs.bill', compact('bill', 'settings')); 
  }

  /*
   * To print the bill
   *
   *@
   */
  public function print($billing)
  {
    $billing = Billing::find($billing);
    $company = Company::where('id', '=', $billing->company_id)->first();
    $bill = $company->billings()->find($billing->id); 

    $settings = Setting::where('company_id', '=', $company->id)->first(); 

    $pdf = PDF::loadView('pdfs.bill', compact('bill', 'settings'));
    return $pdf->download($settings->bill_format. $bill->bill_no . '.pdf'); 
  }

  /*
   * To print the challan
   *
   *@
   */
  public function printChallan($billing)
  {
    $billing = Billing::find($billing);
    $company = Company::where('id', '=', $billing->company_id)->first();
    $bill = $company->billings()->find($billing->id); 

    $settings = Setting::where('company_id', '=', $company->id)->first(); 

    return view('pdfs.challan', compact('bill', 'settings'));  

    $pdf = PDF::loadView('pdfs.challan', compact('bill'));
    return $pdf->download('17-18/'. $bill->bill_no . '.pdf');
  }
}
