<?php

namespace App\Common;

// Guzzle
use GuzzleHttp\Client;

class Guzzle
{
  public static function getGuzzle($url)
  {
    $client = new Client();
    $response = $client->request("GET", $url);
    return $response->getBody();
  }
}
