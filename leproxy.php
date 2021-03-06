<?php

use Clue\React\Socks\Client;
use Clue\React\Socks\Server;
use React\Socket\Server as Socket;
use React\Socket\Connector;

require __DIR__ . '/vendor/autoload.php';

$listen = isset($argv[1]) ? $argv[1] : 1080;
$path = isset($argv[2]) ? array_slice($argv, 2) : array();

// Alternatively, you can also hard-code these values like this:
//$listen = '127.0.0.1:9050';
//$path = array('127.0.0.1:9051', '127.0.0.1:9052', '127.0.0.1:9053');

$loop = React\EventLoop\Factory::create();

// set next SOCKS server chain -> p1 -> p2 -> p3 -> destination
$connector = new Connector($loop);
foreach ($path as $proxy) {
    $connector = new Client($proxy, $connector);
}

// listen on 127.0.0.1:1080 or first argument
$socket = new Socket($listen, $loop);

// start a new server which forwards all connections to the other SOCKS server
$server = new Server($loop, $socket, $connector);

echo 'LeProxy is now listening on ' . $socket->getAddress() . PHP_EOL;
if ($path) {
    echo 'Forwarding via: ' . implode(' -> ', $path) . PHP_EOL;
}

$loop->run();
