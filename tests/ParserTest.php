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
    function testWithValidResult() {
        $data = file_get_contents(dirname(__DIR__) . '/tests/fixtures/valid_result.xml');
        $r = (new Parser())->parse($data);
        $this->assertTrue($r instanceof Result);
    }

    function testWithValidResultContainingNonBlogs() {
        $data = file_get_contents(dirname(__DIR__) . '/tests/fixtures/valid_non_blog_result.xml');
        $r = (new Parser())->parse($data);
        $this->assertTrue($r instanceof Result);
        $this->assertEquals(count($r->posts), 1);
    }

    function testWithNonexistentApiKeyResult() {
        try {
            $data = file_get_contents(dirname(__DIR__) . '/tests/fixtures/nonexistent_api_key_result.xml');
            $r = (new Parser())->parse($data);
            $this->fail('Should throw Auth exception');
        } catch (AuthException $e) {
            $this->assertEquals('The API key does not exist.', $e->getMessage());
        }
    }

    function testWithUnauthorizedApiKeyResult() {
        try {
            $data = file_get_contents(dirname(__DIR__) . '/tests/fixtures/unauthorized_api_key_result.xml');
            $r = (new Parser())->parse($data);
            $this->fail('Should throw Auth exception');
        } catch (AuthException $e) {
            $this->assertEquals('The API key does not grant access to the Search API.', $e->getMessage());
        }
    }

    function testWithServiceUnavailableResult() {
        try {
            $data = file_get_contents(dirname(__DIR__) . '/tests/fixtures/service_unavailable_result.xml');
            $r = (new Parser())->parse($data);
            $this->fail('Should throw Server exception');
        } catch (ServerException $e) {
            $this->assertEquals('Authentication service unavailable.', $e->getMessage());
        }
    }

    function testWithUndefinedErrorResult() {
        try {
            $data = file_get_contents(dirname(__DIR__) . '/tests/fixtures/undefined_error_result.xml');
            $r = (new Parser())->parse($data);
            $this->fail('Should throw Server exception');
        } catch (ServerException $e) {
            $this->assertEquals('Something went wrong.', $e->getMessage());
        }
    }

    function testWithNonXmlResult() {
        try {
            $data = file_get_contents(dirname(__DIR__) . '/tests/fixtures/non_xml_result.xml');
            $r = (new Parser())->parse($data);
            $this->fail('Should throw Server exception');
        } catch (ServerException $e) {
            $this->assertEquals('503 Service Unavailable', $e->getMessage());
        }
    }
}

/*
 * from __future__ import unicode_literals
import unittest

import twingly_search

class ParserTest(unittest.TestCase):



    def test_with_undefined_error_result(self):
        with self.assertRaises(twingly_search.TwinglyServerException):
            data = open("./tests/fixtures/non_xml_result.xml", 'r').read()
            if hasattr(data, 'decode'):
                data = data.decode("utf-8")
            r = twingly_search.Parser().parse(data)
 */