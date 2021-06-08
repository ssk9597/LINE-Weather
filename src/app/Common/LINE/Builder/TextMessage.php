<?php

namespace App\Common\LINE\Builder;

class TextMessage
{
  // TextMessageを作成する
  public static function createTextMessage($message)
  {
    // return [
    //   [
    //     "type" => "text",
    //     "text" => $message[0] . "の天気は、「" . $message[1] . "」です",
    //   ],
    //   [
    //     "type" => "text",
    //     "text" =>
    //     "■体感気温\n" .
    //       "朝：" . $message[2] . "℃\n" .
    //       "日中：" . $message[3] . "℃\n" .
    //       "夕方：" . $message[4] . "℃\n" .
    //       "夜：" . $message[5] . "℃\n\n" .
    //       "■洋服アドバイス\n" . $message[6]
    //   ],
    // ];
    return [
      $message[0] . "の天気は、「" . $message[1] . "」です\n\n" .
        "■体感気温\n" .
        "朝：" . $message[2] . "℃\n" .
        "日中：" . $message[3] . "℃\n" .
        "夕方：" . $message[4] . "℃\n" .
        "夜：" . $message[5] . "℃\n\n" .
        "■洋服アドバイス\n" . $message[6]
    ];
  }
}
