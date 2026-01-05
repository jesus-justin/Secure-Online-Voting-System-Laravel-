<?php

use App\Http\Controllers\VotingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Landing page (accessible to everyone)
Route::get('/', function () {
    return view('pages.landing');
})->name('landing');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Home/Dashboard page
    Route::get('/home', function () {
        return view('pages.home', [
            'activeElections' => \App\Models\Election::where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->count(),
            'castVotes' => \App\Models\Vote::where('user_id', auth()->id())->count(),
            'completedElections' => \App\Models\Election::where('end_date', '<', now())->count(),
            'upcomingElections' => \App\Models\Election::where('start_date', '>', now())->count(),
            'elections' => \App\Models\Election::with('candidates', 'votes')
                ->orderBy('start_date', 'desc')
                ->get(),
        ]);
    })->name('home');

    // Voting routes
    Route::get('/elections', [VotingController::class, 'index'])->name('voting.index');
    Route::get('/elections/{election}', [VotingController::class, 'show'])->name('voting.show');
    Route::post('/elections/{election}/vote', [VotingController::class, 'vote'])
        ->middleware(['voted', 'election.active'])
        ->name('voting.vote');
    Route::get('/elections/{election}/success', [VotingController::class, 'success'])->name('voting.success');
    Route::get('/elections/{election}/results', [VotingController::class, 'results'])->name('voting.results');
    
    // Notifications
    Route::get('/notifications', function () {
        return view('notifications.index');
    })->name('notifications.index');
    
    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar');
        Route::get('/avatar/delete', [ProfileController::class, 'deleteAvatar'])->name('avatar.delete');
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Elections
        Route::get('/elections', [AdminController::class, 'elections'])->name('elections');
        Route::get('/elections/create', [AdminController::class, 'createElection'])->name('elections.create');
        Route::post('/elections', [AdminController::class, 'storeElection'])->name('elections.store');
        Route::get('/elections/{election}/edit', [AdminController::class, 'editElection'])->name('elections.edit');
        Route::put('/elections/{election}', [AdminController::class, 'updateElection'])->name('elections.update');
        Route::delete('/elections/{election}', [AdminController::class, 'deleteElection'])->name('elections.delete');
        Route::get('/elections/{election}/results', [AdminController::class, 'electionResults'])->name('elections.results');
        Route::post('/elections/{election}/detect-tampering', [AdminController::class, 'detectTampering'])->name('elections.detect-tampering');

        // Candidates
        Route::post('/elections/{election}/candidates', [AdminController::class, 'addCandidate'])->name('candidates.store');
        Route::put('/candidates/{candidate}', [AdminController::class, 'updateCandidate'])->name('candidates.update');
        Route::delete('/candidates/{candidate}', [AdminController::class, 'deleteCandidate'])->name('candidates.delete');

        // Users
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users/{user}/verify', [AdminController::class, 'verifyUser'])->name('users.verify');
        Route::post('/users/{user}/unverify', [AdminController::class, 'unverifyUser'])->name('users.unverify');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');

        // Logs
        Route::get('/logs/{election?}', [AdminController::class, 'voteLogs'])->name('logs');
    });
});
