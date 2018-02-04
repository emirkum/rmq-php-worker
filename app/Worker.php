<?php
declare(strict_types=1);

namespace RMQPHP\App;

use RMQPHP\App\Exceptions\MissingValidReceiverException;
use RMQPHP\App\Worker\IReceiver;

/**
 * Wrapper for all receivers (PaymentReceiver...)
 */
class Worker {
    /**
     * @var IReceiver
     */
    private $receiver;

    /**
     * @var bool
     */
    private $listening = false;

    /**
     * Initialize values
     */
    public function __construct() {
        // code
    }

    /**
     * Start listening on a given IReceiver
     */
    public function listen(): void {
        if ($this->getReceiver() == null) return;

        $this->getReceiver()->listen();

        $this->setListening(true);
    }

    /**
     * Add receiver for listening
     *
     * @param   IReceiver $receiver
     * @return  Worker
     */
    public function bindReceiver(IReceiver $receiver): Worker {
        $this->setReceiver($receiver);

        return $this;
    }

    /**
     * @return IReceiver
     */
    private function getReceiver() {
        return $this->receiver;
    }

    /**
     * @param IReceiver $receiver
     */
    private function setReceiver(IReceiver $receiver): void {
        $this->receiver = $receiver;
    }

    /**
     * @return bool
     */
    public function isListening(): bool {
        return $this->listening;
    }

    /**
     * @param bool $listening
     */
    public function setListening(bool $listening): void {
        $this->listening = $listening;
    }
}