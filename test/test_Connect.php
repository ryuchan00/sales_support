<?php
ini_set( 'display_errors', 1 );

require_once __DIR__ . '/../Model/Connect.php';

$profile = array(
    'userId' => 'U334d5960d3ba418048fd5c8814c27de3',
    'displayName' => 'テスト　ネーム',
    'statusMessage' => 'テストコメント',
    'pictureUrl' => 'https://google.com'
);
var_dump($profile);
$pdo = new Connect;
$stmt = $pdo->registerProfile($profile);
var_dump($stmt);
