<?php

use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 授权
Route::get('/oauth', 'OauthController@oauth')->name('oauth');
// 安装
Route::prefix('install')->group(function () {
    Route::any('/', 'InstallController@install')->name('_1stInstall');
    Route::any('apply', 'InstallController@apply')->name('apply');
    Route::any('reset', 'InstallController@reset')->name('reset');
    Route::any('bind', 'InstallController@bind')->name('bind');
});

// 索引
Route::group(['namespace' => 'Index\Auth'], function () {
    Route::get('/', 'LoginController@showLoginForm')->name('login');
    Route::post('/', 'LoginController@login');
    Route::post('/logout', 'LoginController@logout')->name('logout')->middleware('checkUserAuth');
});

Route::group(['middleware' => ['auth:web'], 'namespace' => 'Index'], function () {
    Route::group(['prefix' => 'home'], function () {
        Route::get('{query?}', 'IndexController@list')->where('query', '.*')->name('home');
    });
    Route::get('show/{query}', 'IndexController@show')->where('query', '.*')->name('show');
    Route::group(['middleware' => ['hotlinkProtection']], function () {
        Route::get('down/{query}', 'IndexController@download')->where('query', '.*')->name('download');
        Route::get('view/{query}', 'IndexController@view')->where('query', '.*')->name('view');
    });
    
    Route::post('password', 'IndexController@handlePassword')->name('password');
    Route::get('thumb/{id}/size/{size}', 'IndexController@thumb')->name('thumb');
    Route::get('thumb/{id}/{width}/{height}', 'IndexController@thumbCrop')->name('thumb_crop');
    // 搜索
    Route::get('search', 'IndexController@search')->name('search')->middleware('checkAuth', 'throttle:10,2');
    Route::get('search/file/{id}', 'IndexController@searchShow')->name('search.show')->middleware('checkAuth');
});

Route::view('message', config('olaindex.theme') . 'message')->name('message');
// 图床
Route::get('image', 'ManageController@uploadImage')->name('image')->middleware('checkImage');
Route::post('image/upload', 'ManageController@uploadImage')
    ->name('image.upload')->middleware('throttle:10,2', 'checkImage');
Route::get('file/delete/{sign}', 'ManageController@deleteItem')->name('delete');

// 后台设置管理
Route::get('new_admin', 'Admin\\HomeController@index')->middleware('auth:admin');
Route::group(['prefix' => 'admin'], function () {
    Route::get('new_login', 'Admin\\AuthController@showLoginForm')->name('admin.new_login');
    Route::post('new_login', 'Admin\\AuthController@login');

    Route::post('new_logout', 'Admin\\AuthController@logout')->name('admin.new_logout')->middleware('auth:admin');
    Route::view('login', config('olaindex.theme') . 'admin.login')->name('admin.login');
    Route::middleware('checkAuth')->group(function () {
        Route::view('/', config('olaindex.theme') . 'admin.basic')->name('admin.basic');
        Route::view('show', config('olaindex.theme') . 'admin.show')->name('admin.show');
        Route::view('profile', config('olaindex.theme') . 'admin.profile')->name('admin.profile.show');
    });

    Route::post('login', 'AdminController@login')->name('admin.login');
    Route::post('logout', 'AdminController@logout')->name('admin.logout');
    // 基础设置
    Route::post('/', 'AdminController@basic')->name('admin.basic.post');
    Route::any('bind', 'AdminController@bind')->name('admin.bind');
    Route::post('show', 'AdminController@settings')->name('admin.settings');
    Route::post('profile', 'AdminController@profile')->name('admin.profile.post');
    Route::any('clear', 'AdminController@clear')->name('admin.cache.clear');
    Route::any('refresh', 'AdminController@refresh')->name('admin.cache.refresh');
    // 文件夹操作
    Route::group(['prefix' => 'folder'], function () {
        Route::post('lock', 'ManageController@lockFolder')->name('admin.lock');
        Route::post('create', 'ManageController@createFolder')->name('admin.folder.create');
    });
    // 文件操作
    Route::prefix('file')->group(function () {
        Route::get('/', 'ManageController@uploadFile')->name('admin.file');
        Route::post('upload', 'ManageController@uploadFile')
            ->name('admin.file.upload')->middleware('throttle:10,2');
        Route::any('add', 'ManageController@createFile')
            ->name('admin.file.create');
        Route::any('edit/{id}', 'ManageController@updateFile')
            ->name('admin.file.update');
        Route::view('other', config('olaindex.theme') . 'admin.other')
            ->name('admin.other');
        Route::post('copy', 'ManageController@copyItem')->name('admin.copy');
        Route::post('move', 'ManageController@moveItem')->name('admin.move');
        Route::post('file/path2id', 'ManageController@pathToItemId')
            ->name('admin.path2id');
        Route::post('share', 'ManageController@createShareLink')
            ->name('admin.share');
        Route::post('share/delete', 'ManageController@deleteShareLink')
            ->name('admin.share.delete');
    });
    // 离线上传
    Route::post('url/upload', 'ManageController@uploadUrl')->name('admin.url.upload');
});

if (Str::contains(config('app.url'), ['localhost', 'dev.ningkai.wang'])) {
    Route::get('about', function () {
        $url = 'https://raw.githubusercontent.com/WangNingkai/OLAINDEX/master/README.md';
        $content = getFileContent($url);
        $markdown = markdown2Html($content);

        return response()->view(
            config('olaindex.theme') . 'about',
            compact('markdown')
        );
    });
}
