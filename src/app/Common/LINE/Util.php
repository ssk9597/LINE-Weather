<?php

namespace App\Common\LINE;

// LINE
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class Util
{
  // channelSecretを取得
  public static function getChannelSecret()
  {
    $channelSecret = env("LINE_CHANNEL_SECRET");
    return $channelSecret;
  }

  // channelAccessTokenを取得
  public static function getChannelAccessToken()
  {
    $channelAccessToken = env("LINE_CHANNEL_ACCESS_TOKEN");
    return $channelAccessToken;
  }

  // 署名を発行
  public static function getSignature($request)
  {
    $signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);
    return $signature;
  }

  // 署名があるかどうかを判別する
  public static function isSignature($request)
  {
    // 署名を取得
    $signature = self::getSignature($request);
    // channelSecretを取得
    $channelSecret = self::getChannelSecret();

    // 検証
    if (!SignatureValidator::validateSignature($request->getContent(), $channelSecret, $signature)) {
      return;
    }
  }

  // メッセージを送る準備
  public static function prepareToSendMessage()
  {
    // channelSecretの取得
    $channelSecret = self::getChannelSecret();
    // channelAccessTokenの取得
    $channelAccessToken = self::getChannelAccessToken();

    $httpClient = new CurlHTTPClient($channelAccessToken);
    $bot = new LINEBot($httpClient, ['channelSecret' => $channelSecret]);

    return $bot;
  }

  // webhookでeventsを取得
  public static function getEventsByWebhook($request)
  {
    // botを取得
    $bot = self::prepareToSendMessage();
    // signatureを取得
    $signature = self::getSignature($request);

    $events = $bot->parseEventRequest($request->getContent(), $signature);

    return $events;
  }

  // replyTokenを取得
  public static function getReplyToken($event)
  {
    $replyToken = $event->getReplyToken();
    return $replyToken;
  }
}
