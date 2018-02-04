<?php
declare(strict_types=1);

namespace RMQPHP\Tests\Integration\Entity\Schema;

use PDO;
use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\TestCase;
use RMQPHP\App\Entity\Balance;
use RMQPHP\App\Entity\Schema\BalanceManager;
use RMQPHP\App\Schema\Database;

class BalanceManagerTest extends TestCase {

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
    protected function getConnection() {
        return $this->createDefaultDBConnection(new PDO('mysql:host=localhost;dbname=rmq_test_db','root',''));
    }

    /**
     * Returns the test dataset.
     *
     * @return IDataSet
     */
    protected function getDataSet() {
        return $this->createXMLDataSet(dirname(__FILE__)."/rmq_db_empty.xml");
    }

    /**
     * @test
     */
    public function saveNew_whenBalanceValid_thenBalanceShouldBeSummed() : void {
        // given
        $balance = new Balance();
        $balance->setValue(10);

        $balanceManager = new  BalanceManager($balance, $this->db);

        // when
        $storedBalance = $balanceManager->saveNew();

        // then
        $this->assertNotNull($storedBalance->getId());
        $this->assertNotEquals(0, $storedBalance->getId());
        $this->assertEquals(10, $storedBalance->getValue());
    }

    /**
     * @test
     */
    public function saveNew_whenBalanceLessThanZero_thenBalanceShouldBeZero() : void {
        // given
        $balance = new Balance();
        $balance->setValue(-10);

        $balanceManager = new  BalanceManager($balance, $this->db);

        // when
        $storedBalance = $balanceManager->saveNew();

        // then
        $this->assertNotNull($storedBalance->getId());
        $this->assertNotEquals(0, $storedBalance->getId());
        $this->assertEquals(0, $storedBalance->getValue());
    }

    /**
     * @test
     */
    public function saveNew_whenBalanceEmpty_thenBalanceShouldBeRejected() : void {
        // given
        $balance = new Balance();

        $balanceManager = new BalanceManager($balance, $this->db);

        // when
        $storedBalance = $balanceManager->saveNew();

        // then
        $this->assertEquals(0, $storedBalance->getId());
    }

    /**
     * @test
     */
    public function update_whenBalanceValid_thenBalanceShouldBeUpdated() : void {
        // given
        $balance = new Balance();
        $balance->setValue(10);

        $balanceManager = new  BalanceManager($balance, $this->db);
        $storedBalance = $balanceManager->saveNew();

        $newBalance = $storedBalance;
        $newBalance->setValue(15 + $storedBalance->getValue());

        $balanceManager = new BalanceManager($newBalance, $this->db);

        // when
        $storedBalance = $balanceManager->update();

        // then
        $this->assertEquals(25, $storedBalance->getValue());
    }

    /**
     * @test
     */
    public function update_whenBalanceLessThanZero_thenBalanceShouldBeZero() : void {
        // given
        $balance = new Balance();
        $balance->setValue(10);

        $balanceManager = new  BalanceManager($balance, $this->db);
        $storedBalance = $balanceManager->saveNew();

        $newBalance = $storedBalance;
        $newBalance->setValue(-100 + $storedBalance->getValue());

        $balanceManager = new BalanceManager($newBalance, $this->db);

        // when
        $storedBalance = $balanceManager->update();

        // then
        $this->assertEquals(0, $storedBalance->getValue());
    }

    /**
     * @test
     */
    public function get_whenBalanceExists_thenReturnValidBalance() : void {
        // given
        $balance = new Balance();
        $balance->setValue(10);

        $balanceManager = new  BalanceManager($balance, $this->db);

        $balanceManager->saveNew();

        $balanceManager = new BalanceManager(new Balance(), $this->db);

        // when
        $existingBalance = $balanceManager->get();

        // then
        $this->assertNotNull($existingBalance);
        $this->assertNotNull($existingBalance->getId());
        $this->assertNotEquals(0, $existingBalance->getId());
        $this->assertEquals(10, $existingBalance->getValue());
    }

    /**
     * @test
     */
    public function get_whenBalanceDoNotExist_thenReturnEmptyBalanceObj() : void {
        // given
        $balanceManager = new BalanceManager(new Balance(), $this->db);

        // when
        $existingBalance = $balanceManager->get();

        // then
        $this->assertEquals(0, $existingBalance->getId());
        $this->assertEquals(0, $existingBalance->getValue());
    }
}