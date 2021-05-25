<?php

namespace App\Library\LINE\Builder;

class FlexMessages
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
        "url" => $message[1],
        "size" => "full"
      ],
      "body" => [
        "type" => "box",
        "layout" => "vertical",
        "contents" => [
          [
            "type" => "text",
            "text" => "天気は、「" . $message[2] . "」です",
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
            "text" => "朝：" . $message[3] . "℃",
            "margin" => "sm",
            "size" => "sm",
            "color" => "#C8BD16"
          ],
          [
            "type" => "text",
            "text" => "日中：" . $message[4] . "℃",
            "margin" => "sm",
            "size" => "sm",
            "color" => "#789BC0"
          ],
          [
            "type" => "text",
            "text" => "夕方：" . $message[5] . "℃",
            "margin" => "sm",
            "size" => "sm",
            "color" => "#091C43"
          ],
          [
            "type" => "text",
            "text" => "夜：" . $message[6] . "℃",
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
            "text" => $message[7],
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
