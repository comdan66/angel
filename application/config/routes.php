<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

Route::root ('main');

// $route['admin'] = "admin/main";
Route::get ('admin', 'admin/main@index');

Route::get ('/login', 'platform@login');
Route::get ('/logout', 'platform@logout');
Route::get ('/platform/index', 'platform@login');
Route::get ('/platform', 'platform@login');

Route::post ('/api/messages', 'api/messages@create');


Route::group ('admin', function () {
  Route::resourcePagination (array ('keywords'), 'keywords');
  Route::resourcePagination (array ('users'), 'users');
});
