<?php
declare(strict_types=1);

namespace RMQPHP\App\Schema;

use Exception;
use PDO;

/**
 * Database wrapper
 */
class Database
{
    /**
     * @var string  Database host name
     */
    protected $host = 'localhost';

    /**
     * @var string  Database name
     */
    protected $database_name = 'rmq_db';

    /**
     * @var string  Database user
     */
    protected $username = 'root';

    /**
     * @var string  Database password
     */
    protected $password = '';

    /**
     * static Database instance
     * 
     * @var Database
     */
    private static $instance;

    /**
     * @var PDO
     */
    private $conn;

    /**
     * @return Database
     */
    public static function instance() : self {
        self::$instance = isset(self::$instance) ? self::$instance : new Database();

        return self::$instance;
    }

    /**
     * Create PDO connection
     */
    public function connection() : PDO {
        if ($this->conn === null) {
            try {
                $conn = new PDO("mysql:host=$this->host;dbname=$this->database_name", $this->username, $this->password);

                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $this->setConn($conn);
            } catch (\PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }

        return $this->getConn();
    }

    /**
     * @return PDO
     */
    public function getConn(): PDO {
        return $this->conn;
    }

    /**
     * @param PDO $conn
     */
    public function setConn(PDO $conn): void {
        $this->conn = $conn;
    }
}