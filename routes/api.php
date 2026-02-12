<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/members', [DashboardController::class, 'getMembers']);
Route::get('/categories', [DashboardController::class, 'getCategories']);
Route::post('/admin/login', [DashboardController::class, 'adminLogin']);
Route::post('/score/update', [DashboardController::class, 'updateScore']);
Route::post('/category/add', [DashboardController::class, 'addCategory']);
Route::post('/category/delete', [DashboardController::class, 'deleteCategory']);
Route::post('/member/update', [DashboardController::class, 'updateMemberProfile']);