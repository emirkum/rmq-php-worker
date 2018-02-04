<?php
declare(strict_types=1);

namespace RMQPHP\App\Entity;

use PDO;
use RMQPHP\App\Schema\Database;

class Balance {

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $value;

    /**
     * Balance constructor
     */
    public function __construct() {
        // code
    }

    /**
     * @return int
     */
    public function getId(): int {
        return intval($this->id);
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void {
        $this->id = intval($id);
    }

    /**
     * @return int
     */
    public function getValue(): int {
        return intval($this->value);
    }

    /**
     * @param int $value
     */
    public function setValue(int $value): void {
        $this->value = intval($value);
    }
}