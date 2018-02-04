<?php
declare(strict_types=1);

namespace RMQPHP\App\Worker;

/**
 * Contract for all receivers
 */
interface IReceiver 
{
    /**
     * Process incoming request from AMQP server
     * Takes no arguments, returns no value
     */
    public function listen() : void;
}