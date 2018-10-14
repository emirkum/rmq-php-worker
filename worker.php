<?php
chdir(dirname(__DIR__));

require __DIR__ . '/vendor/autoload.php';

use RMQPHP\App\Exceptions\MissingValidReceiverException;
use RMQPHP\App\Worker;
use RMQPHP\App\Worker\Services\PaymentReceiver;
use josegonzalez\Dotenv\Loader;

$overwrite = true;
$Loader = (new josegonzalez\Dotenv\Loader(__DIR__ . '/.env'))
              ->parse()
              ->putenv($overwriteENV);

$worker = new Worker();
$paymentReceiver = new PaymentReceiver();
$worker->bindReceiver($paymentReceiver)->listen();