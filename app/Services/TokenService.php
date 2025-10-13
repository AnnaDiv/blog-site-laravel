<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenService {

    private string $key = 'none-of-your-business';
    private string $algo = 'HS256';

    public function generateToken(int $user_id): string {

        $payload = [
            'sub' => $user_id,
            'iat' => time(),
            'exp' => time() + (60 * 60) // 1 hour
        ];
        return JWT::encode($payload, $this->key, $this->algo);
    }

    public function validateToken(string $token): array|false {

        try {
            $decoded = JWT::decode($token, new Key($this->key, $this->algo));
            return (array) $decoded;
        } 
        catch (\Throwable $e) {
            return false;
        }
    }
}