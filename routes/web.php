<?php

use App\Http\Controllers\BuyerController;
use App\Http\Controllers\ClientTicketController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\AdminTicketController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Models\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');

// });

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/s', function () {
    return view('search');
});

// Route::get('/search',  [DivisionController::class, 'search'])->name('search');
Route::get('/user-of-supervisor', function () {
    return view('backend.users.superindex');
})->name('superindex');

//New registration ajax route

Route::get('/get-company-designation/{divisionId}', [CompanyController::class, 'getCompanyDesignations'])->name('get_company_designation');


Route::get('/get-department/{company_id}', [CompanyController::class, 'getdepartments'])->name('get_departments');


Route::middleware('auth')->group(function () {
    // Route::get('/check', function () {
    //     return "Hello world";
    // });

    Route::get('/home', function () {
        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'General') {
            return redirect()->route('client.tickets.dashboard');
        } elseif ($user && $user->role && $user->role->name === 'Support') {
            return redirect()->route('support.tickets.dashboard');
        }
        return view('backend.home');
    })->name('home');


    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');


    //user

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    // User Import Routes
    Route::get('/users/import', [\App\Http\Controllers\UserImportController::class, 'showImportForm'])->name('users.import.form');
    Route::post('/users/import', [\App\Http\Controllers\UserImportController::class, 'import'])->name('users.import.process');
    Route::get('/users/import/template', [\App\Http\Controllers\UserImportController::class, 'downloadTemplate'])->name('users.import.template');

    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get(
        '/users/{user}/edit',
        [UserController::class, 'edit']
    )->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/online-user', [UserController::class, 'onlineuserlist'])->name('online_user');

    Route::post('/users/{user}/users_active', [UserController::class, 'user_active'])->name('users.active');

    Route::post('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');

    // User Assignments (for Support users)
    Route::get('/users/{user}/assignments', [UserController::class, 'editAssignments'])->name('users.assignments.edit');
    Route::put('/users/{user}/assignments', [UserController::class, 'updateAssignments'])->name('users.assignments.update');

    // companies
    Route::resource('companies', CompanyController::class);

    //departments
    Route::resource('departments', DepartmentController::class);

    // designations
    Route::resource('designations', DesignationController::class);

    ///buyers
    Route::get('/buyers', [BuyerController::class, 'index'])->name('buyers.index');
    Route::get('/buyers/create', [BuyerController::class, 'create'])->name('buyers.create');
    Route::post('/buyers', [BuyerController::class, 'store'])->name('buyers.store');
    Route::get('/buyers/{buyer}', [BuyerController::class, 'show'])->name('buyers.show');
    Route::get('/buyers/{buyer}/edit', [BuyerController::class, 'edit'])->name('buyers.edit');
    Route::put('/buyers/{buyer}', [BuyerController::class, 'update'])->name('buyers.update');
    Route::delete('/buyers/{buyer}', [BuyerController::class, 'destroy'])->name('buyers.destroy');
    Route::post('/buyers/{buyer}/buyers_active', [BuyerController::class, 'buyer_active'])->name('buyers.active');
    Route::get('/get_buyer', [BuyerController::class, 'get_buyer'])->name('get_buyer');

    ///suppliers
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    Route::post('/suppliers/{supplier}/suppliers_active', [SupplierController::class, 'supplier_active'])->name('suppliers.active');
    Route::get('/get_supplier', [SupplierController::class, 'get_supplier'])->name('get_supplier');

    // Client Tickets - Only for General and Client roles
    Route::prefix('my-tickets')->name('client.tickets.')->middleware('ticket.role:General,Client')->group(function () {
        Route::get('/', [ClientTicketController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports', [ClientTicketController::class, 'reports'])->name('reports');
        Route::get('/reports/download', [ClientTicketController::class, 'downloadReport'])->name('reports.download');
        Route::get('/create', [ClientTicketController::class, 'create'])->name('create');
        Route::post('/', [ClientTicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [ClientTicketController::class, 'show'])->name('show');
        Route::get('/{ticket}/edit', [ClientTicketController::class, 'edit'])->name('edit');
        Route::put('/{ticket}', [ClientTicketController::class, 'update'])->name('update');
        Route::delete('/{ticket}', [ClientTicketController::class, 'destroy'])->name('destroy');
        Route::get('/{ticket}/review', [ClientTicketController::class, 'showReviewForm'])->name('review');
        Route::post('/{ticket}/review', [ClientTicketController::class, 'submitReview'])->name('submit-review');
        // AJAX endpoints
        Route::get('/ajax/stats', [ClientTicketController::class, 'getStats'])->name('ajax.stats');
        Route::get('/ajax/tickets', [ClientTicketController::class, 'getTicketsAjax'])->name('ajax.tickets');
    });

    // Support Team Tickets - Only for Support and Supervisor roles
    Route::prefix('support-tickets')->name('support.tickets.')->middleware('ticket.role:Support,Supervisor')->group(function () {
        Route::get('/', [SupportTicketController::class, 'dashboard'])->name('dashboard');
        Route::get('/my-tickets', [SupportTicketController::class, 'myTickets'])->name('my-tickets');
        Route::get('/reports', [SupportTicketController::class, 'reports'])->name('reports');
        Route::get('/reports/download', [SupportTicketController::class, 'downloadReport'])->name('reports.download');
        Route::get('/{ticket}', [SupportTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/receive', [SupportTicketController::class, 'receive'])->name('receive');
        Route::post('/{ticket}/forward', [SupportTicketController::class, 'forward'])->name('forward');
        Route::post('/{ticket}/update-status', [SupportTicketController::class, 'updateStatus'])->name('update-status');
        Route::post('/{ticket}/complete', [SupportTicketController::class, 'complete'])->name('complete');
        // AJAX endpoints
        Route::get('/ajax/stats', [SupportTicketController::class, 'getStatsAjax'])->name('ajax.stats');
        Route::get('/ajax/dashboard-tickets', [SupportTicketController::class, 'getDashboardTickets'])->name('ajax.dashboard-tickets');
        Route::get('/ajax/my-tickets', [SupportTicketController::class, 'getMyTickets'])->name('ajax.my-tickets');
    });

    // Admin Tickets - Only for Admin role
    Route::prefix('admin-tickets')->name('admin.tickets.')->middleware('ticket.role:Admin')->group(function () {
        Route::get('/', [AdminTicketController::class, 'dashboard'])->name('dashboard');
        Route::get('/all', [AdminTicketController::class, 'index'])->name('index');
        Route::get('/reports', [AdminTicketController::class, 'reports'])->name('reports');
        Route::get('/ajax/reports', [AdminTicketController::class, 'getReportsData'])->name('ajax.reports');
        Route::get('/{ticket}', [AdminTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/assign', [AdminTicketController::class, 'assignSupport'])->name('assign');
        Route::delete('/{ticket}', [AdminTicketController::class, 'destroy'])->name('destroy');
        // AJAX endpoints
        Route::get('/ajax/dashboard-stats', [AdminTicketController::class, 'getDashboardStats'])->name('ajax.dashboard-stats');
        Route::get('/ajax/index-tickets', [AdminTicketController::class, 'getIndexTickets'])->name('ajax.index-tickets');
    });

    //New


});



























Route::get('/read/{notification}', [NotificationController::class, 'read'])->name('notification.read');


require __DIR__ . '/auth.php';

//php artisan command

Route::get('/foo', function () {
    Artisan::call('storage:link');
});

Route::get('/cleareverything', function () {
    $clearcache = Artisan::call('cache:clear');
    echo "Cache cleared<br>";

    $clearview = Artisan::call('view:clear');
    echo "View cleared<br>";

    $clearconfig = Artisan::call('config:cache');
    echo "Config cleared<br>";
});

Route::get('/key =', function () {
    $key =  Artisan::call('key:generate');
    echo "key:generate<br>";
});

Route::get('/migrate', function () {
    $migrate = Artisan::call('migrate');
    echo "migration create<br>";
});

Route::get('/migrate-fresh', function () {
    $fresh = Artisan::call('migrate:fresh --seed');
    echo "migrate:fresh --seed create<br>";
});

Route::get('/optimize', function () {
    $optimize = Artisan::call('optimize:clear');
    echo "optimize cleared<br>";
});
Route::get('/route-clear', function () {
    $route_clear = Artisan::call('route:clear');
    echo "route cleared<br>";
});

Route::get('/route-cache', function () {
    $route_cache = Artisan::call('route:cache');
    echo "route cache<br>";
});

Route::get('/updateapp', function () {
    $dump_autoload = Artisan::call('dump-autoload');
    echo 'dump-autoload complete';
});
