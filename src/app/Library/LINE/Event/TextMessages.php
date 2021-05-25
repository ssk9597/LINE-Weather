<?php

namespace App\Library\LINE\Event;

// Library
use ButtonMessages;

class TextMessages
{
  public static function eventTextMessage($event, $bot, $replyToken)
  {
    // text
    $btn_text = "現在地を送る";
    $btn_url = "https://line.me/R/nv/location/";
    $btn_builder = "現在地を送ってください";
    // テキストメッセージのテキストを取得する
    $message = $event->getText();
    // 入力された文字が「今日の洋服は？」「明日の洋服は？」かどうかで応答メッセージを変更する
    if ($message === "今日の洋服は？") {
      // text
      $btn_message = "今日はどんな洋服にしようかな";
      // class
      ButtonMessages::createButtonMessage($bot, $replyToken, $btn_text, $btn_url, $btn_message, $btn_builder);
    } else if ($message === "明日の洋服は？") {
      $isToday = false;
      // text
      $btn_message = "明日はどんな洋服にしようかな";
      // class
      ButtonMessages::createButtonMessage($bot, $replyToken, $btn_text, $btn_url, $btn_message, $btn_builder);
    } else {
      $textMessage = new TextMessageBuilder("ごめんなさい、このメッセージは対応していません。");
      $bot->replyMessage($replyToken, $textMessage);
    }
  }
}
