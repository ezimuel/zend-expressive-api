<?php
$provider = new Zend\Db\ConfigProvider();

return array_merge($provider(), [
    'db' => [
        'driver' => 'Pdo_Sqlite',
        'database' => 'data/users.sqlite3'
    ],
]);
