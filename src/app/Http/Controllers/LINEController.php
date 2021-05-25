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
use TextMessages;
use LocationMessages;
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

    foreach ($events as $event) {
      // イベントのリプライトークンを取得する
      $replyToken = $event->getReplyToken();
      // eventがテキストメッセージの時
      if ($event instanceof TextMessage) {
        TextMessages::eventTextMessage($event, $bot, $replyToken);
      }

      if ($event instanceof LocationMessage) {
        LocationMessages::getWeatherData($event);
        $messages = LocationMessages::dataFormattingJSON($event);

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
