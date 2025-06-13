<?php

if (!defined('ROOT_DIR_PATH')) {
    define('ROOT_DIR_PATH', realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR);
}

if (!defined('BASE_URL')) {
    $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';

    if ($serverName === 'crm.purewood.in') {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $documentRoot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\'));
        $projectRootFileSystemPath = str_replace('\\', '/', rtrim(ROOT_DIR_PATH, '/\\'));
        $webRelativePath = str_replace($documentRoot, '', $projectRootFileSystemPath);
        $finalWebPath = '/' . ltrim($webRelativePath, '/');
        $baseUrl = rtrim($protocol . $host . rtrim($finalWebPath, '/'), '/') . '/';
        define('BASE_URL', $baseUrl);
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $documentRoot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\'));
        $projectRootFileSystemPath = str_replace('\\', '/', rtrim(ROOT_DIR_PATH, '/\\'));
        $webRelativePath = str_replace($documentRoot, '', $projectRootFileSystemPath);
        $finalWebPath = '/' . ltrim($webRelativePath, '/');
        $baseUrl = rtrim($protocol . $host . rtrim($finalWebPath, '/'), '/') . '/';
        define('BASE_URL', $baseUrl);
    }
}

if (!defined('ROOT_DIR_PATH')) {
    define('ROOT_DIR_PATH', realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR);
}
if (!defined('ROOT_DIR_PATH')) {
    define('ROOT_DIR_PATH', realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR);
}

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';

        if (in_array($serverName, ['localhost', '127.0.0.1'])) {
            $this->host = "localhost";
            $this->db_name = "crm.purewood";
            $this->username = "root";
            $this->password = "";
        } else {
            $this->host = "127.0.0.1:3306";
            $this->db_name = "u404997496_crm_purewood";
            $this->username = "u404997496_crn_purewood";
            $this->password = "Purewood@2025#";
        }
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                                 $this->username,
                                 $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Database connection error: " . $exception->getMessage();
            exit;
        }
        return $this->conn;
    }
}