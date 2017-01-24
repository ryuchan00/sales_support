<?php

class Connect
{
    public function pdo()
    {
        $url = parse_url(getenv('DATABASE_URL'));
        $dsn = sprintf('pgsql:host=%s;dbname=%s;charser=utf8;options="--client_encoding=UTF8"', $url['host'], substr($url['path'], 1));
        try{
            $pdo = new PDO($dsn, $url['user'], $url['pass']);
        }catch(Exception $e){
            echo 'error' .$e->getMesseage;
            die();
        }
        //エラーを表示してくれる。
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        return $pdo;
    }

    //SELECT文のときに使用する関数。
    function select($sql){
        $hoge=$this->pdo();
        $stmt=$hoge->query($sql);
        $items=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $items;
    }
    //SELECT,INSERT,UPDATE,DELETE文の時に使用する関数。
    function plural($sql,$item){
        $hoge=$this->pdo();
        $stmt=$hoge->prepare($sql);
        $stmt->execute(array(':id'=>$item));//sql文のVALUES等の値が?の場合は$itemでもいい。
        return $stmt;
    }
}
