<?php

require "vendor/autoload.php";
use PhpOrient\PhpOrient;
use PhpOrient\Protocols\Binary\Data\Record;

Dotenv::load(__DIR__);

$dbName = getenv('DB_DATABASE')?: 'Users';

$config = [
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '0r13ntDB',
    'hostname' => getenv('DB_HOST') ?: '172.17.42.1',
    'port'     => getenv('DB_PORT') ?: 2424,
];

$client = new PhpOrient();
$client->configure($config);
$client->connect();

if (!$client->dbExists($dbName)) {
    $client->dbCreate($dbName, PhpOrient::STORAGE_TYPE_MEMORY);
}

$client->dbOpen($dbName, $config['username'], $config['password']);

try {
    $client->command('create class Users extends V');
} catch (Exception $e) {
    echo 'class Users already creates';
}

$userName = mt_rand(1000, 100000);
$userType = 'Guest';

$client->command("insert into {$dbName} set name = '{$userName}', type = '{$userType}'");

$users = $client->query("select from {$dbName}");

/** @var  Record $user */
foreach ($users as $user) {
    echo $user->getOData()['name'], PHP_EOL;
}
