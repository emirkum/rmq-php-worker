<?php
declare(strict_types=1);

namespace RMQPHP\App\Worker\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use RMQPHP\App\Worker\IReceiver;
use RMQPHP\App\Worker\Payment;

/**
 * Payment receiver
 */
class PaymentReceiver implements IReceiver
{

    /**
     * @var string  RabbitMQ Queue name
     */
    const RMQQUEUE = 'payment_queue';

    /**
     * @var string
     */
    private $responseMsg;

    /**
     * @var bool
     */
    private $sent;

    /**
     * Default empty constructor
     */
    public function __construct() {
        // code
    }
    
    /**
     * Process incoming request to add new payment
     */ 
    public function listen() : void {        
        $connection = new AMQPStreamConnection
        (            
            getenv('RMQHOST'),
            getenv('RMQPORT'),
            getenv('RMQUSER'),
            getenv('RMQPASSWORD')
        );
        
        $channel = $connection->channel();
        
        $channel->queue_declare(
            self::RMQQUEUE,   #queue
            false,            #passive
            true,             #durable, make sure that RabbitMQ will never lose our queue if a crash occurs
            false,            #exclusive - queues may only be accessed by the current connection
            false             #auto delete - the queue is deleted when all consumers have finished using it
            );
            
        $channel->basic_qos(
            null,   #prefetch size - prefetch window size in octets, null meaning "no specific limit"
            1,      #prefetch count - prefetch window in terms of whole messages
            null    #global - global=null to mean that the QoS settings should apply per-consumer, global=true to mean that the QoS settings should apply per-channel
            );
        
        $channel->basic_consume(
            self::RMQQUEUE,                 #queue
            '',                             #consumer tag - Identifier for the consumer, valid within the current channel. just string
            false,                          #no local - TRUE: the server will not send messages to the connection that published them
            false,                          #no ack, false - acks turned on, true - off.  send a proper acknowledgment from the worker, once we're done with a task
            false,                          #exclusive - queues may only be accessed by the current connection
            false,                          #no wait - TRUE: the server will not respond to the method. The client should not wait for a reply method
            array($this, 'processPayment')  #callback
            );
            
            
        while (count($channel->callbacks)) {
            $channel->wait();
        }
        
        $channel->close();
        $connection->close();
    }
    
    /**
     * Process received request
     * 
     * @param AMQPMessage $msg
     */ 
    public function processPayment(AMQPMessage $msg) : void {
        $trans = json_decode($msg->body);

        if (is_object($trans) && isset($trans->amount) && isset($trans->currency)) {
            echo "New message.";
            $payment = new Payment();
            $payment->setAmount($trans->amount);
            $payment->setCurrency($trans->currency);
            
            if ($payment->processPayment()) {
                $this->setResponseMsg("Payment completed");
                $this->setSent(true);

            } else {
                $this->setSent(false);
                $this->setResponseMsg("Transaction parameters incorrect.");
            }

            echo $this->getResponseMsg();

            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

            return;
        }

        echo 'Transaction parameters incorrect.';
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }

    /**
     * @return string
     */
    public function getResponseMsg(): string {
        return $this->responseMsg;
    }

    /**
     * @param string $responseMsg
     */
    public function setResponseMsg(string $responseMsg): void {
        $this->responseMsg = $responseMsg;
    }

    /**
     * @return bool
     */
    public function isSent(): bool {
        return $this->sent;
    }

    /**
     * @param bool $sent
     */
    public function setSent(bool $sent): void {
        $this->sent = $sent;
    }
}