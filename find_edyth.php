<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$clients = App\Models\Client::where('first_name', 'like', '%Edyth%')->orWhere('company_name', 'like', '%Edyth%')->get();
echo "Clients:\n";
foreach ($clients as $c) {
    echo $c->id . " | " . $c->name . " | " . $c->email . "\n";
}

$contacts = App\Models\Contact::where('first_name', 'like', '%Edyth%')->orWhere('last_name', 'like', '%Edyth%')->get();
echo "Contacts:\n";
foreach ($contacts as $c) {
    echo $c->id . " | " . $c->first_name . " " . $c->last_name . " | " . $c->email . "\n";
}
