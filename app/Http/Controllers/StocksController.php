<?php

namespace App\Http\Controllers;

use App\Stock;
use App\Company;
use App\StockCategory;
use Illuminate\Http\Request;

class StocksController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  /*
   * To get all the stocks
   *
   *@
   */
  public function index()
  {
    $company = Company::where('id', '=', request()->header('company-id'))->first();
    if($company)
      $stocks = $company->stocks;
    else
      $stocks = "";

    return response()->json([
      'data'  =>  $stocks
    ], 200);
  }

  /*
   * To store a new stock
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'supplier_id'       =>  'required',
      'stock_category_id' =>  'required',
      'price'             =>  'required',
      'qty'               =>  'required',
      'date'              =>  'required'
    ]);

    $stock = new Stock($request->all());
    $stock->store();

    $stockCategory = StockCategory::where('id', '=', $request->stock_category_id)->first();
    $stockCategory->updateQuantity($request->qty);

    return response()->json([
      'data'  =>  $stock->toArray()
    ], 200);  
  }

  /*
   * To show a stock
   *
   *@
   */
  public function show(Stock $stock)
  {
    return response()->json([
      'data'  =>  $stock->toArray()
    ], 200);
  }

  /*
   * To update a stock
   *
   *@
   */
  public function update(Request $request, Stock $stock)
  {
    $request->validate([
      'supplier_id'       =>  'required',
      'stock_category_id' =>  'required',
      'price'             =>  'required',
      'qty'               =>  'required',
      'date'              =>  'required'
    ]);

    $stockCategory = StockCategory::where('id', '=', $request->stock_category_id)->first();
    $stockCategory->removeQuantity($stock->qty);

    $stock->update($request->all());
    $stockCategory->updateQuantity($request->qty);

    return response()->json([
      'data'  =>$stock->toArray()
    ], 200);
  }
}
