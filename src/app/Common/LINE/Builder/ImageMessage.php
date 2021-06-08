<?php

namespace App\Common\LINE\Builder;

class ImageMessages
{
  // ImageMessageを作成する
  public static function createImageMessage($message)
  {
    return [
      "type" => "image",
      "originalContentUrl" => $message[1],
      "previewImageUrl" => $message[1]
    ];
  }
}
