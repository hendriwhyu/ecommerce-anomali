<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    abstract public function sendResponse($result, $message, $code) : JsonResponse;

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    abstract public function sendError($error, $errorMessages, $code) : JsonResponse;
}
