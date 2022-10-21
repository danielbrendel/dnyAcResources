<?php

/*
    AC-Resources (dnyAcResources) developed by Daniel Brendel

    (C) 2022 by Daniel Brendel

    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;

Route::get('/', [MainController::class, 'index']);
Route::get('/imprint', [MainController::class, 'imprint']);
Route::get('/tos', [MainController::class, 'tos']);
Route::post('/register', [MainController::class, 'register']);
Route::get('/confirm', [MainController::class, 'confirm']);
Route::get('/reset', [MainController::class, 'viewReset']);
Route::post('/recover', [MainController::class, 'recover']);
Route::get('/resend/{id}', [MainController::class, 'resend']);
Route::post('/reset', [MainController::class, 'reset']);
Route::post('/login', [MainController::class, 'login']);
Route::any('/logout', [MainController::class, 'logout']);

Route::get('/submit', [ItemController::class, 'viewSubmit']);
Route::post('/submit', [ItemController::class, 'submit']);
Route::post('/item/query', [ItemController::class, 'query']);
Route::post('/item/query/user', [ItemController::class, 'queryUser']);
Route::post('/item/query/reviews', [ItemController::class, 'queryReviews']);
Route::get('/view/{item}', [ItemController::class, 'view']);
Route::get('/item/{id}/edit', [ItemController::class, 'viewEdit']);
Route::post('/item/{id}/edit', [ItemController::class, 'edit']);
Route::get('/item/{id}/report', [ItemController::class, 'reportItem']);
Route::get('/item/{id}/delete', [ItemController::class, 'deleteItem']);
Route::post('/item/{id}/review/send', [ItemController::class, 'createReview']);
Route::get('/review/{id}/report', [ItemController::class, 'reportReview']);
Route::get('/review/{id}/delete', [ItemController::class, 'deleteReview']);

Route::get('/user/{ident}', [MemberController::class, 'showProfile']);
Route::get('/user/{ident}/report', [MemberController::class, 'reportUser']);
Route::get('/profile', [MemberController::class, 'profile']);
Route::post('/profile/save', [MemberController::class, 'saveProfile']);
Route::get('/member/name/valid', [MemberController::class, 'usernameValidity']);
Route::any('/user/query/reviews', [MemberController::class, 'queryReviews']);
Route::any('/user/account/delete', [MemberController::class, 'deleteUserAccount']);

Route::get('/notifications/list', [NotificationController::class, 'list']);
Route::get('/notifications/fetch', [NotificationController::class, 'fetch']);
Route::get('/notifications/seen', [NotificationController::class, 'seen']);

Route::get('/admin', [AdminController::class, 'index']);
Route::post('/admin/about/save', [AdminController::class, 'saveAboutContent']);
Route::post('/admin/logo/save', [AdminController::class, 'saveLogo']);
Route::post('/admin/cookieconsent/save', [AdminController::class, 'saveCookieConsent']);
Route::post('/admin/reginfo/save', [AdminController::class, 'saveRegInfo']);
Route::post('/admin/tos/save', [AdminController::class, 'saveTosContent']);
Route::post('/admin/imprint/save', [AdminController::class, 'saveImprintContent']);
Route::post('/admin/headcode/save', [AdminController::class, 'saveHeadCode']);
Route::get('/admin/user/details', [AdminController::class, 'userDetails']);
Route::post('/admin/user/save', [AdminController::class, 'userSave']);
Route::any('/admin/user/{id}/resetpw', [AdminController::class, 'userResetPassword']);
Route::any('/admin/user/{id}/delete', [AdminController::class, 'userDelete']);
Route::any('/admin/user/{id}/lock', [AdminController::class, 'lockUser']);
Route::any('/admin/user/{id}/safe', [AdminController::class, 'setUserSafe']);
Route::any('/admin/approval/{id}/approve', [AdminController::class, 'approveItem']);
Route::any('/admin/approval/{id}/decline', [AdminController::class, 'declineItem']);
Route::get('/admin/entity/lock', [AdminController::class, 'lockEntity']);
Route::get('/admin/entity/delete', [AdminController::class, 'deleteEntity']);
Route::get('/admin/entity/safe', [AdminController::class, 'setSafeEntity']);
Route::post('/admin/newsletter', [AdminController::class, 'newsletter']);

Route::any('/cronjob/twitter/{password}', [MainController::class, 'cronjob_twitter']);
Route::any('/cronjob/newsletter/{password}', [MainController::class, 'cronjob_newsletter']);
