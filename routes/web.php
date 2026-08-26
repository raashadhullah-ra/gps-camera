<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/auth/login');
});

// Load Admin Routes
require __DIR__ . '/admin.php';
