<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\InfrastructureController;
use App\Http\Controllers\BreakdownLogController;
use App\Http\Controllers\UserController;
use App\Models\Entity;
use App\Models\Infrastructure;
use App\Models\BreakdownLog;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Publik / Landing Page (Portal Utama)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $infrastructures = Infrastructure::all();

    $entities = Entity::with('infrastructures')->get()->map(function($entity) {
        $entity->Infrastructure_stats_by_cat = [
            'equipment' => $entity->infrastructures->where('category', 'equipment')->groupBy('type')->map(fn($items) => [
                'available' => $items->where('status', 'available')->count(), // Menggunakan count() kembali
                'breakdown' => $items->where('status', 'breakdown')->count(),
            ]),
            'facility' => $entity->infrastructures->where('category', 'facility')->groupBy('type')->map(fn($items) => [
                'available' => $items->where('status', 'available')->count(),
                'breakdown' => $items->where('status', 'breakdown')->count(),
            ]),
            'utility' => $entity->infrastructures->where('category', 'utility')->groupBy('type')->map(fn($items) => [
                'available' => $items->where('status', 'available')->count(),
                'breakdown' => $items->where('status', 'breakdown')->count(),
            ]),
        ];
        return $entity;
    });

    $breakdowns = BreakdownLog::with('infrastructure.entity')
        ->where('repair_status', '!=', 'resolved')
        ->orderBy('created_at', 'desc')
        ->get();

    return view('welcome', compact('infrastructures', 'entities', 'breakdowns'));
})->name('home');

/*
|--------------------------------------------------------------------------
| Rute Terproteksi (Hanya Bisa Diakses Setelah Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 1. Dashboard Utama Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Grup Utama Admin (Operasional & Master Data)
    Route::prefix('admin')->name('admin.')->group(function () {

        /* --- AKSES SEMUA LEVEL (Superadmin & Operator Cabang) --- */
        
        // Fitur Hapus Semua Data (Wajib di atas resource)
        Route::delete('infrastructures/delete-all', [InfrastructureController::class, 'deleteAll'])->name('infrastructures.deleteAll');
        
        Route::resource('infrastructures', InfrastructureController::class);
        Route::resource('breakdowns', BreakdownLogController::class);

        /* --- KHUSUS AKSES SUPERADMIN (Administrator Pusat) --- */
        Route::middleware(['superadmin'])->group(function () {
            // Manajemen Bagian/Terminal
            Route::resource('entities', EntityController::class);
            // Manajemen Akun Pegawai
            Route::resource('users', UserController::class);
        });
        
    });

    // 3. Manajemen Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
