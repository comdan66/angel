<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Route::root ('main');

Route::get ('/login', 'platform@login');
Route::get ('/logout', 'platform@logout');

Route::get ('admin', 'admin/main@index');

Route::group ('admin', function () {
  Route::resourcePagination (array ('richmenus'), 'richmenus');
  Route::resourcePagination (array ('richmenu', 'actions'), 'richmenu_actions');
  Route::resourcePagination (array ('richmenu', 'sources'), 'richmenu_sources');
  // Route::get ('richmenus/(:id)/users', 'richmenus@users($1, 0)');

  // Route::get ('richmenus/(:id)/users/(:num)', 'richmenus@users($1, $2)');
  // Route::post ('richmenus/(:id)/users', 'richmenus@users_pick($1)');
  // Route::delete ('richmenus/(:id)/users', 'richmenus@users_delete($1)');
});