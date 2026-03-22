<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$email = $argv[1] ?? 'wadabadie@gmail.com';
$user = DB::table('users')->where('email', $email)->first();
if (!$user) {
    echo "User not found: $email\n";
    exit(1);
}
echo "id: " . $user->id . PHP_EOL;
echo "email: " . $user->email . PHP_EOL;
echo "password: " . $user->password . PHP_EOL;
