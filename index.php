<!DOCTYPE html>
<html>
  <body>
<?php
require('vendor/autoload.php');
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$i = 0;
//Getting VCAP info
$service_blob = json_decode($_ENV['VCAP_SERVICES'], true);
$rabbitmq_services = array();
foreach($service_blob as $service_provider => $service_list) {
    // looks for rabbitmq service, can be declared in manifest.yml
    if ($service_provider === $_ENV['RMQ_SERVICE'] || $service_provider === 'p-rabbitmq-35') {
        foreach($service_list as $rabbitmq_service) {
            $rabbitmq_services[] = $rabbitmq_service;
        }
        continue;
    }

}

for ($i = 1; $i <= count($rabbitmq_services); $i++) {
    //getting all the creds
    $amqp = $rabbitmq_services[$i-1]['credentials']['protocols']['amqp'];
    $hostname = $amqp['host'];
    $port = $amqp['port'];
    $user = $amqp['username'];
    $password = $amqp['password'];
    $vhost = $amqp['vhost'];

    //sending OK
    $connection = new AMQPStreamConnection($hostname, $port, $user, $password, $vhost);
    $channel = $connection->channel();
    $channel->queue_declare('hello', false, false, false, false);
    $msg = new AMQPMessage('OK');
    $channel->basic_publish($msg, '', 'hello');
    //echo '<h1>[x] Sent OK!\h1>';
    $channel->close();
    $connection->close();


    $connection = new AMQPStreamConnection($hostname, $port, $user, $password, $vhost);
    $channel = $connection->channel();
    $channel->queue_declare('hello', false, false, false, false);

    //This ensure it only gets one message
    $callback = function($msg) {
      echo $msg->body;
      $channel->close();
      $connection->close();
    };
    $channel->basic_consume('hello', '', false, true, false, false, $callback);
    while(count($channel->callbacks)) {
      $channel->wait();
    }
    $channel->close();
    $connection->close();

}

?>
</body>
</html>
