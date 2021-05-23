<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// LINE
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
// logs
use Illuminate\Support\Facades\Log;

class LINEController extends Controller
{
  public function sendMessage(Request $request)
  {
    // env
    $channelSecret = env("LINE_CHANNEL_SECRET");
    $channelAccessToken = env("LINE_CHANNEL_ACCESS_TOKEN");

    // 署名を検証する
    $signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);
    if (!SignatureValidator::validateSignature($request->getContent(), $channelSecret, $signature)) {
      return;
    }

    // メッセージを送る準備
    $httpClient = new CurlHTTPClient($channelAccessToken);
    $bot = new LINEBot($httpClient, ['channelSecret' => $channelSecret]);

    //Webhookの処理
    $events = $bot->parseEventRequest($request->getContent(), $signature);

    // log
    Log::info($events);

    foreach ($events as $event) {
      // eventがテキストメッセージの時
      if ($event instanceof TextMessage) {
        // テキストメッセージのテキストを取得する
        $message = $event->getText();
        // イベントのリプライトークンを取得する
        $replyToken = $event->getReplyToken();
        // 入力された文字が「明日の天気は？」かどうかで応答メッセージを変更する
        if ($message === "明日の天気は？") {
          $textMessage = new TextMessageBuilder("https://line.me/R/nv/location/");
          $bot->replyMessage($replyToken, $textMessage);
        } else {
          $textMessage = new TextMessageBuilder("ごめんなさい、このメッセージは対応していません。");
          $bot->replyMessage($replyToken, $textMessage);
        }
      }

      if ($event instanceof LocationMessage) {
        // 緯度・軽度を取得
        $latitude = $event->getLatitude();
        $longitude = $event->getLongitude();
        // API
        $weatherAPI = env("WEATHER_API");
      }
      return;
    }
  }
}
