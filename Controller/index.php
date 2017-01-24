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
//    $user_id = $profile["userId"];
//    $displayName = $profile["displayName"];
//    error_log($displayName);
    // $user_info = array(
    //     $profile["displayName"],
    //     $profile["userId"],
    //     $profile["pictureUrl"],
    //     $profile["statusMessage"]
    // );

// $url = parse_url(getenv('DATABASE_URL'));
// $dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1));
// $pdo = new PDO($dsn, $url['user'], $url['pass']);
    $profile = $bot->getProfile($event->getUserId())->getJSONDecodedBody();
    $pdo = new Connect;
    $sql = 'insert into public.user (user_line_id, name, comment, picture_url) values (:user_line_id, :name, :comment, :picture_url)';
    // $sql = "insert into public.user (user_line_id, name) values (:user_line_id, :name)";
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
     replyButtonsTemplate($bot,
         $event->getReplyToken(),
         "お天気お知らせ - 今日は天気予報は晴れです",
         "https://" . $_SERVER["HTTP_HOST"] . "/imgs/template.jpg",
         "お天気お知らせ",
         "今日は天気予報は晴れです",
         new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
             "明日の天気", "tomorrow"),
         new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
             "週末の天気", "weekend"),
         new LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder (
             "Webで見る", "https://ct2.cservice.jp/res5.3t_demo/twilio_demo2/manage/index.php?mode=re_auth")
     );
    
    $pref = array("北海道","青森県","岩手県","宮城県","秋田県","山形県","福島県","茨城県",
    "栃木県","群馬県","埼玉県","千葉県","東京都","神奈川県","新潟県","富山県","石川県","福井県",
    "長野県","山梨県","岐阜県","静岡県","愛知県","三重県","滋賀県","京都府","大阪府","兵庫県",
    "奈良県","和歌山県","鳥取県","島根県","岡山県","広島県","山口県","徳島県","香川県","愛媛県",
    "高知県","福岡県","佐賀県","長崎県","熊本県","大分県","宮崎県","鹿児島県","沖縄県");
    $columnArray = array();
    for($i = 0; $i < 5; $i++) {
        $actionArray = array();
        array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
            "ボタン" . $i . "-" . 1, "c-" . $i . "-" . 1));
        array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
            "ボタン" . $i . "-" . 2, "c-" . $i . "-" . 2));
        array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
            "ボタン" . $i . "-" . 3, "c-" . $i . "-" . 3));
        $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder (
            ($i + 1) . "日後の天気",
            "晴れ",
            "https://" . $_SERVER["HTTP_HOST"] .  "/imgs/template.jpg",
            $actionArray
        );
        array_push($columnArray, $column);
    }
    replyCarouselTemplate($bot, $event->getReplyToken(),"今後の天気予報", $columnArray);
}
