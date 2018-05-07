<?php

namespace App\Http\Controllers;

use App\Company;
use App\Product;
use App\StockCategory;
use App\ProductCategory;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  /*
   * To get all the products
   *
   *@
   */
  public function index()
  {
    $company = Company::where('id', '=', request()->header('company_id'))->first();
    if($company)
      $products = $company->products;
    else
      $products = "";

    return response()->json([
      'data'  =>  $products
    ], 200);
  }

  /*
   * To store a product
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'product_category_id' =>  'required',
      'qty'                 =>  'required'
    ]);

    $product = new Product($request->all());
    $product->store();

    $productCategory = ProductCategory::where('id', '=', $request->product_category_id)->first();
    $productCategory->updateQuantity($request->qty);

    // Reduce the stock quantity of this product
    foreach($productCategory->stock_categories as $stockCategory) { 
      $stockCategory->removeQuantity($request->qty * $stockCategory->pivot->value);
    }

    return response()->json([
      'data'  =>  $product->toArray()
    ], 201);
  }

  /*
   * To get a single product
   *
   *@
   */
  public function show(Product $product)
  {
    $company = Company::where('id', '=', request()->header('company_id'))->first();

    return response()->json([
      'data'  =>  $product->toArray()
    ]);
  }

  /*
   * To update a product
   *
   *@
   */
  public function update(Request $request, Product $product)
  {
    $request->validate([
      'product_category_id' =>  'required',
      'qty'                 =>  'required'
    ]);

    // Update the product quantity before update
    $productCategory = ProductCategory::where('id', '=', $request->product_category_id)->first();
    $productCategory->removeQuantity($product->qty);

    // Reduce the stock quantity of this product
    foreach($productCategory->stock_categories as $stockCategory) { 
      $stockCategory->updateQuantity($product->qty * $stockCategory->pivot->value);
    }

    // reduce the produce quantity after update 
    $product->update($request->all());
    $productCategory->updateQuantity($request->qty);

    // Reduce the stock quantity of this product
    foreach($productCategory->stock_categories as $stockCategory) { 
      $stockCategory->removeQuantity($request->qty * $stockCategory->pivot->value);
    }

    return response()->json([
      'data'  =>  $product->toArray()
    ]);
  }
}
