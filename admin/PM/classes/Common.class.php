<?php

class PM_Common
{
    public function getClientIp()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ipList[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
        return 'unknown';
    }

    public function generateCsrfToken()
    {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Generate token if it doesn't exist
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = $this->generateRandomString(64);
        }

        return $_SESSION['csrf_token'];
    }

    private function generateRandomString($length = 64)
    {
        // PHP 5.6 compatible random string generation
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function verifyCsrfToken($token)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }

        // Use hash_equals if available (PHP 5.6+), otherwise use string comparison
        if (function_exists('hash_equals')) {
            return hash_equals($_SESSION['csrf_token'], $token);
        } else {
            return $_SESSION['csrf_token'] === $token;
        }
    }
}