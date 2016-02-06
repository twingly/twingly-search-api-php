<?php

namespace Twingly;

class Exception extends \Exception {
    public function from_api_response_message($message) {
        if (strpos($message, 'API key') !== FALSE) {
            throw new AuthException($message);
        } else {
            throw new ServerException($message);
        }
    }
}