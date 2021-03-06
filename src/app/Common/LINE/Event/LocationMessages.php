<?php

namespace App\Common\LINE\Event;

// Common
use FlexMessage;
use Guzzle;
use Util;

// LINE
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Illuminate\Support\Facades\Log;

class LocationMessages
{
  // OpenWeatherからguzzleでデータを取得
  public static function getWeatherData($event)
  {
    // 緯度・経度を取得
    $latitude = $event->getLatitude();
    $longitude = $event->getLongitude();

    // API
    $weatherAPI = env("WEATHER_API");

    // OpenWeather
    $openWeather_url = "https://api.openweathermap.org/data/2.5/onecall?lat=" . $latitude . "&lon=" . $longitude . "&units=metric&lang=ja&appid=" . $weatherAPI;

    //common->guzzle
    $weathers = Guzzle::getGuzzle($openWeather_url);

    // JSON->Arrayに変換
    $weathers = json_decode($weathers, true);

    return $weathers;
  }

  // getWeatherDataを整形する
  public static function dataFormatting($event)
  {
    // getWeatherDataを取得
    $weathers = self::getWeatherData($event);

    // 時刻
    $time = $weathers["daily"][0]["dt"];
    $time = date("Y/m/d", $time);
    // 天気予報
    $weatherInformation = $weathers["daily"][0]["weather"][0]["description"];
    // 体感温度（ファッション）（朝、日中、夕方、夜）
    $mornTemperature = $weathers["daily"][0]["feels_like"]["morn"];
    $dayTemperature = $weathers["daily"][0]["feels_like"]["day"];
    $eveTemperature = $weathers["daily"][0]["feels_like"]["eve"];
    $nightTemperature = $weathers["daily"][0]["feels_like"]["night"];

    // 最高気温で洋服を分岐する
    $arrayTemperature = array($mornTemperature, $dayTemperature, $eveTemperature, $nightTemperature);
    $highestTemperature = max($arrayTemperature);

    if ($highestTemperature >= 26) {
      $fashionAdvice = "暑い！半袖が活躍する時期です。少し歩くだけで汗ばむ気温なので半袖1枚で大丈夫です。ハットや日焼け止めなどの対策もしましょう";
      $imageURL = "https://uploads-ssl.webflow.com/603c87adb15be3cb0b3ed9b5/60aa3c44153071e6df530eb7_71.png";
    } else if ($highestTemperature >= 21) {
      $fashionAdvice = "半袖と長袖の分かれ目の気温です。日差しのある日は半袖を、曇りや雨で日差しがない日は長袖がおすすめです。この気温では、半袖の上にライトアウターなどを着ていつでも脱げるようにしておくといいですね！";
      $imageURL = "https://uploads-ssl.webflow.com/603c87adb15be3cb0b3ed9b5/6056e58a5923ad81f73ac747_10.png";
    } else if ($highestTemperature >= 16) {
      $fashionAdvice = "レイヤードスタイルが楽しめる気温です。ちょっと肌寒いかな？というくらいの過ごしやすい時期なので目一杯ファッションを楽しみましょう！日中と朝晩で気温差が激しいので羽織ものを持つことを前提としたコーディネートがおすすめです。";
      $imageURL = "https://uploads-ssl.webflow.com/603c87adb15be3cb0b3ed9b5/6087da411a3ce013f3ddcd42_66.png";
    } else if ($highestTemperature >= 12) {
      $fashionAdvice = "じわじわと寒さを感じる気温です。ライトアウターやニットやパーカーなどが活躍します。この時期は急に暑さをぶり返すことも多いのでこのLINEで毎日天気を確認してくださいね！";
      $imageURL = "https://uploads-ssl.webflow.com/603c87adb15be3cb0b3ed9b5/6056e498e7d26507413fd853_4.png";
    } else if ($highestTemperature >= 7) {
      $fashionAdvice = "そろそろ冬本番です。冬服の上にアウターを羽織ってちょうどいいくらいです。ただし室内は暖房が効いていることが多いので脱ぎ着しやすいコーディネートがおすすめです！";
      $imageURL = "https://uploads-ssl.webflow.com/603c87adb15be3cb0b3ed9b5/6056e4de7156326ff560b1a1_6.png";
    } else {
      $fashionAdvice = "凍えるほどの寒さです。しっかり厚着して、マフラーや手袋、ニット帽などの冬小物もうまく使って防寒対策をしましょう！";
      $imageURL = "https://uploads-ssl.webflow.com/603c87adb15be3cb0b3ed9b5/6056ebd3ea0ff76dfc900633_48.png";
    }

    // 上記の必要項目を配列にする
    $weatherArray = array($time, $imageURL, $weatherInformation, $mornTemperature, $dayTemperature, $eveTemperature, $nightTemperature, $fashionAdvice);

    // common->FlexMessage
    $messages = FlexMessage::createFlexMessage($weatherArray);

    return $messages;
  }

  // メッセージを送る
  public static function sendReplyMessage($event)
  {
    //Utilから値を取得
    $channelAccessToken = Util::getChannelAccessToken();
    $replyToken = Util::getReplyToken($event);

    // 配列を取得
    $messages = self::dataFormatting($event);

    // JSON化する
    $result = json_encode(['replyToken' => $replyToken, 'messages' => [$messages]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    Log::info($result);

    try {
      $curl = curl_init();
      //POSTリクエスト
      curl_setopt($curl, CURLOPT_POST, true);
      //ヘッダを指定
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $channelAccessToken, 'Content-type: application/json'));
      //リクエストURL
      curl_setopt($curl, CURLOPT_URL, 'https://api.line.me/v2/bot/message/reply');
      //送信するデータ
      curl_setopt($curl, CURLOPT_POSTFIELDS, $result);
      // 実行する
      curl_exec($curl);
      // 閉じる
      curl_close($curl);
    } catch (Exception $err) {
      Log::info($err);
    }
  }
}
