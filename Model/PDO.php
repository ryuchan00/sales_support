<?php

class Connect
{
    public function pdo()
    {
        $url = parse_url(getenv('DATABASE_URL'));
        $dsn = sprintf('pgsql:host=%s;dbname=%s;charset=utf8;', $url['host'], substr($url['path'], 1));
        // $pdo = new PDO($dsn, $url['user'], $url['pass']);
        try{
            $pdo = new PDO($dsn, $url['user'], $url['pass']);
        }catch(Exception $e){
            error_log('error' .$e->getMesseage);
            die();
        }
        //エラーを表示してくれる。
        // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        return $pdo;
    }
}
