<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Assuming user 'admin' exists for test
$user = User::where('username', 'admin')->first();
if ($user) {
    Auth::login($user);
    echo "Logged in as: " . Auth::user()->username . "\n";
    echo "Auth Identifier Name: " . Auth::user()->getAuthIdentifierName() . "\n";
    echo "Auth ID: " . Auth::id() . "\n";
    echo "Key: " . Auth::user()->getKey() . "\n";
} else {
    echo "Admin user not found.\n";
}
