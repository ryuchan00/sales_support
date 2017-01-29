<?php
// 確認用
$dsn = 'pgsql:dbname=postgres host=localhost port=5432';
$user = 'postgres';
$password = 'cbase3554';

try{
    $dbh = new PDO($dsn, $user, $password);
}catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
}
var_dump($dbh->getAttribute(PDO::ATTR_SERVER_VERSION));
