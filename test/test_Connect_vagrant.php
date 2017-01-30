<?php
// 確認用
$dsn = 'pgsql:dbname=postgres host=localhost port=5432';
$user = 'postgres';
$password = 'cbase3554';

// try{
//     $dbh = new PDO($dsn, $user, $password);
// }catch (PDOException $e){
//     print('Error:'.$e->getMessage());
//     die();
// }

try{
    $dbh = new PDO($dsn, $user, $password);

    print('<br>');

    if ($dbh == null){
        print('接続に失敗しました。<br>');
    }else{
        print('接続に成功しました。<br>');
    }

    $dbh->query('SET NAMES sjis');

    $serch = "test";
    $sql = "select id, text from test where text=:serch and id=1";
    // $stmt = $dbh->query($sql);
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(":serch", $serch);
    $flag = $stmt->execute();

//    $sql = "select FOUND_ROWS()";
//    $count = $dbh->query($sql);
//    echo $count;

    if ($flag){
       echo('データの選択に成功しました');
    }else{
       echo('データの選択に失敗しました');
    }
    if ($stmt->fetchColumn() == 0){
        echo('0件');
    }else{
        echo('件数あり');
    }


    while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
        print($result['id']);
        print($result['text'].'<br>');
    }
}catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
}
var_dump($dbh->getAttribute(PDO::ATTR_SERVER_VERSION));

$dbh = null;
