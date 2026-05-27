<?php
namespace App\Utils;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT {
    private static $secret;

    private static function ensureInit() {
        if (!self::$secret) {
            self::$secret = $_ENV['JWT_SECRET'];
        }
    }

    public static function encode($payload) {
        self::ensureInit();
        $payload['exp'] = time() + (int)$_ENV['JWT_EXPIRE'];
        return FirebaseJWT::encode($payload, self::$secret, 'HS256');
    }

    public static function decode($token) {
        self::ensureInit();
        try {
            return FirebaseJWT::decode($token, new Key(self::$secret, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }
}