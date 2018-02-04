<?php
declare(strict_types=1);

namespace RMQPHP\Tests\Integration\Entity\Schema;

use PDO;
use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\TestCase;
use RMQPHP\App\Entity\Schema\TransactionManager;
use RMQPHP\App\Entity\Transaction;
use RMQPHP\App\Schema\Database;

class TransactionManagerTest extends TestCase {

    /**
     * @var Database
     */
    private $db;

    /**
     * @before
     */
    public function prepare() : void {
        $this->db = Database::instance();
        $this->db->setConn($this->getConnection()->getConnection());
    }

    /**
     * Returns the test database connection.
     *
     * @return Connection
     */
    protected function getConnection() : Connection {
        return $this->createDefaultDBConnection(new PDO('mysql:host=localhost;dbname=rmq_test_db','root',''));
    }

    /**
     * Returns the test dataset.
     *
     * @return IDataSet
     */
    protected function getDataSet() : IDataSet {
        return $this->createXMLDataSet(dirname(__FILE__)."/rmq_db_empty.xml");
    }

    /**
     * @test
     */
    public function saveNew_whenValidTransaction_thenTransactionShouldBeStored() : void {
        // given
        $transaction = new Transaction();
        $transaction->setAmount(100);
        $transaction->setCurrency("EUR");

        $transactionManager = new TransactionManager($transaction, $this->db);

        // when
        $storedTransaction = $transactionManager->saveNew();

        // then
        $this->assertNotNull($storedTransaction->getId());
        $this->assertNotEquals(0, $storedTransaction->getId());
        $this->assertEquals(100, $storedTransaction->getAmount());
    }

    /**
     * @test
     */
    public function saveNew_whenTransactionEmptyObj_thenTransactionShouldBeRejected() : void {
        // given
        $transaction = new Transaction();

        $transactionManager = new TransactionManager($transaction, $this->db);

        // when
        $storedTransaction = $transactionManager->saveNew();

        // then
        $this->assertEquals(0, $storedTransaction->getId());
    }
}