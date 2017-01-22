<?php
/**
 * Created by IntelliJ IDEA.
 * User: ryuutarou
 * Date: 2016/12/26
 * Time: 22:42
 */
$url = parse_url(getenv('DATABASE_URL'));

$dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1));

$pdo = new PDO($dsn, $url['user'], $url['pass']);
var_dump($pdo->getAttribute(PDO::ATTR_SERVER_VERSION));
