<?php
declare(strict_types=1);

namespace RMQPHP\App\Worker;

use Exception;
use RMQPHP\App\Entity\Balance;
use RMQPHP\App\Entity\Schema\BalanceManager;
use RMQPHP\App\Entity\Schema\TransactionManager;
use RMQPHP\App\Entity\Transaction;
use RMQPHP\App\Schema\Database;

class Payment {

    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var Database
     */
    private $db;

    /**
     * @var bool
     */
    private $successful;

    public function __construct() {

    }

    /**
     * NOTE: Mock Transaction, TransactionManager, Balance and BalanceManager
     * TODO: Test if process was successful
     *
     * Attempt to process payment
     *
     * @return boolean Returns true if payment was successful, false otherwise
     */
    public function processPayment () : bool {
        if ($this->db == null) $this->db = Database::instance();

        if ($this->storeTransaction($this->db) && $this->storeBalance($this->db)) {

            $this->setSuccessful(true);

            return true;
        }

        return false;
    }

    /**
     * @param Database $db
     * @return bool
     */
    private function storeTransaction(Database $db) : bool {
        $transaction = new Transaction();
        $transaction->setAmount($this->getAmount());
        $transaction->setCurrency($this->getCurrency());

        $transactionManager = new TransactionManager($transaction, $db);
        $transaction = $transactionManager->saveNew();

        return $transaction->getId() != null && $transaction->getId() != 0;
    }

    /**
     * @param Database $db
     * @return bool
     */
    private function storeBalance(Database $db) : bool {
        $balanceManager = new BalanceManager(new Balance(), $db);
        $balance = $balanceManager->get();

        if ($balance->getId() == null || $balance->getId() == 0) {
            $amount = $this->getAmount() > 0 ? $this->getAmount() : 0;

            $balance->setValue($amount);

            $balance = $balanceManager->saveNew();
        } else {
            $v = $this->getAmount() + $balance->getValue() > 0 ? $this->getAmount() + $balance->getValue() : 0;
            $balance->setValue($v);

            $balance = $balanceManager->update();
        }

        return $balance->getId() != null && $balance->getId() != 0;
    }

    /**
     * @return int
     */
    public function getAmount(): int {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void {
        $this->currency = $currency;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool {
        return $this->successful;
    }

    /**
     * @param bool $successful
     */
    public function setSuccessful(bool $successful): void {
        $this->successful = $successful;
    }

    /**
     * @return Database
     */
    public function getDb(): Database {
        return $this->db;
    }

    /**
     * @param Database $db
     */
    public function setDb(Database $db): void {
        $this->db = $db;
    }
}