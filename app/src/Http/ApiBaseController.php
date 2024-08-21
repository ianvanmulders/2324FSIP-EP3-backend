<?php

namespace Http;

abstract class ApiBaseController
{
    protected ?array $httpBody;

    public function __construct()
    {
        // Parse the HTTP request body assuming it contains plain JSON
        $this->httpBody = json_decode(file_get_contents('php://input'), true);

        // CORS: API response can be shared with javascript code from origin ALLOW_ORIGIN
        header('Access-Control-Allow-Origin: ' . ALLOW_ORIGIN);

        // set the Content-type header of the HTTP response to JSON
        header('Content-type: application/json; charset=UTF-8');
    }

    protected function message(int $httpCode, string $message) {
        http_response_code($httpCode);
        $answer = ['message' => $message];
        echo json_encode($answer);
    }

    public function methodNotAllowed() {
        $this->message(405, 'HTTP request method ' .  $_SERVER['REQUEST_METHOD']. ' not allowed.');
    }
}