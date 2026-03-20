<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SecondaryUserController;
use App\Http\Controllers\MarketingMaterialController;
use App\Http\Controllers\SavedLinkController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Models\Industry;
use Illuminate\Support\Facades\Mail;

Route::get('/', [LoginController::class, 'showLoginForm']);
// Guest routes (for non-logged in users)
Route::middleware('guest')->group(function () {

	Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
	Route::post('/login', [LoginController::class, 'login']);
	Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
	Route::post('/register', [RegisterController::class, 'register']);
	Route::get('/user-agreement', [RegisterController::class, 'userAgreement'])->name('user-agreement');
	Route::get('/register/secondary/{token}', [RegisterController::class, 'showSecondaryRegistration'])->name('register.secondary');
	Route::post('/register/secondary/{token}', [RegisterController::class, 'registerSecondary'])->name('register.secondary.submit');

	// Password Reset Routes
	Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
	Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
	Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
	Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

	Route::get('/register/secondary/{token}', [SecondaryUserController::class, 'showRegistrationForm'])->name('register.secondary');
	Route::post('/register/secondary', [SecondaryUserController::class, 'completeRegistration'])->name('register.secondary.complete');
	Route::post('/account/reactivation-request', [LoginController::class, 'requestReactivation'])->name('account.reactivation.request');

});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
	Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
	Route::middleware(['auth'])->group(function () {
		Route::resource('saved-links', SavedLinkController::class);
	});

});

// Admin routes
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
	Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
	Route::get('/users', [AdminController::class, 'index'])->name('admin.users.index');
	Route::post('/users/{user}/toggle-approval', [AdminController::class, 'toggleApproval'])->name('admin.users.toggle-approval');
	Route::post('/users/{user}/toggle-active', [AdminController::class, 'toggleActive'])->name('admin.users.toggle-active');
	Route::post('/users/{user}/update', [AdminController::class, 'update'])->name('admin.users.update');
	Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
	Route::get('/users/export', [AdminController::class, 'export'])->name('admin.users.export');

	Route::resource('industries', IndustryController::class)->names([
		'index' => 'admin.industries.index',
		'store' => 'admin.industries.store',
		'update' => 'admin.industries.update',
		'destroy' => 'admin.industries.destroy'
	]);

	Route::resource('dealers', DealerController::class)->names([
		'index' => 'admin.dealers.index',
		'store' => 'admin.dealers.store',
		'update' => 'admin.dealers.update',
		'destroy' => 'admin.dealers.destroy'
	]);

	Route::resource('manufacturers', ManufacturerController::class)->names([
		'index' => 'admin.manufacturers.index',
		'store' => 'admin.manufacturers.store',
		'update' => 'admin.manufacturers.update',
		'destroy' => 'admin.manufacturers.destroy'
	]);

	Route::resource('announcements', AnnouncementController::class)->names([
		'index' => 'admin.announcements.index',
		'store' => 'admin.announcements.store',
		'update' => 'admin.announcements.update',
		'destroy' => 'admin.announcements.destroy'
	]);
	Route::post('announcements/eligible-users', [AnnouncementController::class, 'getEligibleUsers'])->name('admin.announcements.eligible-users');
	Route::post('announcements/reorder', [AnnouncementController::class, 'reorder'])->name('admin.announcements.reorder');

	// Media routes
	Route::get('/media', [MediaController::class, 'index'])->name('admin.media.index');
	Route::get('/media/files', [MediaController::class, 'getMediaFiles']);
	Route::get('/media/groups', [MediaController::class, 'getGroups'])->name('admin.media.groups');
	Route::post('/media/groups', [MediaController::class, 'createGroup']);
	Route::put('/media/groups/{group}', [MediaController::class, 'updateGroup']);
	Route::delete('/media/groups/{group}', [MediaController::class, 'deleteGroup'])->name('media.groups.delete');
	Route::get('/media/{media}', [MediaController::class, 'show']);
	Route::post('/media/remove-from-group', [MediaController::class, 'removeFromGroup']);
	Route::post('/media/group-info', [MediaController::class, 'getGroupInfo'])->name('media.group.info');

	// Media Bulk operations
	Route::post('/media', [MediaController::class, 'store']);
	Route::post('media/bulk-info', [MediaController::class, 'getBulkInfo'])->name('admin.media.bulk-info');
	Route::post('/media/bulk-update', [MediaController::class, 'bulkUpdate']);
	Route::delete('/media/bulk-delete', [MediaController::class, 'bulkDelete']);
	// Route::post('/media/{media}/comment', [MediaController::class, 'addComment']);
	Route::post('/media/{media}/toggle-like', [MediaController::class, 'toggleLike']);
	Route::post('/media/{media}/view', [MediaController::class, 'recordView']);
	Route::get('/media/{media}/engagement', [MediaController::class, 'getMediaEngagement']);
	// Route::delete('/media/comments/{comment}', [MediaController::class, 'deleteComment']);
	Route::post('/media/assigned-users', [MediaController::class, 'getAssignedUsers'])->name('api.assigned-users');

	// Marketing Material Routes
	Route::get('/marketing-materials', [MarketingMaterialController::class, 'index'])->name('admin.marketing-materials.index');
	Route::get('/marketing-materials/files', [MarketingMaterialController::class, 'getMaterialFiles']);
	Route::get('/marketing-materials/groups', [MarketingMaterialController::class, 'getGroups'])->name('admin.marketing-materials.groups');
	Route::post('/marketing-materials/groups', [MarketingMaterialController::class, 'createGroup']);
	Route::put('/marketing-materials/groups/{group}', [MarketingMaterialController::class, 'updateGroup'])->name('admin.marketing-materials.groups.update');
	Route::delete('/marketing-materials/groups/{group}', [MarketingMaterialController::class, 'deleteGroup'])->name('marketing-materials.groups.delete');
	Route::get('/marketing-materials/{material}', [MarketingMaterialController::class, 'show']);
	Route::post('/marketing-materials/remove-from-group', [MarketingMaterialController::class, 'removeFromGroup']);
	Route::post('/marketing-materials/group-info', [MarketingMaterialController::class, 'getGroupInfo'])->name('marketing-materials.group.info');

	//  Marketing Material Bulk operations
	Route::post('/marketing-materials', [MarketingMaterialController::class, 'store']);
	Route::post('marketing-materials/bulk-info', [MarketingMaterialController::class, 'getBulkInfo'])->name('admin.marketing-materials.bulk-info');
	Route::post('/marketing-materials/bulk-update', [MarketingMaterialController::class, 'bulkUpdate']);
	Route::delete('/marketing-materials/bulk-delete', [MarketingMaterialController::class, 'bulkDelete']);
	Route::post('/marketing-materials/{material}/toggle-like', [MarketingMaterialController::class, 'toggleLike']);
	Route::post('/marketing-materials/{material}/view', [MarketingMaterialController::class, 'recordView']);
	Route::get('/marketing-materials/{material}/engagement', [MarketingMaterialController::class, 'getMaterialEngagement']);
	Route::post('/marketing-materials/assigned-users', [MarketingMaterialController::class, 'getAssignedUsers'])->name('api.marketing-materials.assigned-users');

});


// Super Admin only routes
Route::middleware(['auth', 'superadmin'])->prefix('admin')->group(function () {
    Route::post('/create-admin', [SuperAdminController::class, 'createAdmin'])->name('admin.create-admin');
    Route::post('/admins/{user}/toggle-status', [SuperAdminController::class, 'toggleAdminStatus'])->name('admin.toggle-admin-status');
    Route::delete('/admins/{user}', [SuperAdminController::class, 'deleteAdmin'])->name('admin.delete-admin');
});

// Customer Routes
Route::middleware(['auth'])->prefix('customer')->group(function () {
	Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
	Route::get('/medias', [CustomerController::class, 'medias'])->name('customer.medias');
	Route::get('/media/{media}/engagement', [CustomerController::class, 'getMediaEngagement']);
	Route::post('/media/{media}/toggle-like', [CustomerController::class, 'toggleLike']);
	// Route::post('/media/{media}/comment', [CustomerController::class, 'addComment']);
	Route::post('/media/{media}/view', [CustomerController::class, 'recordView']);
	Route::get('/managed-users', [CustomerController::class, 'managedUsers'])->name('customer.managed-users');
	Route::delete('/remove-secondary-user/{user}', [CustomerController::class, 'removeSecondaryUser'])->name('customer.remove-secondary');
	Route::post('/invite-secondary-user', [SecondaryUserController::class, 'inviteSecondaryUser'])->name('customer.invite-secondary');
	Route::post('/toggle-secondary-user-status/{user}', [SecondaryUserController::class, 'toggleStatus'])->name('customer.toggle-secondary-status');
	Route::delete('/remove-secondary-user/{user}', [SecondaryUserController::class, 'removeUser'])->name('customer.remove-secondary');
	Route::get('/profile', [CustomerController::class, 'edit'])->name('profile.edit');
	Route::put('/profile', [CustomerController::class, 'update'])->name('profile.update');

	Route::get('/marketing-materials', [CustomerController::class, 'marketingMaterials'])->name('customer.marketing-materials');
	Route::get('/marketing-material/{material}/engagement', [CustomerController::class, 'getMarketingMaterialEngagement']);
	Route::post('/marketing-material/{material}/toggle-like', [CustomerController::class, 'toggleMarketingMaterialLike']);
	Route::post('/marketing-material/{material}/view', [CustomerController::class, 'recordMarketingMaterialView']);

	//secondary user management routes
	Route::put('/managed-users/{user}', [CustomerController::class, 'updateSecondaryUser'])->name('customer.secondary-users.update');
	Route::post('/managed-users/{user}/toggle-status', [CustomerController::class, 'toggleSecondaryUserStatus'])->name('customer.secondary-users.toggle-status');
	Route::delete('/managed-users/{user}', [CustomerController::class, 'destroySecondaryUser'])->name('customer.secondary-users.destroy');

	Route::post('/file-request', [CustomerController::class, 'fileRequest'])->name('customer.file-request');
	

});

// API routes
Route::get('/api/industries/search', function (Request $request) {
	$query = Industry::select('id', 'name')
		->orderBy('name', 'asc');
	if ($request->filled('q')) {
		$query->where('name', 'like', '%' . $request->input('q') . '%');
	}
	if ($request->filled('selected')) {
		$query->whereNotIn('id', (array) $request->input('selected'));
	}
	$industries = $query->take(10)->get();
	return response()->json($industries);
});

Route::prefix('api')->group(function () {
	Route::get('countries/search', [LocationController::class, 'searchCountries']);
	Route::get('states/search', [LocationController::class, 'searchStates']);
	Route::get('cities/search', [LocationController::class, 'searchCities']);
	Route::get('dealers/search', [DealerController::class, 'search']);
	Route::get('/states/{country}', [LocationController::class, 'getStates']);
	Route::get('/cities/{state}', [LocationController::class, 'getCities']);
	Route::get('/manufacturers/search', [ManufacturerController::class, 'searchManufacturers']);
});



// Route::get('/send-email', function () {
//     try {
//         Mail::raw('This is a test email body', function ($message) {
//             $message->to('admin@example.com')
//                 ->subject('Test Email from Laravel');
//         });

//         return 'Email sent successfully!';
//     } catch (\Exception $e) {
//         \Log::error('Mail Error: ' . $e->getMessage());
//         return 'Failed to send email: ' . $e->getMessage() . ' | Line: ' . $e->getLine() . ' | File: ' . $e->getFile();
//     }
// });
