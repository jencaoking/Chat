<?php
namespace App\Utils;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT {
    private static $secret;

    public static function init() {
        self::$secret = $_ENV['JWT_SECRET'];
    }

    public static function encode($payload) {
        $payload['exp'] = time() + (int)$_ENV['JWT_EXPIRE'];
        return FirebaseJWT::encode($payload, self::$secret, 'HS256');
    }

    public static function decode($token) {
        try {
            return FirebaseJWT::decode($token, new Key(self::$secret, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }
}