<?php

class Connect
{
    public function pdo()
    {
        // charcterの指定をするとエラーになる
        $url = parse_url(getenv('DATABASE_URL'));
        $dsn = sprintf('pgsql:host=%s;dbname=%s;', $url['host'], substr($url['path'], 1));
        try {
            $pdo = new PDO($dsn, $url['user'], $url['pass']);
        } catch (Exception $e) {
            error_log('error' . $e->getMesseage);
            die();
        }
        //エラーを表示してくれる。
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        return $pdo;
    }

    public function registerProfile($profile)
    {
        $sql = "select id, user_line_id, name, comment, picture_url from public.user where user_line_id=:user_line_id";
        $stmt = $this->pdo()->prepare($sql);
        $stmt->bindValue(":user_line_id", $profile["userId"], PDO::PARAM_STR);
        $flag = $stmt->execute();
        if ($flag) {
            error_log('データの選択に成功しました');
        } else {
            error_log('データの選択に失敗しました');
        }

        $count = 0;
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            error_log($result['user_line_id']);
            error_log($result['name']);
            $count++;
        }

        if ($count === 0) {
            $sql = "insert into public.user (user_line_id, name, comment, picture_url) values (:user_line_id, :name, :comment, :picture_url)";
            $stmt = $this->pdo()->prepare($sql);
            $stmt->bindValue(":user_line_id", $profile["userId"]);
            $stmt->bindValue(":name", $profile["displayName"]);
            $stmt->bindValue(":comment", $profile["statusMessage"]);
            $stmt->bindValue(":picture_url", $profile["pictureUrl"]);
            $flag = $stmt->execute();
            if ($flag) {
                error_log('データの追加に成功しました');
            } else {
                error_log('データの追加に失敗しました');
            }
        } else {
            error_log('すでに登録実績あり');
        }
    }

    //SELECT文のときに使用する関数。
    public function select($sql)
    {
        $hoge = $this->pdo();
        $stmt = $hoge->query($sql);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $items;
    }

    //SELECT,INSERT,UPDATE,DELETE文の時に使用する関数。
    public function plural($sql, $item)
    {
        $hoge = $this->pdo();
        $stmt = $hoge->prepare($sql);
//        $stmt = $this->pdo()->prepare($sql);
        $stmt->bindValue(":id", $item);
        $flag = $stmt->execute();
        if ($flag) {
            error_log('データの選択に成功しました');
        } else {
            error_log('データの選択に失敗しました');
        }
        // $stmt->execute(array(':id'=>$item));//sql文のVALUES等の値が?の場合は$itemでもいい。
        // $stmt->execute(array($item));
        error_log($stmt->debugDumpParams());
        return $flag;
    }

    public function plurals($sql, $item)
    {
        $hoge = $this->pdo();
        $stmt = $hoge->prepare($sql);
        foreach ($item as $k => $v) {
            $stmt->bindValue(":" . $k, $v);
        }
        $flag = $stmt->execute();
        if ($flag) {
            error_log('データの更新に成功しました');
        } else {
            error_log('データの更新に失敗しました');
        }
//        error_log($stmt->debugDumpParams());
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log($sql);
        while ($result) {
            error_log($result['user_line_id']);
            error_log($result['name']);
        }
        return $result;
    }
}
