<?php

require "vendor/autoload.php";
use PhpOrient\PhpOrient;
use PhpOrient\Protocols\Binary\Data\Record;

Dotenv::load(__DIR__);

$dbName = getenv('DB_DATABASE') ?: 'GratefulDeadConcerts';

$config = [
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '0r13ntDB',
    'hostname' => getenv('DB_HOST') ?: 'localhost',
    'port'     => getenv('DB_PORT') ?: 2424,
];

$client = new PhpOrient();
$client->configure($config);
$client->connect();

if (!$client->dbExists($dbName)) {
    $client->dbCreate($dbName, PhpOrient::STORAGE_TYPE_MEMORY);
}

$client->dbOpen($dbName, $config['username'], $config['password']);

$userClassName = getenv('DB_USER_CLASS') ?: 'User';

try {
    $client->command("create class {$userClassName}");
} catch (Exception $e) {
    echo 'class Users already exists', PHP_EOL;
}

$userName = mt_rand(1000, 100000);
$userType = 'Guest';

$client->command("insert into {$userClassName} set name = '{$userName}', type = '{$userType}'");

$users = $client->query("select from {$dbName}");

/** @var  Record $user */
foreach ($users as $user) {
    echo $user->getOData()['name'], PHP_EOL;
}
