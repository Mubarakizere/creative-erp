<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\Finance\StatementService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CustomerStatementController extends Controller
{
    use AuthorizesRequests;

    protected StatementService $statementService;

    public function __construct(StatementService $statementService)
    {
        $this->statementService = $statementService;
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);
        
        $statement = $this->statementService->generateCustomerStatement($client);
        return response()->json($statement);
    }
}
