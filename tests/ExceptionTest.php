<?php

namespace Twingly\Tests;

use Twingly\Client;
use Twingly\AuthException;
use Twingly\ServerException;
use Twingly\Exception;
use Twingly\Query;
use Twingly\QueryException;


class ExceptionTest extends \PHPUnit_Framework_TestCase {
    function testFromApiResponseMessage() {
        try {
            $ex = new Exception();
            $ex->from_api_response_message('... API key ...');
            $this->fail('Should throw AuthException');
        } catch (AuthException $e) {
            $this->assertEquals('... API key ...', $e->getMessage());
        }

        try {
            $ex = new Exception();
            $ex->from_api_response_message('server error');
            $this->fail('Should throw ServerException');
        } catch (ServerException $e) {
            $this->assertEquals('server error', $e->getMessage());
        }
    }

    function testEachException() {
        try {
            throw new AuthException('test');
            $this->fail('Should throw AuthException');
        } catch(AuthException $e) {
            $this->assertEquals('test', $e->getMessage());
        }

        try {
            throw new ServerException('test');
            $this->fail('Should throw ServerException');
        } catch(ServerException $e) {
            $this->assertEquals('test', $e->getMessage());
        }

        try {
            throw new QueryException('test');
            $this->fail('Should throw QueryException');
        } catch(QueryException $e) {
            $this->assertEquals('test', $e->getMessage());
        }
    }
}