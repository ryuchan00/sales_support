<?php

require_once __DIR__ . '/../Model/Connect.php';
require_once __DIR__ . '/../Model/edit_message.php';
require __DIR__ . '/../vendor/autoload.php'; // path to vendor/

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

$signature = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
try {
    $events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);
} catch (\LINE\LINEBot\Exception\InvalidSignatureException $e) {
    error_log("parseEventRequest failed. InvalidSignatureException => " . var_export($e, true));
} catch (\LINE\LINEBot\Exception\UnknownEventTypeException $e) {
    error_log("parseEventRequest failed. UnknownEventTypeException => " . var_export($e, true));
} catch (\LINE\LINEBot\Exception\UnknownMessageTypeException $e) {
    error_log("parseEventRequest failed. UnknownMessageTypeException => " . var_export($e, true));
} catch (\LINE\LINEBot\Exception\InvalidEventRequestException $e) {
    error_log("parseEventRequest failed. InvalidEventRequestException => " . var_export($e, true));
}

foreach ($events as $event) {
    $profile = $bot->getProfile($event->getUserId())->getJSONDecodedBody();
    $pdo = new Connect;
    $pdo->registerProfile($profile);

    // 友達追加処理

    // am 9:00 ~ pm 22:45
    $target_hh = array("h_9", "h_10", "h_11", "h_12", "h_13", "h_14", "h_15", "h_16", "h_17", "h_18", "h_19", "h_20", "h_21", "h_22", "h_23");
    $target_mm = array("m_00", "m_15", "m_30", "m_45");
    if (($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage)) {
        $post_msg = $event->getText();

        $sql = "select id, user_line_id, name, comment, picture_url, hour, minute from public.user where user_line_id=:user_line_id and hour is not NULL and minute is not NULL and body is NULL";
        $item = [
            "user_line_id" => $profile["userId"]
        ];
        $user = $pdo->plurals($sql, $item);
        if (!empty($user)) {
            $sql = "update public.user set body=:body where user_line_id=:user_line_id";
            $item = [
                "user_line_id" => $profile["userId"],
                "body" => $post_msg
            ];
            $pdo->plurals($sql, $item);
            // todo:$user['name']の改行マークを置換する。
            $body = <<<EOD
本文は以下でよろしいですか？
各位

{$user['name']}です。
{$user['hour']}時{$user['minute']}分に帰社します。
{$post_msg}

以上、宜しくお願い致します。
EOD;
            replyTextMessage($bot, $event->getReplyToken(), $body);
            exit;
        }
        switch ($post_msg) {
            case "帰社":
                $sql = "update public.user set hour=NULL, minute=NULL, body=NULL where user_line_id=:user_line_id";
                $item = [
                    "user_line_id" => $profile["userId"],
                ];
                $pdo->plurals($sql, $item);
                $columnArray = [];
                $actionArray = [];
                foreach ($target_hh as $k => $v) {
                    array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
                        substr($v, 2), $v));
                    if ((($k + 1) % 3 == 0)) {
                        $picture_num = (($k + 1) / 3);
                        $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder (
                            "時選択",
                            "何時に帰社しますか？",
                            "https://" . $_SERVER["HTTP_HOST"] . "/imgs/" . $picture_num . ".png",
                            $actionArray
                        );
                        array_push($columnArray, $column);
                        $actionArray = array();
                    }
                }
                replyCarouselTemplate($bot, $event->getReplyToken(), "帰社報告　時選択", $columnArray);
                exit;
            case "はい":
                $sql = "select id, user_line_id, name, comment, picture_url, hour, minute, body from public.user where user_line_id=:user_line_id and hour is not NULL and minute is not NULL and body is not NULL";
                $item = [
                    "user_line_id" => $profile["userId"]
                ];
                $user = $pdo->plurals($sql, $item);
                if (!empty($user)) {

                    $message = <<<EOD
各位

{$user['name']}です。
{$user['hour']}時{$user['minute']}分に帰社します。
{$user['body']}

以上、宜しくお願い致します。
EOD;
                    $sendgrid = new SendGrid(getenv('SENDGRID_USERNAME'), getenv('SENDGRID_PASSWORD'));
                    $email = new SendGrid\Email();
//                    $email->addTo('leo0210leo@gmail.com')->
                    $email->addTo(getenv('MAIL_TO'))->
                    setFrom('kintai@cbase.co.jp')->
                    setSubject('【勤怠連絡】' . $user["name"])->
//                    setSubject('テスト' . $user["name"])->
                    setText($message);

                    $sendgrid->send($email);
                }
                $sql = "update public.user set hour=NULL, minute=NULL, body=NULL where user_line_id=:user_line_id";
                $item = [
                    "user_line_id" => $profile["userId"],
                ];
                $pdo->plurals($sql, $item);
                replyTextMessage($bot, $event->getReplyToken(), 'メール送信完了');
                exit;
        }
    }
    if ($event instanceof \LINE\LINEBot\Event\PostbackEvent) {
        $postback_msg = $event->getPostbackData();
        if (in_array($postback_msg, $target_hh)) {
            $hh = substr($postback_msg, 2);
            $sql = "update public.user set hour=:hour where user_line_id=:user_line_id";
            $item = [
                "user_line_id" => $profile["userId"],
                "hour" => $hh
            ];
            $pdo->plurals($sql, $item);
            replyButtonsTemplate($bot,
                $event->getReplyToken(),
                "帰社報告　分選択",
                "https://" . $_SERVER["HTTP_HOST"] . "/imgs/hh.png",
                "分選択",
                "{$hh}時何分に帰社しますか？",
                new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
                    substr($target_mm[0], 2), $target_mm[0]),
                new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
                    substr($target_mm[1], 2), $target_mm[1]),
                new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
                    substr($target_mm[2], 2), $target_mm[2]),
                new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
                    substr($target_mm[3], 2), $target_mm[3])
            );
            exit;
        }
        if (in_array($postback_msg, $target_mm)) {
            $mm = substr($postback_msg, 2);
            $sql = "update public.user set minute=:minute where user_line_id=:user_line_id";
            $item = [
                "user_line_id" => $profile["userId"],
                "minute" => $mm
            ];
            $pdo->plurals($sql, $item);
            replyTextMessage($bot, $event->getReplyToken(), "続けてメール本文を入力してください。");
            exit;
        }
    }

    // 直帰処理

    // 「はい」処理

    // 「いいえ」処理
}
