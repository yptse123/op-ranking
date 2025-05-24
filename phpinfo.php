<?php
// Simple PHP version check
echo "<h1>PHP Version Information</h1>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Check for specific functions
echo "<h2>Function Availability:</h2>";
echo "<p>random_bytes(): " . (function_exists('random_bytes') ? 'Available' : 'Not Available') . "</p>";
echo "<p>openssl_random_pseudo_bytes(): " . (function_exists('openssl_random_pseudo_bytes') ? 'Available' : 'Not Available') . "</p>";
echo "<p>mcrypt_create_iv(): " . (function_exists('mcrypt_create_iv') ? 'Available' : 'Not Available') . "</p>";

// Check loaded extensions
echo "<h2>Relevant Extensions:</h2>";
echo "<p>OpenSSL: " . (extension_loaded('openssl') ? 'Loaded' : 'Not Loaded') . "</p>";
echo "<p>MySQLi: " . (extension_loaded('mysqli') ? 'Loaded' : 'Not Loaded') . "</p>";
echo "<p>PDO: " . (extension_loaded('pdo') ? 'Loaded' : 'Not Loaded') . "</p>";
echo "<p>PDO MySQL: " . (extension_loaded('pdo_mysql') ? 'Loaded' : 'Not Loaded') . "</p>";

echo "<hr>";
echo "<h2>Full PHP Configuration (phpinfo)</h2>";
echo "<p><em>Uncomment the line below to see full phpinfo() output:</em></p>";
// Uncomment the next line to see full PHP configuration
// phpinfo();
?>