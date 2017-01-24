<?php

require_once __DIR__ . '/../vendor/autoload.php';

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
