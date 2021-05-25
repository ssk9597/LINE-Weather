<?php

namespace App\Library\LINE;

class FlexMessageBuilder
{
  // FlexMessageを作成する
  public static function createFlexMessage($message)
  {
    $contents = self::getFlexMessageTemplate($message);
    return ["type" => "flex", "altText" => "This is a Flex Message", "contents" => $contents];
  }
  // FlexMessageのテンプレート
  // 修正が必要なところは写真とアドバイスメッセージ
  public static function getFlexMessageTemplate($message)
  {
    return [
      "type" => "bubble",
      "header" => [
        "type" => "box",
        "layout" => "vertical",
        "contents" => [
          [
            "type" => "text",
            "text" => $message[0],
            "color" => "#FFFFFF",
            "align" => "center",
            "weight" => "bold"
          ]
        ]
      ],
      "hero" => [
        "type" => "image",
        "url" => "https =>//imgs.m-oo-m.com/uploads/public/5fa/b5d/d64/5fab5dd64eeed669757038.jpg",
        "size" => "full"
      ],
      "body" => [
        "type" => "box",
        "layout" => "vertical",
        "contents" => [
          [
            "type" => "text",
            "text" => "天気は、「" . $message[1] . "」です",
            "weight" => "bold",
            "align" => "center"
          ],
          [
            "type" => "text",
            "text" => "■体感気温",
            "margin" => "lg"
          ],
          [
            "type" => "text",
            "text" => "朝：" . $message[2] . "℃",
            "margin" => "sm",
            "size" => "sm",
            "color" => "#C8BD16"
          ],
          [
            "type" => "text",
            "text" => "日中：" . $message[3] . "℃",
            "margin" => "sm",
            "size" => "sm",
            "color" => "#789BC0"
          ],
          [
            "type" => "text",
            "text" => "夕方：" . $message[4] . "℃",
            "margin" => "sm",
            "size" => "sm",
            "color" => "#091C43"
          ],
          [
            "type" => "text",
            "text" => "夜：" . $message[5] . "℃",
            "margin" => "sm",
            "size" => "sm",
            "color" => "#004032"
          ],
          [
            "type" => "separator",
            "margin" => "xl"
          ],
          [
            "type" => "text",
            "text" => "■洋服アドバイス",
            "margin" => "xl"
          ],
          [
            "type" => "text",
            "text" => $message[6],
            "margin" => "sm",
            "wrap" => true,
            "size" => "xs"
          ]
        ],
      ],
      "styles" => [
        "header" => [
          "backgroundColor" => "#00B900"
        ],
        "hero" => [
          "separator" => false
        ]
      ]
    ];
  }
}
