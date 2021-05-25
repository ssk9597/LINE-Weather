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
use ButtonMessages;
use Guzzle;

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

    // text
    $btn_text = "現在地を送る";
    $btn_url = "https://line.me/R/nv/location/";
    $btn_builder = "現在地を送ってください";

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

      if ($event instanceof LocationMessage) {
        // 緯度・経度を取得
        $latitude = $event->getLatitude();
        $longitude = $event->getLongitude();

        // API
        $weatherAPI = env("WEATHER_API");
        // OpenWeather
        $openWeather_url = "https://api.openweathermap.org/data/2.5/onecall?lat=" . $latitude . "&lon=" . $longitude . "&units=metric&lang=ja&appid=" . $weatherAPI;
        //guzzle
        $weathers = Guzzle::getGuzzle($openWeather_url);
        // JSON->Arrayに変換
        $weathers = json_decode($weathers, true);

        // 時刻
        $time = $weathers["daily"][1]["dt"];
        $time = date("Y/m/d", $time);
        // 天気予報
        $weatherInformation = $weathers["daily"][1]["weather"][0]["description"];
        // 体感温度（ファッション）（朝、日中、夕方、夜）
        $mornTemperature = $weathers["daily"][1]["feels_like"]["morn"];
        $dayTemperature = $weathers["daily"][1]["feels_like"]["day"];
        $eveTemperature = $weathers["daily"][1]["feels_like"]["eve"];
        $nightTemperature = $weathers["daily"][1]["feels_like"]["night"];

        // 最高気温で洋服を分岐する
        $arrayTemperature = array($mornTemperature, $dayTemperature, $eveTemperature, $nightTemperature);

        $highestTemperature = max($arrayTemperature);
        if ($highestTemperature >= 26) {
          $fashionAdvice = "暑い！半袖が活躍する時期です。少し歩くだけで汗ばむ気温なので半袖1枚で大丈夫です。ハットや日焼け止めなどの対策もしましょう";
        } else if ($highestTemperature >= 21) {
          $fashionAdvice = "半袖と長袖の分かれ目の気温です。日差しのある日は半袖を、曇りや雨で日差しがない日は長袖がおすすめです。この気温では、半袖の上にライトアウターなどを着ていつでも脱げるようにしておくといいですね！";
        } else if ($highestTemperature >= 16) {
          $fashionAdvice = "レイヤードスタイルが楽しめる気温です。ちょっと肌寒いかな？というくらいの過ごしやすい時期なので目一杯ファッションを楽しみましょう！日中と朝晩で気温差が激しいので羽織ものを持つことを前提としたコーディネートがおすすめです。";
        } else if ($highestTemperature >= 12) {
          $fashionAdvice = "じわじわと寒さを感じる気温です。ライトアウターやニットやパーカーなどが活躍します。この時期は急に暑さをぶり返すことも多いのでこのLINEで毎日天気を確認してくださいね！";
        } else if ($highestTemperature >= 7) {
          $fashionAdvice = "そろそろ冬本番です。冬服の上にアウターを羽織ってちょうどいいくらいです。ただし室内は暖房が効いていることが多いので脱ぎ着しやすいコーディネートがおすすめです！";
        } else {
          $fashionAdvice = "凍えるほどの寒さです。しっかり厚着して、マフラーや手袋、ニット帽などの冬小物もうまく使って防寒対策をしましょう！";
        }

        // 上記の必要項目を配列にする
        $weatherArray = array($time, $weatherInformation, $mornTemperature, $dayTemperature, $eveTemperature, $nightTemperature, $fashionAdvice);

        // JSONにする
        $messages = json_encode(FlexMessages::createFlexMessage($weatherArray), JSON_UNESCAPED_UNICODE);
        Log::info($messages);

        //guzzle
        $line_url = "https://api.line.me/v2/bot/message/reply";
        $client = new Client();
        $response = $client->request(
          "POST",
          $line_url,
          [
            "headers" => [
              "Content-Type" => "application/json",
              "Authorization" => "Bearer " . $channelAccessToken,
            ],
            "form_params" => [
              "replyToken" => $replyToken,
              "messages" => $messages
            ]
          ]
        );
      }
      return;
    }
  }
}
