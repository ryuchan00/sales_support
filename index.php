<?php

require_once __DIR__ . '/vendor/autoload.php';

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

    if (!($event instanceof \LINE\LINEBot\Event\MessageEvent)) {
        error_log('Non message event has come');
        continue;
    }
    if (!($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage)) {
        error_log('Non text message has come');
        continue;
    }


// $bot->replyText($event->getReplyToken(), $event->getText());
    $profile = $bot->getProfile($event->getUserId())->getJSONDecodedBody();
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

    foreach ($profile as $k => $v) {
        error_log($k . ":" . $v);
    }

$url = parse_url(getenv('DATABASE_URL'));
$dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1));
$pdo = new PDO($dsn, $url['user'], $url['pass']);

$sql = 'insert into public.user (user_line_id, name, comment, picture_url) values (:user_line_id, :name, :comment, :picture_url)';
// $sql = "insert into public.user (user_line_id, name) values (:user_line_id, :name)";
$stmt = $pdo->prepare($sql);
// foreach ($user_info as $k => $v) {
//     error_log($k . ":" . $v);
// }
// $flag = $stmt->execute(array($user_info));
// $flag = $stmt->execute(array($profile["displayName"],$profile["userId"],$profile["pictureUrl"],$profile["statusMessage"]));
$stmt->bindValue(":user_line_id", $profile["userId"]);
$stmt->bindValue(":name", $profile["displayName"]);
$stmt->bindValue(":comment", $profile["statusMessage"]);
$stmt->bindValue(":picture_url", $profile["pictureUrl"]);
$flag = $stmt->execute();
// $flag = $stmt->execute(array($user_id, $displayName));

if ($flag){
   error_log('データの追加に成功しました');
}else{
   error_log('データの追加に失敗しました');
}

// 返答するLINEスタンプをランダムで算出
    $stkid = mt_rand(1, 17);

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

//    $columnArray = array();
//    for($i = 0; $i < 5; $i++) {
//        $actionArray = array();
//        array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
//            "ボタン" . $i . "-" . 1, "c-" . $i . "-" . 1));
//        array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
//            "ボタン" . $i . "-" . 2, "c-" . $i . "-" . 2));
//        array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
//            "ボタン" . $i . "-" . 3, "c-" . $i . "-" . 3));
//        $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder (
//            ($i + 1) . "日後の天気",
//            "晴れ",
//            "https://" . $_SERVER["HTTP_HOST"] .  "/imgs/template.jpg",
//            $actionArray
//        );
//        array_push($columnArray, $column);
//      }
  // replyCarouselTemplate($bot, $event->getReplyToken(),"今後の天気予報", $columnArray);

}

function replyTextMessage($bot, $replyToken, $text) {
    $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));
    if (!$response->isSucceeded()) {
        error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
    }
}

function replyImageMessage($bot, $replyToken, $originalImageUrl, $previewImageUrl) {
    $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($originalImageUrl, $previewImageUrl));
    if (!$response->isSucceeded()) {
        error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
    }
}

function replyButtonsTemplate($bot, $replyToken, $alternativeText, $imageUrl, $title, $text, ...$actions) {
    $actionArray = array();
    foreach($actions as $value) {
        array_push($actionArray, $value);
    }
    $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
        $alternativeText,
        new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder ($title, $text, $imageUrl, $actionArray)
    );
    $response = $bot->replyMessage($replyToken, $builder);
    if (!$response->isSucceeded()) {
        error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
    }
}

function replyCarouselTemplate($bot, $replyToken, $alternativeText, $columnArray) {
    $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
        $alternativeText,
        new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder (
            $columnArray)
    );
    $response = $bot->replyMessage($replyToken, $builder);
    if (!$response->isSucceeded()) {
        error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
    }
}


?>
