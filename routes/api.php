<?php

use App\Http\Controllers\Api\V1\AnalyticsApiController;
use App\Http\Controllers\Api\V1\AppointmentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\ContractController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\LeadController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\QuoteController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - V1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public authentication routes
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Public services list
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{service}', [ServiceController::class, 'show']);

    // Protected API Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Companies
        Route::apiResource('companies', CompanyController::class)->only(['index', 'show', 'update']);

        // Customers
        Route::apiResource('customers', CustomerController::class);

        // Leads
        Route::apiResource('leads', LeadController::class);

        // Quotes
        Route::apiResource('quotes', QuoteController::class);

        // Invoices
        Route::apiResource('invoices', InvoiceController::class);

        // Projects
        Route::apiResource('projects', ProjectController::class);

        // Appointments
        Route::apiResource('appointments', AppointmentController::class);

        // Contracts
        Route::apiResource('contracts', ContractController::class);
        Route::post('contracts/{contract}/sign', [ContractController::class, 'sign']);

        // Webhooks
        Route::apiResource('webhooks', WebhookController::class);

        // Analytics & BI
        Route::get('analytics/metrics', [AnalyticsApiController::class, 'metrics']);
        Route::get('analytics/business-intelligence', [AnalyticsApiController::class, 'businessIntelligence']);
    });
});
