<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    redirect('/admin/login');
});
