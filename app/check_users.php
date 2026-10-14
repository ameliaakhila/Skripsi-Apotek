<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Http\Kernel::class);

// Boot the application
$app->boot();

// Get users
$users = \App\Models\User::all(['id_user', 'username', 'email', 'role']);

echo "Users in Database:\n";
echo "==================\n";
foreach ($users as $user) {
    echo "ID: {$user->id_user}, Username: {$user->username}, Email: {$user->email}, Role: {$user->role}\n";
}
