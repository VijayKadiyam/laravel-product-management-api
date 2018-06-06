<?php

namespace App\Http\Controllers;

use App\Stock;
use App\Company;
use App\Product;
use App\Setting;
use App\Customer;
use Carbon\Carbon;
use App\ProductCategory;
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
    $company = Company::where('id', '=', request()->company_id)->first();
    $stock_category = $company->stock_categories()->find($request->stock_category_id);

    $fromDate = '';
    $toDate = '';
    if($request->fromDate) {
      $fromDate = new Carbon($request->toDate);
    } 
    else {
      return "Please select from date";
    }

    if($request->toDate)
      $toDate = (new Carbon($request->fromDate));  
    else {
      return "Please select to date";
    }

    $data = [];
    $keys = [
      'Date', 'Supplier Name', 'Invoice No', 'Qty Received'
    ];
    $balance = $stock_category->quantity_left;
    while($fromDate >= $toDate) {
      // Get the stocks for a particular date
      $stocks = Stock::where('date', '=', $fromDate->format('d-m-Y'))
        ->where('stock_category_id', '=', $request->stock_category_id)
        ->with('supplier')
        ->get();

      $temp = [];
      foreach($stocks as $stock) {
        $temp['date'] = $stock->date;
        $temp['supplier_name'] = $stock->supplier->name;
        $temp['invoice_no'] = $stock->invoice_no;
        $temp['qty'] = $stock->qty;
        $balance += $stock->qty;
      }

      if(count($stocks) == 0) {
        $temp['date'] = $fromDate;
        $temp['supplier_name'] = 0;
        $temp['invoice_no'] = 0;
        $temp['qty'] = 0;
      }

      // Get the production for a particular date
      $product_categories = ProductCategory::all();
      foreach($product_categories as $product_category) {
        $products = Product::whereDate('created_at', '=', $fromDate->format('Y-m-d'))
          ->where('product_category_id', '=', $product_category->id)
          ->get();

        $qty = 0;
        foreach($products as $product) {
          $qty += $product->qty;
        }

        foreach($product_category->stock_categories as $stock_category) {
          if($stock_category->id == $request->stock_category_id) { 
            if(!in_array($product_category->name . ' Qty. Manu. (in Kgs)' , $keys)) {
              array_push($keys, $product_category->name . ' Qty. Manu. (in Kgs)');
            }
            $temp[$product_category->name . ' Qty. Manu.'] = $stock_category->pivot->value * $qty;
            $balance -= $stock_category->pivot->value * $qty; 
            // dd($stock_category->pivot->value * $qty);
          }
        }
      } 

      $temp['balance'] = $balance;

      array_push($data, $temp);
      $fromDate->subDays(1);
    }

    $opening_balance = $temp['balance'];

    array_push($keys, 'Balance (in Kgs)');

    // To get back the from date
    if($request->fromDate) {
      $fromDate = new Carbon($request->fromDate);
    } 

    // To get back the from date
    if($request->toDate) {
      $toDate = new Carbon($request->toDate);
    } 

    return view('reports.stock-report', compact('data', 'keys', 'stock_category', 'fromDate', 'toDate', 'opening_balance'));

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
      $products = Product::whereDate('created_at', '=', $fromDate->format('Y-m-d'))
        ->where('product_category_id', '=', $request->product_category_id)
        ->get();

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
