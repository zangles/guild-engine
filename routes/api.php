<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DkpController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRsvpController;
use App\Http\Controllers\GuildController;
use App\Http\Controllers\GuildMemberController;
use App\Http\Controllers\GuildRoleController;
use Illuminate\Support\Facades\Route;

// Auth
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
    Route::post('logout',   [AuthController::class, 'logout'])->middleware('auth:api');
});

// Public guild endpoints
Route::get('guilds',          [GuildController::class, 'index']);
Route::get('guilds/{guild}',  [GuildController::class, 'show']);

// Authenticated endpoints
Route::middleware('auth:api')->group(function () {

    // Guilds
    Route::post('guilds',           [GuildController::class, 'store']);
    Route::put('guilds/{guild}',    [GuildController::class, 'update']);

    // Guild Members
    Route::prefix('guilds/{guild}')->group(function () {
        Route::get('members',                                       [GuildMemberController::class, 'index']);
        Route::post('join',                                         [GuildMemberController::class, 'join']);
        Route::post('invite',                                       [GuildMemberController::class, 'invite']);
        Route::post('members/{member}/approve',                     [GuildMemberController::class, 'approve']);
        Route::post('members/{member}/reject',                      [GuildMemberController::class, 'reject']);
        Route::post('members/{member}/kick',                        [GuildMemberController::class, 'kick']);
        Route::patch('members/{member}/role',                       [GuildMemberController::class, 'updateRole']);
        Route::post('transfer-leadership',                          [GuildMemberController::class, 'transferLeadership']);

        // Guild Roles
        Route::get('roles',           [GuildRoleController::class, 'index']);
        Route::post('roles',          [GuildRoleController::class, 'store']);
        Route::put('roles/{role}',    [GuildRoleController::class, 'update']);

        // Events
        Route::get('events',                                        [EventController::class, 'index']);
        Route::post('events',                                       [EventController::class, 'store']);
        Route::post('events/{event}/cancel',                        [EventController::class, 'cancel']);
        Route::post('events/{event}/attendance',                    [EventController::class, 'registerAttendance']);
        Route::put('events/{event}/rsvp',                           [EventRsvpController::class, 'upsert']);

        // DKP
        Route::get('members/{member}/dkp/balance',                  [DkpController::class, 'balance']);
        Route::get('members/{member}/dkp/history',                  [DkpController::class, 'history']);
        Route::post('members/{member}/dkp/grant',                   [DkpController::class, 'grant']);
        Route::post('members/{member}/dkp/deduct',                  [DkpController::class, 'deduct']);

        // Donations
        Route::get('donations',                                     [DonationController::class, 'index']);
        Route::get('donations/history',                             [DonationController::class, 'history']);
        Route::post('donations',                                    [DonationController::class, 'store']);
        Route::patch('donations/{donation}/review',                 [DonationController::class, 'review']);

        // Audit Log
        Route::get('audit-log',                                     [AuditLogController::class, 'index']);
    });
});
