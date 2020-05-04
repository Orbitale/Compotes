<?php

// Basically an extract of Symfony's NativePasswordEncoder's behavior.
// Note: if default encoder changes in the app, this file must be changed too.

use Symfony\Component\Security\Core\Encoder\NativePasswordEncoder;

$userPassword = $_SERVER['PASSWORD'] ?? $_ENV['PASSWORD'] ?? getenv('PASSWORD') ?: $argv[1] ?? null;

if (!$userPassword) {
    throw new RuntimeException('You must set a password.');
}

if (is_file($autoload = dirname(__DIR__).'/vendor/autoload.php')) {
    // Use native encoder if it exists.
    require_once $autoload;
    $encoder = new NativePasswordEncoder();
    echo $encoder->encodePassword($userPassword, null);

    return;
}

$opsLimit = max(4, defined('SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE') ? SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE : 4);
$memLimit = max(64 * 1024 * 1024, defined('SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE') ? SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE : 64 * 1024 * 1024);
$algo = (string) ($algo ?? (defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : (defined('PASSWORD_ARGON2I') ? PASSWORD_ARGON2I : PASSWORD_BCRYPT)));

echo password_hash(
    $userPassword,
    $algo,
    [
        'cost' => 13,
        'time_cost' => $opsLimit,
        'memory_cost' => $memLimit,
        'threads' => 1,
    ]
);
