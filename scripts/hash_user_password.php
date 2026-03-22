<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$email = $argv[1] ?? null;
if (!$email) {
    echo "Usage: php scripts/hash_user_password.php user@example.com\n";
    exit(1);
}

$user = DB::table('users')->where('email', $email)->first();
if (!$user) {
    echo "User not found: $email\n";
    exit(1);
}

$pwd = $user->password;
// detect bcrypt/argon hashed values
if (preg_match('/^\$2[ayb]\$|^\$argon2/', $pwd)) {
    echo "Password for {$email} looks already hashed. No change made.\n";
    echo "Stored value: $pwd\n";
    exit(0);
}

echo "Plain password detected for {$email} — hashing now...\n";
$new = Hash::make($pwd);
DB::table('users')->where('id', $user->id)->update(['password' => $new]);

echo "Password updated to bcrypt for user id {$user->id}.\n";
echo "New stored value: $new\n";
