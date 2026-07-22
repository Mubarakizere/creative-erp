<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\SearchController;

auth()->loginUsingId(1);
$user = auth()->user();

echo "\n--- Global Search Tests ---\n\n";

$controller = app(SearchController::class);

$queries = [
    'INV-',     // Should match Journal references
    'Sales',    // Should match Chart of Accounts
    'JRN-',     // Should match Journal Number
    'REF-',     // Should match Ledger reference
];

foreach ($queries as $q) {
    echo "Searching for '$q'...\n";
    $request = Request::create('/admin/search', 'GET', ['q' => $q]);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    $response = $controller->index($request);
    $data = json_decode($response->getContent(), true);
    
    $found = false;
    foreach (['journals', 'chart_of_accounts', 'general_ledger'] as $key) {
        if (!empty($data[$key])) {
            echo "   Found " . count($data[$key]) . " items in $key.\n";
            $found = true;
        }
    }
    if (!$found) {
        echo "   [FAIL] No accounting items found.\n";
    }
}

echo "\nTesting permissions...\n";
// Create a temporary user with no roles
$guest = User::factory()->create(['company_id' => 1]);
auth()->login($guest);

$request = Request::create('/admin/search', 'GET', ['q' => 'Sales']);
$request->setUserResolver(function () use ($guest) {
    return $guest;
});
$response = $controller->index($request);
$data = json_decode($response->getContent(), true);

$hasAccess = false;
foreach (['journals', 'chart_of_accounts', 'general_ledger'] as $key) {
    if (!empty($data[$key])) {
        $hasAccess = true;
    }
}

if (!$hasAccess) {
    echo "   [PASS] User without permissions cannot view accounting search results.\n";
} else {
    echo "   [FAIL] User without permissions saw accounting search results!\n";
}

// Cleanup
$guest->delete();

echo "\nDone.\n";
