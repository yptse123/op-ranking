<?php

class PM_Common 
{
   public function getClientIp() {
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
        // PHP 5.6 compatible random token generation
        if (function_exists('random_bytes')) {
            // PHP 7.0+ method
            return bin2hex(random_bytes(32));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            // Fallback for PHP 5.6 with OpenSSL
            return bin2hex(openssl_random_pseudo_bytes(32));
        } elseif (function_exists('mcrypt_create_iv')) {
            // Fallback for PHP 5.6 with mcrypt (deprecated but still available)
            return bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
        } else {
            // Last resort fallback (less secure but works)
            return hash('sha256', uniqid(mt_rand(), true) . microtime(true) . $_SERVER['REMOTE_ADDR']);
        }
    }

    public function verifyCsrfToken($csrfToken) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($csrfToken) || empty($_SESSION['csrf_token'])) {
            return false;
        }

        if ($csrfToken !== $_SESSION['csrf_token']) {
            return false;
        }

        // $tokenTime = $_SESSION['csrf_token_time'] ?? 0;
        // if ((time() - $tokenTime) > 3600) {
        //     unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
        //     return false;
        // }

        return true;
    }
}