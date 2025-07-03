<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ClientController,
    LoanController,
    CollateralController,
    BrandController,
    UserController,
    PaymentController,
    HomeController,
    CommentController,
    Auth\LoginController,
    Auth\RegisterController
};
Route::get('/test', function () {
    return 'This is a test route';
});
// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/loans/filter', [LoanController::class, 'filter'])->name('loans.filter');
Route::get('/payments/filter', [PaymentController::class, 'filter'])->name('payments.filter');

Route::get('/activities', [UserController::class, 'userActivities'])->name('users.userActivities');
// // Dashboard
Route::get('/', [HomeController::class, 'index'])->name('dashboard');
Route::post('/loans/{loan}/UpdateColor', [LoanController::class, 'UpdateColor'])->name('loans.setColor');
Route::post('/loans/{loan}/UpdateNote', [LoanController::class, 'UpdateNote'])->name('loans.setNote');
// Group routes that require authentication
Route::middleware(['auth'])->group(function () {
    // Resources
    Route::get('/loans/blocked', [LoanController::class, 'blocked'])->name('loans.blocked');
    Route::get('/loans/toblock', [LoanController::class, 'toblock'])->name('loans.toblock');
    Route::resource('clients', ClientController::class);
    Route::resource('loans', LoanController::class);
    Route::resource('collaterals', CollateralController::class);
    Route::post('/register', [UserController::class, 'register']);
    Route::patch('/collaterals/{id}/update-pass', [CollateralController::class, 'updatePass'])->name('collaterals.updatePass');
    Route::patch('/collaterals/{id}/update-email', [CollateralController::class, 'updateEmail'])->name('collaterals.updateEmail');
    
    Route::post('/loans/{loan_id}/comments', [CommentController::class, 'store'])->name('loans.comments.store');
    Route::post('/loans/{loan_id}/payInterest', [LoanController::class, 'payInterest'])->name('loans.payInterest');
    Route::post('/loans/{loan_id}/payLoanPrincipal', [LoanController::class, 'payLoanPrincipal'])->name('loans.payLoanPrincipal');
    Route::post('/loans/{loan_id}/changeStatus', [LoanController::class, 'changeStatus'])->name('loans.changeStatus');

    // Brands API
    Route::get('/api/brands/{type}', [BrandController::class, 'getByType']);
});

// Admin Routes with a separate middleware to check for admin ro

Route::middleware(['admin'])->group(function () {

    Route::get('/register', [UserController::class, 'registerForm'])->name('register');

    
    //Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');

 
    
    
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
   
    //Route::post('/activities', [UserController::class, 'activities'])->name('users.activities');
    Route::get('/users/reset/{id}', [UserController::class, 'resetPassword'])->name('users.reset');
    
    Route::get('/users/disable/{id}', [UserController::class, 'disableUser'])->name('users.disable');
    Route::get('/users/enable/{id}', [UserController::class, 'enableUser'])->name('users.enable');
});

// //Admin only routes
// Route::middleware(['CheckRole'])->group(function () {
//     Route::get('/users', [UserController::class, 'index'])->name('users.index');
//     Route::get('/users/reset/{id}', [UserController::class, 'resetPassword'])->name('users.reset');
//     Route::get('/users/disable/{id}', [UserController::class, 'disableUser'])->name('users.disable');
//     Route::get('/users/enable/{id}', [UserController::class, 'enableUser'])->name('users.enable');
//     // Add any other admin routes here
// });
// // Admin only routes
// if (Auth::check() /*&& Auth::user()->role_id == 1*/) {
//     dd(Auth::user());
//     Route::get('/users', [UserController::class, 'index'])->name('users.index');
//     Route::get('/users/reset/{id}', [UserController::class, 'resetPassword'])->name('users.reset');
//     Route::get('/users/disable/{id}', [UserController::class, 'disableUser'])->name('users.disable');
//     Route::get('/users/enable/{id}', [UserController::class, 'enableUser'])->name('users.enable');
//     // Add any other admin routes here
// }
// else
// {
//     dd(Auth::user());
//     Route::get('/users', function () {
//         return redirect('home')->with('error', "You don't have admin access.");
//     });
//     Route::get('/users/reset/{id}', function () {
//         return redirect('home')->with('error', "You don't have admin access.");
//     });
//     Route::get('/users/disable/{id}', function () {
//         return redirect('home')->with('error', "You don't have admin access.");
//     });
//     Route::get('/users/enable/{id}', function () {
//         return redirect('home')->with('error', "You don't have admin access.");
//     });
// }
