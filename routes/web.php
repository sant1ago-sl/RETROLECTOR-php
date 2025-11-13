<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\ResenaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ReadingAnalyticsController;
use App\Http\Controllers\ReadingClubController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rutas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showUserLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'userLogin']);
    Route::get('/admin-login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin-login', [AuthController::class, 'adminLogin']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rutas de cambio de idioma y tema
Route::get('/language/{locale}', [LanguageController::class, 'change'])->name('language.change');
Route::get('/theme/{theme}', [ThemeController::class, 'change'])->name('theme.change');

// Rutas del catálogo de libros (públicas)
Route::prefix('books')->name('books.')->group(function () {
    Route::get('/catalog', [LibroController::class, 'catalog'])->name('catalog');
    Route::get('/advanced-search', [LibroController::class, 'advancedSearch'])->name('advanced-search');
    Route::get('/popular', [LibroController::class, 'popularBooks'])->name('popular');
    Route::get('/recent', [LibroController::class, 'recentBooks'])->name('recent');
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations');
    Route::get('/reading-clubs', [ReadingClubController::class, 'index'])->name('reading-clubs');
    Route::get('/category/{categoria_id}', [LibroController::class, 'booksByCategory'])->name('by-category');
    Route::get('/author/{autor_id}', [LibroController::class, 'booksByAuthor'])->name('by-author');
    Route::get('/export', [LibroController::class, 'exportCatalog'])->name('export');
    Route::get('/search-api', [LibroController::class, 'searchApi'])->name('search-api');
    
    // Rutas que requieren autenticación
Route::middleware('auth')->group(function () {
        Route::get('/{id}', [LibroController::class, 'show'])->name('show');
        Route::get('/{id}/request-loan', [LibroController::class, 'requestLoan'])->name('request-loan');
        Route::get('/{id}/reserve', [LibroController::class, 'reserveBook'])->name('reserve-book');
        Route::get('/{id}/purchase', [LibroController::class, 'purchaseBook'])->name('purchase');
        Route::post('/{id}/process-purchase', [LibroController::class, 'processPurchase'])->name('process-purchase');
        Route::post('/{id}/prestamo', [LibroController::class, 'createPrestamo'])->name('prestamo');
        Route::post('/{id}/purchase/fisica', [LibroController::class, 'purchaseFisica'])->name('purchase.fisica');
        Route::post('/{id}/purchase/virtual', [LibroController::class, 'purchaseVirtual'])->name('purchase.virtual');
        Route::get('/purchase/download/{compra_id}', [LibroController::class, 'downloadPurchase'])->name('purchase.download');
        Route::get('/purchase-success/{id}', [LibroController::class, 'purchaseSuccess'])->name('purchase-success');
        Route::get('/{id}/read', [LibroController::class, 'readBook'])->name('read');
    });
});

// Eliminar libro (usuario)
Route::delete('/books/{id}/delete', [\App\Http\Controllers\LibroController::class, 'deleteBook'])->name('books.delete')->middleware('auth');

// Rutas de usuario autenticado
Route::middleware(['auth', 'cliente'])->prefix('user')->name('user.')->group(function () {
    // Dashboard y perfil
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('update-profile');
    Route::put('/change-password', [UserController::class, 'changePassword'])->name('change-password');
    Route::get('/settings', [UserController::class, 'settings'])->name('settings');
    Route::put('/settings', [UserController::class, 'updateSettings'])->name('update-settings');
    Route::get('/export-data', [UserController::class, 'exportData'])->name('export-data');
    Route::delete('/delete-account', [UserController::class, 'deleteAccount'])->name('delete-account');
    
    // Préstamos
    Route::get('/loans', [UserController::class, 'loans'])->name('loans');
    Route::post('/loans/{id}/renew', [UserController::class, 'renewLoan'])->name('renew-loan');
    
    // Reservas
    Route::get('/reservations', [UserController::class, 'reservations'])->name('reservations');
    Route::post('/reservations/{id}/cancel', [UserController::class, 'cancelReservation'])->name('cancel-reservation');
    
    // Favoritos
    Route::get('/favorites', [UserController::class, 'favorites'])->name('favorites');
    Route::post('/favorites/{libro_id}/toggle', [UserController::class, 'toggleFavorite'])->name('toggle-favorite');
    
    // Historial
    Route::get('/history', [UserController::class, 'history'])->name('history');
    
    // Compras
    Route::get('/purchases', [UserController::class, 'purchases'])->name('purchases');
    Route::get('/virtual-books', [UserController::class, 'virtualBooks'])->name('virtual-books');
    Route::get('/virtual-books/{id}/read', [UserController::class, 'readVirtualBook'])->name('read-virtual-book');
    
    // Notificaciones
    Route::get('/notifications', [UserController::class, 'notifications'])->name('notifications');
    Route::get('/notifications/unread', [UserController::class, 'getUnreadNotifications'])->name('notifications.unread');
    Route::post('/notifications/{id}/mark-read', function($id) {
        $notificacion = \App\Models\Notificacion::where('usuario_id', auth()->id())->findOrFail($id);
        $notificacion->leida = 1;
        $notificacion->save();
        return back();
    })->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [UserController::class, 'markAllNotificationsRead'])->name('mark-all-notifications-read');
    
    // Mensajes
    Route::get('/messages', [UserController::class, 'messages'])->name('messages');
    Route::get('/messages/conversation/{usuario_id}', [UserController::class, 'viewConversation'])->name('view-conversation');
    Route::post('/messages/send', [UserController::class, 'sendMessage'])->name('send-message');
    Route::get('/messages/search-users', [UserController::class, 'searchUsers'])->name('search-users');
    
    // Reseñas
    Route::get('/reviews', [UserController::class, 'reviews'])->name('reviews');
    Route::post('/books/{libro_id}/review', [UserController::class, 'createReview'])->name('create-review');
    Route::put('/reviews/{id}', [UserController::class, 'editReview'])->name('edit-review');
    Route::delete('/reviews/{id}', [UserController::class, 'deleteReview'])->name('delete-review');
    
    // Análisis de lectura
    Route::get('/reading-analytics', [ReadingAnalyticsController::class, 'index'])->name('reading-analytics');
    Route::get('/export-reading-data', [ReadingAnalyticsController::class, 'exportData'])->name('export-reading-data');
    Route::get('/export-reading-pdf', [ReadingAnalyticsController::class, 'exportData'])->name('export-reading-pdf');
    Route::get('/export-reading-json', [ReadingAnalyticsController::class, 'exportData'])->name('export-reading-json');
    
    // Clubes de lectura
    Route::get('/clubs', [ReadingClubController::class, 'index'])->name('clubs.index');
    Route::get('/clubs/{id}', [ReadingClubController::class, 'show'])->name('clubs.show');
    Route::post('/clubs', [ReadingClubController::class, 'store'])->name('clubs.store');
    Route::post('/clubs/{id}/join', [ReadingClubController::class, 'join'])->name('clubs.join');
    Route::post('/clubs/{id}/leave', [ReadingClubController::class, 'leave'])->name('clubs.leave');
    Route::get('/clubs/{id}/manage', [ReadingClubController::class, 'manage'])->name('clubs.manage');
    Route::post('/clubs/{club_id}/members/{member_id}/approve', [ReadingClubController::class, 'approveMember'])->name('clubs.approve-member');
    Route::post('/clubs/{club_id}/members/{member_id}/reject', [ReadingClubController::class, 'rejectMember'])->name('clubs.reject-member');
    Route::post('/clubs/{club_id}/discussions', [ReadingClubController::class, 'createDiscussion'])->name('clubs.create-discussion');
    Route::post('/clubs/{club_id}/books', [ReadingClubController::class, 'addBook'])->name('clubs.add-book');
    Route::get('/clubs/category/{categoria_id}', [ReadingClubController::class, 'byCategory'])->name('clubs.by-category');
    Route::get('/clubs/search', [ReadingClubController::class, 'search'])->name('clubs.search');
});

// Rutas de administrador
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Gestión de libros
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/', [AdminController::class, 'books'])->name('index');
        Route::get('/create', [AdminController::class, 'createBook'])->name('create');
        Route::post('/store', [AdminController::class, 'storeBook'])->name('store');
        Route::get('/{id}', [AdminController::class, 'showBook'])->name('show');
        Route::get('/{id}/edit', [AdminController::class, 'editBook'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'updateBook'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'deleteBook'])->name('delete');
        Route::post('/{id}/upload-image', [AdminController::class, 'uploadBookImage'])->name('upload-image');
        Route::post('/{id}/upload-pdf', [AdminController::class, 'uploadBookPdf'])->name('upload-pdf');
        Route::get('/export', [AdminController::class, 'exportBooks'])->name('export');
    });
    
    // Gestión de usuarios
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'users'])->name('index');
        Route::get('/{id}', [AdminController::class, 'showUser'])->name('show');
        Route::get('/{id}/edit', [AdminController::class, 'editUser'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'updateUser'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'deleteUser'])->name('delete');
        Route::post('/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('toggle-status');
        Route::get('/export', [AdminController::class, 'exportUsers'])->name('export');
    });
    
    // Gestión de préstamos
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/', [AdminController::class, 'loans'])->name('index');
        Route::get('/{id}', [AdminController::class, 'showLoan'])->name('show');
        Route::post('/{id}/approve', [AdminController::class, 'approveLoan'])->name('approve');
        Route::post('/{id}/reject', [AdminController::class, 'rejectLoan'])->name('reject');
        Route::post('/{id}/return', [AdminController::class, 'returnLoan'])->name('return');
        Route::post('/{id}/extend', [AdminController::class, 'extendLoan'])->name('extend');
        Route::delete('/{id}', [AdminController::class, 'deleteLoan'])->name('delete');
        Route::get('/export', [AdminController::class, 'exportLoans'])->name('export');
    });
    
    // Gestión de reservas
    Route::prefix('reservations')->name('reservations.')->group(function () {
        Route::get('/', [AdminController::class, 'reservations'])->name('index');
        Route::get('/{id}', [AdminController::class, 'showReservation'])->name('show');
        Route::post('/{id}/approve', [AdminController::class, 'approveReservation'])->name('approve');
        Route::post('/{id}/reject', [AdminController::class, 'rejectReservation'])->name('reject');
        Route::post('/{id}/complete', [AdminController::class, 'completeReservation'])->name('complete');
        Route::delete('/{id}', [AdminController::class, 'deleteReservation'])->name('delete');
        Route::get('/export', [AdminController::class, 'exportReservations'])->name('export');
    });
    
    // Gestión de categorías
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [AdminController::class, 'categories'])->name('index');
        Route::post('/store', [AdminController::class, 'storeCategory'])->name('store');
        Route::put('/{id}', [AdminController::class, 'updateCategory'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'deleteCategory'])->name('delete');
    });
    
    // Gestión de autores
    Route::prefix('authors')->name('authors.')->group(function () {
        Route::get('/', [AdminController::class, 'authors'])->name('index');
        Route::get('/create', [AdminController::class, 'createAuthor'])->name('create');
        Route::post('/store', [AdminController::class, 'storeAuthor'])->name('store');
        Route::get('/{id}', [AdminController::class, 'showAuthor'])->name('show');
        Route::get('/{id}/edit', [AdminController::class, 'editAuthor'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'updateAuthor'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'deleteAuthor'])->name('delete');
    });
    
    // Gestión de reseñas
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [AdminController::class, 'reviews'])->name('index');
        Route::get('/pending', [AdminController::class, 'pendingReviews'])->name('pending');
        Route::post('/{id}/approve', [AdminController::class, 'approveReview'])->name('approve');
        Route::post('/{id}/reject', [AdminController::class, 'rejectReview'])->name('reject');
        Route::delete('/{id}', [AdminController::class, 'deleteReview'])->name('delete');
    });
    
    // Gestión de notificaciones
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [AdminController::class, 'notifications'])->name('index');
        Route::get('/unread', [AdminController::class, 'getUnreadNotifications'])->name('get-unread');
        Route::post('/send', [AdminController::class, 'sendNotification'])->name('send');
        Route::post('/send-bulk', [AdminController::class, 'sendBulkNotification'])->name('send-bulk');
        Route::delete('/{id}', [AdminController::class, 'deleteNotification'])->name('delete');
        Route::post('/cleanup', [AdminController::class, 'cleanupNotifications'])->name('cleanup');
    });
    
    // Gestión de mensajes
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [AdminController::class, 'messages'])->name('index');
        Route::get('/conversation/{usuario_id}', [AdminController::class, 'viewConversation'])->name('view-conversation');
        Route::post('/send', [AdminController::class, 'sendMessage'])->name('send');
        Route::delete('/{id}', [AdminController::class, 'deleteMessage'])->name('delete');
        Route::post('/cleanup', [AdminController::class, 'cleanupMessages'])->name('cleanup');
    });
    
    // Reportes y estadísticas
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AdminController::class, 'reports'])->name('index');
        Route::get('/loans', [AdminController::class, 'loanReport'])->name('loans');
        Route::get('/reservations', [AdminController::class, 'reservationReport'])->name('reservations');
        Route::get('/users', [AdminController::class, 'userReport'])->name('users');
        Route::get('/books', [AdminController::class, 'bookReport'])->name('books');
        Route::get('/popular-books', [AdminController::class, 'popularBooksReport'])->name('popular-books');
        Route::get('/overdue-loans', [AdminController::class, 'overdueLoansReport'])->name('overdue-loans');
        Route::get('/export/{type}', [AdminController::class, 'exportReport'])->name('export');
    });
    
    // Configuración del sistema
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminController::class, 'settings'])->name('index');
        Route::put('/general', [AdminController::class, 'updateGeneralSettings'])->name('general');
        Route::put('/loan', [AdminController::class, 'updateLoanSettings'])->name('loan');
        Route::put('/notification', [AdminController::class, 'updateNotificationSettings'])->name('notification');
        Route::put('/backup', [AdminController::class, 'updateBackupSettings'])->name('backup');
});

    // Mantenimiento del sistema
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/', [AdminController::class, 'maintenance'])->name('index');
        Route::post('/cleanup-database', [AdminController::class, 'cleanupDatabase'])->name('cleanup-database');
        Route::post('/optimize-database', [AdminController::class, 'optimizeDatabase'])->name('optimize-database');
        Route::post('/backup-database', [AdminController::class, 'backupDatabase'])->name('backup-database');
        Route::post('/clear-cache', [AdminController::class, 'clearCache'])->name('clear-cache');
        Route::post('/clear-logs', [AdminController::class, 'clearLogs'])->name('clear-logs');
    });
});

// Rutas admin compras
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function() {
    Route::get('compras', [\App\Http\Controllers\CompraController::class, 'index'])->name('admin.compras.index');
});

// Rutas de API para funcionalidades AJAX
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    // Búsqueda de usuarios para mensajes
    Route::get('/search-users', [UserController::class, 'searchUsers'])->name('search-users');
    
    // Toggle favoritos
    Route::post('/toggle-favorite/{libro_id}', [UserController::class, 'toggleFavorite'])->name('toggle-favorite');
    
    // Marcar notificación como leída
    Route::post('/mark-notification-read/{id}', [UserController::class, 'markNotificationRead'])->name('mark-notification-read');
    
    // Enviar mensaje
    Route::post('/send-message', [UserController::class, 'sendMessage'])->name('send-message');
    
    // Búsqueda de libros en tiempo real
    Route::get('/search-books', [LibroController::class, 'searchApi'])->name('search-books');
    
    // Recomendaciones
    Route::prefix('recommendations')->name('recommendations.')->group(function () {
        Route::get('/', [RecommendationController::class, 'getRecommendations'])->name('get');
        Route::post('/preferences', [RecommendationController::class, 'updatePreferences'])->name('update-preferences');
        Route::get('/stats', [RecommendationController::class, 'getRecommendationStats'])->name('stats');
    });
    
    // Análisis de lectura
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/chart-data', [ReadingAnalyticsController::class, 'getChartData'])->name('chart-data');
    });
});

// Ruta para estadísticas en tiempo real (AJAX)
Route::get('/stats/realtime', [\App\Http\Controllers\HomeController::class, 'getStatsAjax'])->name('stats.realtime');

// Rutas de comandos Artisan (solo para desarrollo)
if (app()->environment('local')) {
    Route::get('/artisan/{command}', function ($command) {
        Artisan::call($command);
        return response()->json(['message' => 'Command executed successfully']);
    })->middleware('admin');
}

// Ruta de fallback para páginas no encontradas
Route::fallback(function () {
    return view('errors.404');
});

Route::get('/admin/authors/json', [App\Http\Controllers\AutorController::class, 'jsonList'])->name('admin.authors.json');
