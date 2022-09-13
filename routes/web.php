<?php

Route::redirect('/', '/login');
Route::redirect('/home', '/admin');
Auth::routes();

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::redirect('/', '/admin/expenses');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Expensecategories
    Route::delete('expense-categories/destroy', 'ExpenseCategoryController@massDestroy')->name('expense-categories.massDestroy');
    Route::resource('expense-categories', 'ExpenseCategoryController');

    // Incomecategories
    Route::delete('income-categories/destroy', 'IncomeCategoryController@massDestroy')->name('income-categories.massDestroy');
    Route::resource('income-categories', 'IncomeCategoryController');

    // Expenses
    Route::delete('expenses/destroy', 'ExpenseController@massDestroy')->name('expenses.massDestroy');
    Route::resource('expenses', 'ExpenseController');

    // Supplier
    Route::resource('supplier', 'SupplierController');
    Route::get('supplier/payment/{id}', 'SupplierController@payment')->name('supplier.payment');
    Route::post('supplier/payment/store', 'SupplierController@paymentStore')->name('supplier.payment.store');


    // Incomes
    Route::delete('incomes/destroy', 'IncomeController@massDestroy')->name('incomes.massDestroy');
    Route::resource('incomes', 'IncomeController');

    // Expensereports
    Route::delete('expense-reports/destroy', 'ExpenseReportController@massDestroy')->name('expense-reports.massDestroy');
    Route::resource('expense-reports', 'ExpenseReportController');

    // Departments
    Route::delete('departments/destroy', 'DepartmentController@massDestroy')->name('department.massDestroy');
    Route::resource('department', 'DepartmentController');

    // Stock In
    Route::resource('material-in', 'MaterialInController');
    Route::get('transfer/material/{id}', 'MaterialInController@transfer')->name('transfer.material');

    // Material Config
    Route::resource('material-config', 'MaterialConfigController');

    //Product
    Route::resource('product', 'ProductController');
    Route::get('purchase/product/create', 'ProductController@purchaseCreate')->name('product.purchase.create');
    Route::get('purchase/product', 'ProductController@purchase')->name('product.purchase');
    Route::get('product/company/return-list/{id}', 'ProductController@returnList')->name('product.company.return');
    Route::post('return/material/stock', 'ProductController@returnStock')->name('return.materail.stock');



    // Neeting
    Route::get('neeting/index', 'NeetingController@index')->name('neeting.index');
    Route::get('neeting/company/transfer-list/{id}', 'NeetingController@transferList')->name('netting.company.transfer');
    Route::get('neeting/company/return-list/{id}', 'NeetingController@returnList')->name('netting.company.return');
    Route::get('neeting/transfer/company/product/{id}', 'NeetingController@dyeingInProduct')->name('netting.transfer.company.product');
    Route::post('neeting/dyeing/transfer/company/product', 'NeetingController@dyeingTransferProduct')->name('netting.dyeing.transfer.company.product');
    Route::get('netting/transfer/show/{id}', 'NeetingController@transferShow')->name('transfer.show');
    Route::get('netting/transfer/other/show/{id}', 'NeetingController@transferOtherShow')->name('transfer.other.show');
    Route::get('neeting/stock/in', 'NeetingController@stockIn')->name('neeting.stock.in');
    Route::get('neeting/stock/out', 'NeetingController@stockOut')->name('neeting.stock.out');
    Route::post('neeting/stock/search', 'NeetingController@search')->name('neeting.stock.search');
    Route::post('neeting/stock/in', 'NeetingController@store')->name('neeting.stock.store');
    Route::post('knitting/return/stock/main', 'NeetingController@returnStock')->name('knitting.stock.return');
    Route::get('netting/transfer/expense/{id}', 'NeetingController@expenseList')->name('netting.transfer.expense');
    Route::get('netting/all/expense', 'NeetingController@expenses')->name('netting.all.expense');

    // Dyeing

    Route::post('dyeing/stock/search', 'DyeingController@search')->name('neeting.stock.search');
    Route::post('dyeing/stock/in', 'DyeingController@store')->name('dyeing.stock.store');
    Route::get('dyeing/index', 'DyeingController@index')->name('dyeing.index');
    Route::get('dyeing/all/expense', 'DyeingController@expenses')->name('dyeing.all.expense');
    Route::get('dyeing/process/company/product/{id}', 'DyeingController@transferProduct')->name('dyeing.process.company.product');

//    Route::get('dyeing/transfer/company/product/{id}', 'DyeingController@transferProduct')->name('dyeing.transfer.company.product');
    Route::get('dyeing/company/transfer-list/{id}', 'DyeingController@transferList')->name('dyeing.company.transfer');
    Route::get('dyeing/transfer/show/{id}', 'DyeingController@transferShow')->name('dyeing.transfer.show');

    //Showroom
    Route::post('showroom/stock/transfer', 'ShowroomController@store')->name('showroom.stock.store');
    Route::get('showroom/index', 'ShowroomController@index')->name('showroom.index');
    Route::get('showroom/transfer/{id}', 'ShowroomController@show')->name('showroom.transfer');

    // Stock Set
    Route::get('stock/set', 'StockSetController@index')->name('stock.set');
    Route::get('showroom/stock', 'StockSetController@index')->name('showroom.stock');
    Route::get('add/set', 'StockSetController@create')->name('add.set');
    Route::get('show/set/{id}', 'StockSetController@show')->name('show.set');
    Route::post('store/set', 'StockSetController@store')->name('store.set');










    // Color
    Route::get('color/index', 'MaterialConfigController@color')->name('color.index');
    Route::get('color/create', 'MaterialConfigController@colorCreate')->name('color.create');

    // Company
    Route::resource('company', 'CompanyController');




    // HR
    Route::resource('employee', 'EmployeeController');
});
