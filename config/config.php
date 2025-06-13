<?php
// Define ROOT_DIR_PATH first, as BASE_URL calculation might use it.
if (!defined('ROOT_DIR_PATH')) {
    // This assumes config.php is in 'PROJECT_ROOT/config/'
    // ROOT_DIR_PATH will point to 'PROJECT_ROOT/'
    define('ROOT_DIR_PATH', realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR);
}

if (!defined('BASE_URL')) {
    $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';

    if ($serverName === 'crm.purewood.in') {
        // Live server base URL override
        define('BASE_URL', 'https://crm.purewood.in/');
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST']; // e.g., 'localhost'

        // Get the server's document root, normalize slashes, and remove any trailing slash.
        // Example: 'C:/xampp/htdocs'
        $documentRoot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\'));

        // Get the project's root directory path on the file system, normalize slashes, and remove any trailing slash.
        // Example: 'C:/xampp/htdocs/php_erp/purewood'
        $projectRootFileSystemPath = str_replace('\\', '/', rtrim(ROOT_DIR_PATH, '/\\'));

        // Calculate the web path of the project relative to the document root.
        // This should result in something like '/php_erp/purewood' if the project is in a subfolder,
        // or an empty string if the project is directly in the document root.
        $webRelativePath = str_replace($documentRoot, '', $projectRootFileSystemPath);

        // Ensure the path starts with a slash (e.g., '/' or '/php_erp/purewood').
        // This handles cases where the project is at the document root (webRelativePath is empty)
        // or in a subdirectory.
        $finalWebPath = '/' . ltrim($webRelativePath, '/');
        
        // Construct the base URL.
        // This ensures it ends with a single trailing slash.
        // Example: 'http://localhost/' or 'http://localhost/php_erp/purewood/'
        $baseUrl = rtrim($protocol . $host . rtrim($finalWebPath, '/'), '/') . '/';

        define('BASE_URL', $baseUrl);
    }
}

// The original ROOT_DIR_PATH definition is kept (and moved above).
// if (!defined('ROOT_DIR_PATH')) {
//    define('ROOT_DIR_PATH', realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR);
// }

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Detect environment based on server name or other criteria
        $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';

        if (in_array($serverName, ['localhost', '127.0.0.1'])) {
            // Local environment settings
            $this->host = "localhost";
            $this->db_name = "crm.purewood";
            $this->username = "root";
            $this->password = "";
        } else {
            // Live environment settings
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
            // Set PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Database connection error: " . $exception->getMessage();
            exit;
        }
        return $this->conn;
    }
}
?>
