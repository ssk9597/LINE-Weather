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
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
// logs
use Illuminate\Support\Facades\Log;
// Guzzle
use GuzzleHttp\Client;
// Library
use FlexMessages;

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

    foreach ($events as $event) {
      // イベントのリプライトークンを取得する
      $replyToken = $event->getReplyToken();
      // 今日か明日かを判別（true: 今日、false: 明日）
      $isToday = true;
      // eventがテキストメッセージの時
      if ($event instanceof TextMessage) {
        // テキストメッセージのテキストを取得する
        $message = $event->getText();
        // 入力された文字が「今日の洋服は？」「明日の洋服は？」かどうかで応答メッセージを変更する
        if ($message === "今日の洋服は？") {
          $buttonURL = new UriTemplateActionBuilder("現在地を送る", "https://line.me/R/nv/location/");
          $buttonMessage = new ButtonTemplateBuilder(null, "今日はどんな洋服にしようかな", null, [$buttonURL]);
          $bot->replyMessage($replyToken, new TemplateMessageBuilder("現在地を送ってください", $buttonMessage));
        } else if ($message === "明日の洋服は？") {
          $isToday = false;
          $buttonURL = new UriTemplateActionBuilder("現在地を送る", "https://line.me/R/nv/location/");
          $buttonMessage = new ButtonTemplateBuilder(null, "明日はどんな洋服にしようかな", null, [$buttonURL]);
          $bot->replyMessage($replyToken, new TemplateMessageBuilder("現在地を送ってください", $buttonMessage));
        } else {
          $textMessage = new TextMessageBuilder("ごめんなさい、このメッセージは対応していません。");
          $bot->replyMessage($replyToken, $textMessage);
        }
      }

      if ($event instanceof LocationMessage) {
        // 緯度・経度を取得
        $latitude = $event->getLatitude();
        $longitude = $event->getLongitude();
        // API
        $weatherAPI = env("WEATHER_API");
        // OpenWeather
        $url = "https://api.openweathermap.org/data/2.5/onecall?lat=" . $latitude . "&lon=" . $longitude . "&units=metric&lang=ja&appid=" . $weatherAPI;

        //guzzle
        $client = new Client();
        $response = $client->request("GET", $url);
        $weathers = $response->getBody();
        // JSON->Arrayに変換
        $weathers = json_decode($weathers, true);

        //log
        Log::info($weathers["daily"][1]);
        Log::info($isToday);
        Log::info(FlexMessages::getFlexMessageTemplate());

        // 時刻
        $time = $weathers["daily"][1]["dt"];
        $time = date("Y/m/d", $time);
        // 天気予報
        $weatherInformation = $weathers["daily"][1]["weather"][0]["description"];
        // 体感温度（ファッション）（朝、日中、夕方、夜）
        $mornTemperature = $weathers["daily"][1]["feels_like"]["morn"] . "℃";
        $dayTemperature = $weathers["daily"][1]["feels_like"]["day"] . "℃";
        $eveTemperature = $weathers["daily"][1]["feels_like"]["eve"] . "℃";
        $nightTemperature = $weathers["daily"][1]["feels_like"]["night"] . "℃";

        // メッセージ定型
        $message = "時刻: " . $time . ", 天気: " . $weatherInformation . ", 体感気温: 朝）" . $mornTemperature . ", 日中）" . $dayTemperature . ", 夕方）" . $eveTemperature . ", 夜）" . $nightTemperature;

        $textMessage = new TextMessageBuilder($message);
        $bot->replyMessage($replyToken, $textMessage);
      }
      return;
    }
  }
}
