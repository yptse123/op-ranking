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

    public function generateCsrfToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time(); 

        return $token;
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