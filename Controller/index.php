<?php

require_once __DIR__ . '/../Model/PDO.php';
require_once __DIR__ . '/../Model/edit_message.php';

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

$signature = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
try {
  $events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);
} catch(\LINE\LINEBot\Exception\InvalidSignatureException $e) {
    error_log("parseEventRequest failed. InvalidSignatureException => ".var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownEventTypeException $e) {
    error_log("parseEventRequest failed. UnknownEventTypeException => ".var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownMessageTypeException $e) {
    error_log("parseEventRequest failed. UnknownMessageTypeException => ".var_export($e, true));
} catch(\LINE\LINEBot\Exception\InvalidEventRequestException $e) {
    error_log("parseEventRequest failed. InvalidEventRequestException => ".var_export($e, true));
}

foreach ($events as $event) {
    if ($event instanceof \LINE\LINEBot\Event\PostbackEvent) {
        replyTextMessage($bot, $event->getReplyToken(), "Postback受信「" . $event->getPostbackData() . "」");
        continue;
    }

    // 全イベントを許可する。
    // if (!($event instanceof \LINE\LINEBot\Event\MessageEvent)) {
    //     error_log('Non message event has come');
    //     continue;
    // }
    // if (!($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage)) {
    //     error_log('Non text message has come');
    //     continue;
    // }


// $bot->replyText($event->getReplyToken(), $event->getText());
    $message = "http://codezine.jp/article/detail/9905";
//$message = $profile["displayName"] . "さん、ランダムでスタンプで返答します。";
    $profile = $bot->getProfile($event->getUserId())->getJSONDecodedBody();
    $pdo = new Connect;
    $sql = 'insert into public.user (user_line_id, name, comment, picture_url) values (:user_line_id, :name, :comment, :picture_url)';
    $stmt = $pdo->pdo()->prepare($sql);
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
//$bot->replyMessage($event->getReplyToken(),
//  (new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder())
//    ->add(new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message))
//    ->add(new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder(1, $stkid))
//);

//
//    replyImageMessage($bot, $event->getReplyToken(), "https://" . $_SERVER["HTTP_HOST"] . "/imgs/original.jpg", "https://" . $_SERVER["HTTP_HOST"] . "/imgs/preview.jpg");
    //  replyButtonsTemplate($bot,
    //      $event->getReplyToken(),
    //      "お天気お知らせ - 今日は天気予報は晴れです",
    //      "https://" . $_SERVER["HTTP_HOST"] . "/imgs/template.jpg",
    //      "お天気お知らせ",
    //      "今日は天気予報は晴れです",
    //      new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
    //          "明日の天気", "tomorrow"),
    //      new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
    //          "週末の天気", "weekend"),
    //      new LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder (
    //          "Webで見る", "https://ct2.cservice.jp/res5.3t_demo/twilio_demo2/manage/index.php?mode=re_auth")
    //  );
    
    // 友達追加処理

    // 帰社処理
    // am 9:00 ~ pm 22:45
    // $target_hh = array("9","10","11","12","13","14");
    // $target_hh = array("9","10","11","12","13","14","15","16","17","18","19","20","21","22");
    // $target_mm = array("00","15","30","45");
    // $columnArray = array();
    // $actionArray = array();
    // foreach ($target_hh as $k => $v) {
    //     array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
    //         $v, $v));
    //     error_log("process 1");
    //     if ((($k + 1) % 3 == 0) || (($k + 1) == (count($target_hh)))) {
    //         $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder (
    //             "帰社時間選択",
    //             "何時に帰社しますか?",
    //             "https://" . $_SERVER["HTTP_HOST"] .  "/imgs/template.png",
    //             $actionArray
    //         );
    //         array_push($columnArray, $column);
    //         $actionArray = array();
    //         error_log("process 2");
    //     }
    // }
    // replyCarouselTemplate($bot, $event->getReplyToken(),"帰社報告", $columnArray);
    // error_log("process 3");

    // 直帰処理
    
    // 「はい」処理
    
    // 「いいえ」処理
    
    $columnArray = array();
    for($i = 0; $i < 1; $i++) {
        $actionArray = array();
        array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
            "ボタン" . $i . "-" . 1, "c-" . $i . "-" . 1));
        // array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
        //     "ボタン" . $i . "-" . 2, "c-" . $i . "-" . 2));
        // array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
        //     "ボタン" . $i . "-" . 3, "c-" . $i . "-" . 3));
        $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder (
            ($i + 1) . "日後の天気",
            "晴れ",
            "https://" . $_SERVER["HTTP_HOST"] .  "/imgs/template.png",
            $actionArray
        );
        array_push($columnArray, $column);
    }
    
    for($i = 0; $i < 1; $i++) {
        $actionArray = array();
        array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
            "ボタン" . $i . "-" . 1, "c-" . $i . "-" . 1));
        array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
            "ボタン" . $i . "-" . 2, "c-" . $i . "-" . 2));
        // array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
        //     "ボタン" . $i . "-" . 3, "c-" . $i . "-" . 3));
        $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder (
            ($i + 1) . "日後の天気",
            "晴れ",
            "https://" . $_SERVER["HTTP_HOST"] .  "/imgs/template.png",
            $actionArray
        );
        array_push($columnArray, $column);
    }
    replyCarouselTemplate($bot, $event->getReplyToken(),"今後の天気予報", $columnArray);
}
