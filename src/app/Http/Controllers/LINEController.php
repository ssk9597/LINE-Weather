<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Service
use App\Services\LINEService;

class LINEController extends Controller
{
  public function sendMessage(LINEService $line_service, Request $request)
  {
    $line_service->sendMessage($request);
  }
}
