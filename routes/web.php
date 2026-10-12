<?php

use App\Http\Controllers\AdminArticleController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\LookbookController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminHomeSectionController;
use App\Http\Controllers\AdminLookController;
use App\Http\Controllers\AdminOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/{category}', [CatalogController::class, 'category'])->name('catalog.category');
Route::get('/product/{product}', [CatalogController::class, 'show'])->name('product.show');

Route::get('/article', [ArticleController::class, 'index'])->name('article');
Route::get('/article/{article:slug}', [ArticleController::class, 'show'])->name('article.show');

Route::get('/lookbook', [LookbookController::class, 'index'])->name('lookbook');
Route::get('/lookbook/{look:slug}', [LookbookController::class, 'show'])->name('lookbook.show');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');

Route::get('/information', [InformationController::class, 'index'])->name('information');
Route::get('/information/{slug}', [InformationController::class, 'show'])->name('information.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', [CustomerAuthController::class, 'showVerifyNotice'])->name('verification.notice');
    Route::post('/email/resend', [CustomerAuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', [CustomerAuthController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/orders', [OrderController::class, 'index'])->middleware('verified')->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->middleware('verified')->name('orders.show');

    Route::get('/checkout', [CheckoutController::class, 'index'])->middleware('verified')->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('verified')->name('checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->middleware('verified')->name('checkout.success');

    Route::get('/wilayah/provinces', [CheckoutController::class, 'provinces'])->name('checkout.provinces');
    Route::get('/wilayah/cities', [CheckoutController::class, 'cities'])->name('checkout.cities');
    Route::get('/wilayah/districts', [CheckoutController::class, 'districts'])->name('checkout.districts');
    Route::get('/checkout/cost', [CheckoutController::class, 'cost'])->middleware('verified')->name('checkout.cost');

    Route::get('/payment/return/{order}', [PaymentController::class, 'returnPage'])->middleware('verified')->name('payment.return');
    Route::get('/payment/status/{order}', [PaymentController::class, 'status'])->middleware('verified')->name('payment.status');
    Route::get('/payment/rechoose/{order}', [PaymentController::class, 'rechoose'])->middleware('verified')->name('payment.rechoose');
});

Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');

Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login']);
});

Route::middleware(AdminMiddleware::class)->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}/print', [AdminOrderController::class, 'print'])->name('orders.print');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');

    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::post('/articles', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}/edit', [AdminArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [AdminArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');

    Route::get('/looks', [AdminLookController::class, 'index'])->name('looks.index');
    Route::post('/looks', [AdminLookController::class, 'store'])->name('looks.store');
    Route::get('/looks/{look}/edit', [AdminLookController::class, 'edit'])->name('looks.edit');
    Route::put('/looks/{look}', [AdminLookController::class, 'update'])->name('looks.update');
    Route::delete('/looks/{look}', [AdminLookController::class, 'destroy'])->name('looks.destroy');

    Route::get('/home-sections', [AdminHomeSectionController::class, 'index'])->name('home-sections.index');
    Route::put('/home-sections/{section}', [AdminHomeSectionController::class, 'update'])->name('home-sections.update');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});