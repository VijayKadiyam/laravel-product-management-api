<?php

namespace App\Http\Controllers;

use App\Company;
use App\Product;
use App\Setting;
use App\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
  /*
   * To get the customer ledger
   *
   *@
   */
  public function customerLedger(Request $request)
  {
    $customer = Customer::where('id', '=', $request->customer_id)->first();
    if($customer)
      $billings = $customer->billings;
    else
      return "Please Select a Customer First";

    $settings = Setting::where('company_id', '=', $request->company_id)->first(); 
    if(! $settings)
      $settings->bill_format = 'BILL/';

    if($request->fromDate) {
      $fromDate = new Carbon($request->fromDate);
      $billings = $billings->where('created_at', '>=', $fromDate->toDateTimeString());
    }  

    if($request->toDate) {
      $toDate = (new Carbon($request->toDate))->addDays(1);;
      $billings = $billings->where('created_at', '<=', $toDate->toDateTimeString());
    }  

    return view('reports.customer-ledger', compact('customer', 'billings', 'settings'));
  }

  /*
   * To get the stock category report
   *
   *@
   */
  public function stockCategoryReport(Request $request)
  {
    
  }

  /*
   * To get the product category report
   *
   *@
   */
  public function productCategoryReport(Request $request)
  { 
    $company = Company::where('id', '=', request()->company_id)->first();
    $product_category = $company->product_categories()->find($request->product_category_id);
    $products = Product::where('product_category_id', '=', $request->product_category_id)->oldest()->get();

    $fromDate = '';
    $toDate = '';
    if($request->fromDate) {
      $fromDate = new Carbon($request->fromDate);
      // $products = $products->where('created_at', '>=', $fromDate->toDateTimeString());
    } 
    else {
      return "Please select from date";
    }

    if($request->toDate)
      $toDate = (new Carbon($request->toDate));  
    else {
      return "Please select to date";
    }

    $data = [];
    $total = 0;
    while($fromDate <= $toDate) {
      $products = Product::whereDate('created_at', '=', $fromDate->format('Y-m-d'))->get();

      $qty = 0;
      foreach($products as $product) {
        $qty += $product->qty;
      }

      $temp = [];
      $temp['Date'] = $fromDate->format('d-m-Y');
      $temp['Bags Manufactured'] = $qty . ' Bags';
      $temp['Sub_Total (in Kgs)'] = 0;
      foreach($product_category->stock_categories as $stock_category) {
        $temp[$stock_category->name . '(in Kgs)'] = $stock_category->pivot->value * $qty;
        $temp['Sub_Total (in Kgs)'] += $stock_category->pivot->value * $qty;
      }
      $total += $temp['Sub_Total (in Kgs)'];
      array_push($data, $temp);

      $fromDate->addDays(1);
    }

    $keys = array_keys($temp);

    return view('reports.product-report', compact('data', 'total', 'keys', 'product_category', 'fromDate', 'toDate'));
  }
}
