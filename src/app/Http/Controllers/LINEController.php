<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// LINE
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;



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
  }
}
