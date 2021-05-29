<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// LINE
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
// Library
use TextMessages;
use LocationMessages;
use Util;

class LINEController extends Controller
{
  public function sendMessage(Request $request)
  {
    //Webhookの処理
    $events = Util::getEventsByWebhook($request);

    foreach ($events as $event) {
      // eventがテキストメッセージの時
      if ($event instanceof TextMessage) {
        TextMessages::eventTextMessage($event);
      }

      // eventが位置情報メッセージの時
      if ($event instanceof LocationMessage) {
        LocationMessages::sendReplyMessage($event);
      }
      return;
    }
  }
}
