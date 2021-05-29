<?php

namespace App\Common\LINE\Event;

// Common
use ButtonMessages;
use Util;

// LINE
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class TextMessages
{
  public static function eventTextMessage($event)
  {
    // Utilから必要なものを呼び出す
    // $bot
    $bot = Util::prepareToSendMessage();
    // $replyToken
    $replyToken = Util::getReplyToken($event);

    // テキストメッセージのテキストを取得する
    $message = $event->getText();

    // 入力された文字が「今日の洋服は？」かどうかで応答メッセージを変更する
    if ($message === "今日の洋服は？") {
      // text
      $btn_text = "現在地を送る";
      $btn_url = "https://line.me/R/nv/location/";
      $btn_builder = "現在地を送ってください";
      $btn_message = "今日はどんな洋服にしようかな";

      // builder->ButtonMessages
      ButtonMessages::createButtonMessage($bot, $replyToken, $btn_text, $btn_url, $btn_message, $btn_builder);
    } else {
      $textMessage = new TextMessageBuilder("ごめんなさい、このメッセージは対応していません。");
      $bot->replyMessage($replyToken, $textMessage);
    }
  }
}
