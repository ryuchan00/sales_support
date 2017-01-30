<?php

class Connect
{
    public function pdo()
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

    public function registerProfile($profile)
    {
        $sql = "select user_line_id, name from public.user where user_line_id='U334d5960d3ba418048fd5c8814c27de3'";
        $stmt = $this->pdo()->prepare($sql);
//        $stmt->bindValue(":user_line_id", $profile["userId"]);
//        $stmt->bindValue(":user_line_id", "U334d5960d3ba418048fd5c8814c27de3");
        $flag = $stmt->execute();
        if ($flag){
            error_log('データの選択に成功しました');
        }else{
            error_log('データの選択に失敗しました');
        }
        if ($stmt->fetchColumn() == 0){
            error_log('0件');
        }else{
            error_log('件数あり');
        }

//        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
//            error_log($result['user_line_id']);
//            error_log($result['name']);
//        }

//        if ($stmt->fetchColumn() == 0) {
//            $sql = "insert into public.user (user_line_id, name, comment, picture_url) values (:user_line_id, :name, :comment, :picture_url)";
//            $stmt = $this->pdo()->prepare($sql);
//            $stmt->bindValue(":user_line_id", $profile["userId"]);
//            $stmt->bindValue(":name", $profile["displayName"]);
//            $stmt->bindValue(":comment", $profile["statusMessage"]);
//            $stmt->bindValue(":picture_url", $profile["pictureUrl"]);
//            $flag = $stmt->execute();
//            if ($flag){
//                error_log('データの追加に成功しました');
//            }else{
//                error_log('データの追加に失敗しました');
//            }
//        }

        // 結果の取得
        // $members = array();
        // foreach ($statement as $row) {
        //     $members[] = $row;
        //     error_log($row);
        // }
        // var_dump($members);

        // $hoge = $this->pdo();
        // $items = $this->plural($sql, $profile["userId"]);
        // error_log($profile["userId"]);
        // if (!empty($items)) {
        //     error_log("throw empty");
        //     $sql = 'insert into public.user (user_line_id, name, comment, picture_url) values (:user_line_id, :name, :comment, :picture_url)';
        //     $stmt = $this->pdo()->prepare($sql);
        //     $stmt->bindValue(":user_line_id", $profile["userId"]);
        //     $stmt->bindValue(":name", $profile["displayName"]);
        //     $stmt->bindValue(":comment", $profile["statusMessage"]);
        //     $stmt->bindValue(":picture_url", $profile["pictureUrl"]);
        //     $flag = $stmt->execute();
        //     if ($flag){
        //        error_log('データの追加に成功しました');
        //     }else{
        //        error_log('データの追加に失敗しました');
        //     }
        // } else {
        //     error_log("throw not empty");
        // }
        // error_log("end of method");
    }

    //SELECT文のときに使用する関数。
    public function select($sql)
    {
        $hoge=$this->pdo();
        $stmt=$hoge->query($sql);
        $items=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $items;
    }
    //SELECT,INSERT,UPDATE,DELETE文の時に使用する関数。
    public function plural($sql,$item)
    {
        error_log('item'.$item);
        $hoge=$this->pdo();
        $stmt=$hoge->prepare($sql);
        $stmt = $this->pdo()->prepare($sql);
        $stmt->bindValue(":id", $item);
        $flag = $stmt->execute();
        if ($flag){
           error_log('データの選択に成功しました');
        }else{
           error_log('データの選択に失敗しました');
        }
        // $stmt->execute(array(':id'=>$item));//sql文のVALUES等の値が?の場合は$itemでもいい。
        // $stmt->execute(array($item));
        error_log($stmt->debugDumpParams());
        return $flag;
    }
}
