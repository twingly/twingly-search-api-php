<?php

namespace Twingly;

class Exception extends \Exception {
    public function from_api_response($code, $message = '') {
        switch(substr(((string)$code),0,3)) {
            case "400":
            case "404":
                throw new QueryException("$message (code: $code)");
            case "401":
            case "402":
                throw new AuthException("$message (code: $code)");
            default:
                throw new ServerException("$message (code: $code)");
        }
    }
}
