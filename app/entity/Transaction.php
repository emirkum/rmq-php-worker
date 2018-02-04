<?php
declare(strict_types=1);

namespace RMQPHP\App\Entity;

class Transaction {

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * Transaction constructor.
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
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getAmount(): int {
        return intval($this->amount);
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
        return $this->currency == null ? "" : $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void {
        $this->currency = $currency;
    }
}