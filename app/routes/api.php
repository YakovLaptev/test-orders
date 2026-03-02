<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')
    ->group(base_path('routes/api-v1.php'));
