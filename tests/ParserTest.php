<?php

namespace Twingly\Tests;

use Twingly\Client;
use Twingly\AuthException;
use Twingly\Parser;
use Twingly\Result;
use Twingly\ServerException;
use Twingly\Exception;
use Twingly\Query;
use Twingly\QueryException;


class ParserTest extends \PHPUnit_Framework_TestCase {
    static function getFixture($fixture_name) {
        $file_path = dirname(__DIR__) . "/tests/fixtures/{$fixture_name}.xml";
        return file_get_contents($file_path);
    }

    function testWithValidResult() {
        $data = self::getFixture('valid_result');
        $r = (new Parser())->parse($data);
        $this->assertTrue($r instanceof Result);
    }

    function testWithValidResultContainingNonBlogs() {
        $data = self::getFixture('valid_non_blog_result');
        $r = (new Parser())->parse($data);
        $this->assertTrue($r instanceof Result);
        $this->assertEquals(count($r->posts), 1);
    }

    function testWithNonexistentApiKeyResult() {
        try {
            $data = self::getFixture('nonexistent_api_key_result');
            $r = (new Parser())->parse($data);
            $this->fail('Should throw Auth exception');
        } catch (AuthException $e) {
            $this->assertEquals('The API key does not exist.', $e->getMessage());
        }
    }

    function testWithUnauthorizedApiKeyResult() {
        try {
            $data = self::getFixture('unauthorized_api_key_result');
            $r = (new Parser())->parse($data);
            $this->fail('Should throw Auth exception');
        } catch (AuthException $e) {
            $this->assertEquals('The API key does not grant access to the Search API.', $e->getMessage());
        }
    }

    function testWithServiceUnavailableResult() {
        try {
            $data = self::getFixture('service_unavailable_result');
            $r = (new Parser())->parse($data);
            $this->fail('Should throw Server exception');
        } catch (ServerException $e) {
            $this->assertEquals('Authentication service unavailable.', $e->getMessage());
        }
    }

    function testWithUndefinedErrorResult() {
        try {
            $data = self::getFixture('undefined_error_result');
            $r = (new Parser())->parse($data);
            $this->fail('Should throw Server exception');
        } catch (ServerException $e) {
            $this->assertEquals('Something went wrong.', $e->getMessage());
        }
    }

    function testWithNonXmlResult() {
        try {
            $data = self::getFixture('non_xml_result');
            $r = (new Parser())->parse($data);
            $this->fail('Should throw Server exception');
        } catch (ServerException $e) {
            $this->assertEquals('503 Service Unavailable', $e->getMessage());
        }
    }
}
