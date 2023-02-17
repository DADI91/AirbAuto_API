<?php
$client = new Grpc\Example\GreeterClient('localhost:50051', [
    'credentials' => Grpc\ChannelCredentials::createInsecure(),
]);

$request = new Grpc\Example\HelloRequest();
$request->setName('World');

list($response, $status) = $client->SayHello($request)->wait();

if ($status->code !== Grpc\STATUS_OK) {
    echo "Error: " . $status->details . PHP_EOL;
    exit(1);
}

echo "Received: " . $response->getMessage() . PHP_EOL;
