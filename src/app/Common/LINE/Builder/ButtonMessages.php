<?php

namespace App\Common\LINE\Builder;

// LINE
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;

class ButtonMessages
{
  public static function createButtonMessage($bot, $replyToken, $btn_text, $btn_url, $btn_message, $btn_builder)
  {
    $buttonURL = new UriTemplateActionBuilder($btn_text, $btn_url);
    $buttonMessage = new ButtonTemplateBuilder(null, $btn_message, null, [$buttonURL]);
    $bot->replyMessage($replyToken, new TemplateMessageBuilder($btn_builder, $buttonMessage));
  }
}
