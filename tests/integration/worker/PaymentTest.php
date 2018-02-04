<?php
declare(strict_types=1);

namespace RMQPHP\Tests\Integration\Worker;

use PDO;
use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\TestCase;
use RMQPHP\App\Schema\Database;
use RMQPHP\App\Worker\Payment;

class PaymentTest extends TestCase {

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
        return $this->createXMLDataSet(dirname(__FILE__)."/rmq_db.xml");
    }

    /**
     * @test
     */
    public function processPayment_whenValidTransactionBalance_thenReturnTrue() : void {
        // given
        $payment = new Payment();
        $payment->setDb($this->db);
        $payment->setAmount(20);
        $payment->setCurrency("EUR");

        // when
        $paid = $payment->processPayment();

        // then
        $this->assertTrue($paid);
    }
}