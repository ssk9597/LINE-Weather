<?php

namespace App\Library;

class FlexMessages
{
  // FlexMessageのテンプレート
  public static function getFlexMessageTemplate()
  {
    return [
      "type" => "bubble",
      "header" => [
        "type" => "box",
        "layout" => "vertical",
        "contents" => [
          [
            "type" => "text",
            "text" => "2021/05/25",
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
            "text" => "天気は、「薄い雲」です",
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
            "text" => "朝：16.11℃",
            "margin" => "sm",
            "size" => "sm",
            "color" => "#C8BD16"
          ],
          [
            "type" => "text",
            "text" => "日中：25.13℃",
            "margin" => "sm",
            "size" => "sm",
            "color" => "#789BC0"
          ],
          [
            "type" => "text",
            "text" => "夕方：19.9℃",
            "margin" => "sm",
            "size" => "sm",
            "color" => "#091C43"
          ],
          [
            "type" => "text",
            "text" => "夜：15.64℃",
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
            "text" => "シャツなど羽織りものがあると過ごしやすいです",
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
