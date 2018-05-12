<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', 'Auth\RegisterController@register');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout');

Route::resource('/users', 'UsersController');
Route::post('users/assign-roles', 'UsersController@assignRoles');

// Helpers
Route::resource('/roles', 'RolesController');
Route::post('roles/assign-permissions', 'RolesController@assignPermissions');
Route::resource('/units', 'UnitsController');
Route::resource('/taxes', 'TaxesController');
Route::resource('/discounts', 'DiscountsController');
Route::resource('/modules', 'ModulesController');
Route::resource('/permissions', 'PermissionsController');

// Companies
Route::resource('/companies', 'CompaniesController');

// Suppliers
Route::resource('/suppliers', 'SuppliersController');

// Customers
Route::resource('/customers', 'CustomersController');

// Stock Categories
Route::resource('stock-categories', 'StockCategoriesController');
Route::get('/stock-categories/{stock_category}/refresh-quantity', 'StockCategoriesController@refreshQuantity');

// Stocks
Route::resource('/stocks', 'StocksController');

// Product Categories
Route::resource('/product-categories', 'ProductCategoriesController');
Route::get('/product-categories/{product_category}/refresh-quantity', 'ProductCategoriesController@refreshQuantity');

// Products
Route::resource('/products', 'ProductsController');

// Billings
Route::get('/billings/get-latest-bill-no', 'BillingsController@getLatestBillNo');
Route::resource('/billings', 'BillingsController');
Route::get('/billings/{billing_id}/view', 'BillingsController@view');
Route::get('/billings/{billing_id}/print', 'BillingsController@print');
Route::get('/billings/{billing_id}/printChallan', 'BillingsController@printChallan');

// Settings
Route::resource('settings', 'SettingsController');

// Reports
Route::get('/customer-ledger', 'ReportsController@customerLedger');
Route::get('/product-category-report', 'ReportsController@productCategoryReport');
