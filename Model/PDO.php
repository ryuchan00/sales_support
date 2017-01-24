<?php

class Connect
{
    function pdo()
    {
        // charcterの指定をするとエラーになる
        $url = parse_url(getenv('DATABASE_URL'));
        $dsn = sprintf('pgsql:host=%s;dbname=%s;', $url['host'], substr($url['path'], 1));
        try{
            $pdo = new PDO($dsn, $url['user'], $url['pass']);
        }catch(Exception $e){
            error_log('error' .$e->getMesseage);
            die();
        }
        //エラーを表示してくれる。
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        return $pdo;
    }
}
