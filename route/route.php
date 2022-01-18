<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('/api/codes', 'admin_login/Login/getCode');
Route::post('/api/sing_in', 'admin_login/Login/go_in');

Route::get('/about', 'index/index/about');
Route::get('/contact', 'index/index/contact');
Route::get('/news', 'index/index/news');
Route::get('/hot', 'index/index/hotList');
Route::get('/tag_news/:tag_id', 'index/index/tag_news');
Route::get('/news-detail/:id', 'index/index/news_detail');
Route::get('/product-list/[:name]/[:type]/[:last]', 'index/index/product_list');
Route::get('/product-detail/:names', 'index/index/product_detail');
Route::post('/feedback','index/index/feedback');
Route::get('/search/[:keyword]','index/index/search');


