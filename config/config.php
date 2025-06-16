<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

if (!class_exists('AppConfig')) {
    class AppConfig {
        private static $localConfig = [
            'host' => 'localhost',
            'db' => 'crm.purewood',
            'user' => 'root',
            'pass' => '',
            'charset' => 'utf8mb4',
            'base_url' => 'http://localhost/php_erp/',
            'subdirectory' => 'php_erp'
        ];

        private static $liveConfig = [
            'host' => '127.0.0.1',
            'db' => 'u404997496_crm_purewood',
            'user' => 'u404997496_crn_purewood',
            'pass' => 'Purewood@2025#',
            'charset' => 'utf8mb4',
            'base_url' => 'https://crm.purewood.in/',
            'subdirectory' => ''
        ];

        public static function isLocalhost() {
            $host = $_SERVER['HTTP_HOST'] ?? '';
            // Explicitly check for live domain to avoid misdetection
            if ($host === 'crm.purewood.in') {
                return false;
            }
            // Check if host contains 'localhost' or '127.0.0.1' to handle ports and subdomains
            if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
                return true;
            }
            return false;
        }

        public static function getConfig() {
            return self::isLocalhost() ? self::$localConfig : self::$liveConfig;
        }

        public static function baseUrl() {
            $config = self::getConfig();
            return rtrim($config['base_url'], '/') . '/';
        }

        public static function getDsn() {
            $config = self::getConfig();
            return "mysql:host=" . $config['host'] . ";dbname=" . $config['db'] . ";charset=" . $config['charset'];
        }

        public static function getUser() {
            return self::getConfig()['user'];
        }

        public static function getPass() {
            return self::getConfig()['pass'];
        }
    }
}

$dsn = AppConfig::getDsn();
$user = AppConfig::getUser();
$pass = AppConfig::getPass();

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("DB Connection Failed: " . $e->getMessage());
}

// Define ROOT_PATH
define('ROOT_PATH', dirname(__FILE__, 2)); // Go up two levels from config.php to the project root

// Define BASE_PATH for absolute includes
define('BASE_PATH', ROOT_PATH);

if (!defined('ROOT_DIR_PATH')) {
    define('ROOT_DIR_PATH', ROOT_PATH . DIRECTORY_SEPARATOR);
}

// Define BASE_URL (if not already defined)
if (!defined('BASE_URL')) {
    define('BASE_URL', AppConfig::baseUrl());
}

// SMTP configuration for email sending
define('SMTP_HOST', 'smtp.hostinger.com');
define('SMTP_USERNAME', 'crm@thepurewood.com');
define('SMTP_PASSWORD', 'Rusty@2014');
define('SMTP_PORT', 465);
define('SMTP_FROM_EMAIL', 'crm@thepurewood.com');
define('SMTP_FROM_NAME', 'Purewood Admin');
