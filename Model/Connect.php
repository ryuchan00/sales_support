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

    function registerProfile($profile)
    {
        $sql = "SELECT * FROM public.user WHERE user_line_id=:user_line_id";
        // $hoge = $this->pdo();
        $items = $this->plural($sql, $profile["userId"]);
        error_log(var_dump($items));
        if (empty($items)) {
            error_log("throw not empty");
            $sql = 'insert into public.user (user_line_id, name, comment, picture_url) values (:user_line_id, :name, :comment, :picture_url)';
            $stmt = $this->pdo()->prepare($sql);
            $stmt->bindValue(":user_line_id", $profile["userId"]);
            $stmt->bindValue(":name", $profile["displayName"]);
            $stmt->bindValue(":comment", $profile["statusMessage"]);
            $stmt->bindValue(":picture_url", $profile["pictureUrl"]);
            $flag = $stmt->execute();
            if ($flag){
               error_log('データの追加に成功しました');
            }else{
               error_log('データの追加に失敗しました');
            }
        }
        error_log("end of method");
    }

    //SELECT文のときに使用する関数。
    function select($sql)
    {
        $hoge=$this->pdo();
        $stmt=$hoge->query($sql);
        $items=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $items;
    }
    //SELECT,INSERT,UPDATE,DELETE文の時に使用する関数。
    function plural($sql,$item)
    {
        $hoge=$this->pdo();
        $stmt=$hoge->prepare($sql);
        $stmt->execute(array(':user_line_id'=>$item));//sql文のVALUES等の値が?の場合は$itemでもいい。
        return $stmt;
    }
}
