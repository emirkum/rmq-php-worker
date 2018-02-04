<?php
declare(strict_types=1);

namespace RMQPHP\App\Entity\Schema;

use RMQPHP\App\Entity\Transaction;
use RMQPHP\App\Schema\Database;

/**
 * Class TransactionManager
 * @package RMQPHP\App\Entity\Schema
 */
class TransactionManager {

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var Database
     */
    private $db;

    /**
     * TransactionManager constructor.
     *
     * @param Transaction $transaction
     * @param Database $db
     */
    public function __construct(Transaction $transaction, Database $db) {
        $this->transaction = $transaction;
        $this->db = $db->connection();
    }

    /**
     * @return Transaction
     */
    public function saveNew() : Transaction {
        if ($this->transaction->getAmount() == 0 || $this->transaction->getCurrency() == "") return $this->transaction;

        $sql_insert = "INSERT INTO transactions (transaction_id, amount, currency) VALUES (?, ?, ?)";

        $stmt_insert = $this->db->prepare($sql_insert);

        $stmt_insert->execute(array(
                uniqid(),
                $this->transaction->getAmount(),
                $this->transaction->getCurrency(),
            )
        );

        $this->transaction->setId(intval($this->db->lastInsertId()));

        return $this->transaction;
    }
}