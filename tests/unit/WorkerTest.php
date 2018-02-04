<?php
declare(strict_types=1);

namespace RMQPHP\Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use RMQPHP\App\Exceptions\MissingValidReceiverException;
use RMQPHP\App\Worker;

class WorkerTest extends TestCase {

    /**
     * @after
     */
    public function clean() : void {
        Mockery::close();
    }

    /**
     * @test
     */
    public function listen_whenValidReceiver_thenWorkerShouldListen() : void {
        // given
        $worker = new Worker();

        $receiver = Mockery::mock(Worker\IReceiver::class);
        $receiver->shouldReceive("listen")->once()->getMock();

        $worker->bindReceiver($receiver);

        // when
        try {
            $worker->listen();
        } catch (MissingValidReceiverException $e) {
            $this->fail($e->getMessage());
        }

        // then
        $this->assertTrue($worker->isListening());
    }

    /**
     * @test
     */
    public function listen_whenReceiverNull_thenWorkerShouldNotBeListening() : void {
        // given
        $worker = new Worker();

        // when
        $worker->listen();

        // then
        $this->assertFalse($worker->isListening());
    }
}