<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('name', 'like', '%Edyth Nienow%')
    ->orWhere('first_name', 'like', '%Edyth%')
    ->orWhere('last_name', 'like', '%Nienow%')
    ->first();

if ($user) {
    echo "Email: " . $user->email . "\n";
    // Check if password matches 'password' which is typical for Laravel seeded users
    if (Hash::check('password', $user->password)) {
        echo "Password: password\n";
    } else {
        echo "Password: (unknown/hashed)\n";
    }
} else {
    echo "User not found.\n";
}
