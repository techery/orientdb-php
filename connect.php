<?php

require "vendor/autoload.php";
use PhpOrient\PhpOrient;
use PhpOrient\Protocols\Binary\Data\Record;

$dbName = 'Users';

$config = [
  'username' => 'root',
  'password' => '0r13ntDB',
  'hostname' => '172.17.42.1',
  'port' => 2424,
];

$client = new PhpOrient();
$client->configure($config);
$client->connect();

if (!$client->dbExists($dbName))
{
  $client->dbCreate($dbName, PhpOrient::STORAGE_TYPE_MEMORY);
}

$client->dbOpen($dbName, $config['username'], $config['password']);

try
{
  $client->command('create class Users extends V');
}
catch (Exception $e)
{
  echo 'class Users already creates';
}

$userName = mt_rand(1000, 100000);
$userType = 'Guest';

$client->command("insert into {$dbName} set name = '{$userName}', type = '{$userType}'");

$users = $client->query('select from Users');

/** @var  Record $user */
foreach ($users as $user)
{
  echo $user->getOData()['name'], PHP_EOL;
}