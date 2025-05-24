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
        // Replace ?? operator with isset() ternary for PHP 5.6 compatibility
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
        return 'unknown';
    }

    public function generateCsrfToken() {
        if (session_status() === PHP_SESSION_NONE) {
            if (!headers_sent()) {
                session_start();
            }
        }

        if(!empty($_SESSION['csrf_token'])) {
            return $_SESSION['csrf_token'];
        }

        // Generate random token compatible with PHP 5.6 and 7.4+
        $token = $this->generateRandomBytes(32);
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time(); 

        return $token;
    }

    public function verifyCsrfToken($csrfToken) {
        if (session_status() === PHP_SESSION_NONE) {
            if (!headers_sent()) {
                session_start();
            }
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

    /**
     * Generate cryptographically secure random bytes compatible with PHP 5.6+
     * @param int $length The number of bytes to generate
     * @return string Hexadecimal representation of random bytes
     */
    private function generateRandomBytes($length) {
        // PHP 7.0+ method (preferred)
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes($length));
        }
        
        // PHP 5.6+ with OpenSSL extension
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length, $strong);
            if ($bytes !== false && $strong === true) {
                return bin2hex($bytes);
            }
        }
        
        // PHP 5.6+ with mcrypt extension (deprecated in PHP 7.1 but still works)
        if (function_exists('mcrypt_create_iv')) {
            return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
        }
        
        // Fallback for systems without crypto extensions (less secure)
        return $this->generatePseudoRandomBytes($length);
    }

    /**
     * Fallback pseudo-random generator for systems without crypto extensions
     * @param int $length The number of bytes to generate
     * @return string Hexadecimal representation of pseudo-random bytes
     */
    private function generatePseudoRandomBytes($length) {
        $bytes = '';
        for ($i = 0; $i < $length; $i++) {
            $bytes .= chr(mt_rand(0, 255));
        }
        return bin2hex($bytes);
    }
}