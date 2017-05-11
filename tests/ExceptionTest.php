<?php

namespace Twingly\Tests;

use Twingly\Client;
use Twingly\AuthException;
use Twingly\ServerException;
use Twingly\Exception;
use Twingly\Query;
use Twingly\QueryException;


class ExceptionTest extends \PHPUnit_Framework_TestCase {
    function testFromApiResponse() {
        try {
            $ex = new Exception();
            $ex->from_api_response(401);
            $this->fail('Should throw AuthException');
        } catch (AuthException $e) {
            $this->assertContains('401', $e->getMessage());
        }

        try {
            $ex = new Exception();
            $ex->from_api_response(500);
            $this->fail('Should throw ServerException');
        } catch (ServerException $e) {
            $this->assertContains('500', $e->getMessage());
        }

        try {
            $ex = new Exception();
            $ex->from_api_response(400);
            $this->fail('Should throw QueryException');
        } catch (QueryException $e) {
            $this->assertContains('400', $e->getMessage());
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