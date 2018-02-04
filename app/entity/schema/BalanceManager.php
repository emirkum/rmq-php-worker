<?php
declare(strict_types=1);

namespace RMQPHP\App\Entity\Schema;


use PDO;
use RMQPHP\App\Entity\Balance;
use RMQPHP\App\Schema\Database;

/**
 * TODO: Test database operations
 *
 * Class BalanceManager
 * @package RMQPHP\App\Entity\Schema
 */
class BalanceManager {

    /**
     * @var Balance
     */
    private $balance;
    /**
     * @var Database
     */
    private $db;

    /**
     * BalanceManager constructor.
     *
     * @param Balance $balance
     * @param Database $db
     */
    public function __construct(Balance $balance, Database $db) {
        $this->balance = $balance;
        $this->db = $db->connection();
    }

    /**
     * @return Balance
     */
    public function get() : Balance {
        $sql_select = "SELECT IFNULL(balance, 0) as balance, IFNULL(id, 0) as id FROM balance LIMIT 1 FOR UPDATE";

        $stmt_select = $this->db->prepare($sql_select);
        $stmt_select->execute();

        $res = $stmt_select->fetch(PDO::FETCH_OBJ);

        if ($res != null && isset($res->id)) {
            $this->balance->setId(intval($res->id));
            $this->balance->setValue(intval($res->balance));
        }

        return $this->balance;
    }

    /**
     * Save new balance
     *
     * @return Balance
     */
    public function saveNew() : Balance {
        if ($this->balance->getValue() == 0) return $this->balance;

        $sql_insert_balance = "INSERT INTO balance (balance) VALUES (?)";

        $stmt_insert_balance = $this->db->prepare($sql_insert_balance);

        $amount = $this->balance->getValue() < 0 ? 0 : $this->balance->getValue();

        $stmt_insert_balance->execute(array($amount));

        $this->balance->setId(intval($this->db->lastInsertId()));
        $this->balance->setValue($amount);

        return $this->balance;
    }

    /**
     * @return Balance
     */
    public function update() : Balance {
        $sql_select = "UPDATE balance SET balance = ? WHERE id = ?";

        $stmt_select = $this->db->prepare($sql_select);

        $v = $this->balance->getValue() < 0 ? 0 : $this->balance->getValue();

        $stmt_select->execute(array($v, $this->balance->getId()));

        $this->balance->setValue($v);

        return $this->balance;
    }
}

