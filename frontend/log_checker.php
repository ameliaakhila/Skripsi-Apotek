<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ActivityLog;
use App\Models\User;

$logs = ActivityLog::all();
foreach ($logs as $log) {
    $user = User::where('id_user', $log->user_id)->first();
    echo "Log ID: " . $log->id . " | Action: " . $log->action . "\n";
    echo "User ID in log: " . $log->user_id . "\n";
    echo "User found: " . ($user ? $user->username : 'NO USER') . "\n";
    echo "-------------------\n";
}
