<?php

namespace Twingly\Tests;

use Twingly\Client;
use Twingly\AuthException;
use Twingly\Parser;
use Twingly\Result;
use Twingly\Post;
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
        $data = self::getFixture('minimal_valid_result');
        $r = (new Parser())->parse($data);
        $this->assertTrue($r instanceof Result);
        $this->assertEquals(3, $r->number_of_matches_returned);
        $this->assertEquals(3122050, $r->number_of_matches_total);
        $this->assertEquals(0.369, $r->seconds_elapsed);

        $actual_post = $r->posts[0];
        $expected_post = new Post();
        $this->assertStringStartsWith("Det vart en förmiddag på boxen med en brud som jag", $actual_post->text);
        $actual_post->text = "";
        $expected_post->set_values([
            "id" => '16405819479794412880',
            "author" => "klivinihemligheten",
            "url" => "http://nouw.com/klivinihemligheten/planering---men-dalig-30016048",
            "title" => "Planering - men dålig",
            "text" => "",
            "languageCode" => "sv",
            "locationCode" => "se",
            "coordinates" => [],
            "images" => [],
            "links" => [],
            "publishedAt" => "2017-05-04T06:50:59Z",
            "indexedAt" => "2017-05-04T06:51:23Z",
            "reindexedAt" => "2017-05-04T08:51:23Z",
            "inlinksCount" => 0,
            "blogId" => '5312283800049632348',
            "blogName" => "Love life like a student",
            "blogUrl" => "http://nouw.com/klivinihemligheten",
            "authority" => "0",
            "blogRank" => "1",
            "tags" => ["Ätas & drickas", "Universitet & studentlivet", "Träning", "To to list"],
        ]);
        $this->assertEquals($expected_post, $actual_post);

        // repeat with last post
        $actual_post = $r->posts[count($r->posts) - 1];

        $this->assertStringStartsWith("Hmm.... Vad ska man börja ??  Jag vet inte riktigt vad min gnista", $actual_post->text);
        $actual_post->text = "";

        $expected_post->set_values([
            "id" => '2770252465384762934',
            "author" => "maartiinasvardag",
            "url" => "http://nouw.com/maartiinasvardag/god-formiddag-30016041",
            "title" => "God förmiddag! ☀",
            "text" => "",
            "languageCode" => "sv",
            "locationCode" => "se",
            "coordinates" => [],
            "images" => [],
            "links" => [],
            "publishedAt" => "2017-05-04T06:49:50Z",
            "indexedAt" => "2017-05-04T06:50:07Z",
            "reindexedAt" => "0001-01-01T00:00:00Z",
            "inlinksCount" => 0,
            "blogId" => '1578135310841173675',
            "blogName" => "maartiinasvardag blogg",
            "blogUrl" => "http://nouw.com/maartiinasvardag",
            "authority" => "0",
            "blogRank" => "1",
            "tags" => [],
        ]);

    }


    function testWithValidEmptyResult() {
        $data = self::getFixture('valid_empty_result');
        $r = (new Parser())->parse($data);
        $this->assertTrue($r instanceof Result);
        $this->assertEquals(count($r->posts), 0);
        $this->assertEquals($r->number_of_matches_total, 0);
        $this->assertEquals($r->number_of_matches_returned, 0);
    }

    function testWithNonexistentApiKeyResult() {
        try {
            $data = self::getFixture('nonexistent_api_key_result');
            $r = (new Parser())->parse($data);
            $this->fail('Should throw Auth exception');
        } catch (QueryException $e) {
            $this->assertEquals('Parameter apikey may not be empty (code: 40001)', $e->getMessage());
        }
    }

    function testWithUnauthorizedApiKeyResult() {
        try {
            $data = self::getFixture('unauthorized_api_key_result');
            $r = (new Parser())->parse($data);
            $this->fail('Should throw Auth exception');
        } catch (AuthException $e) {
            $this->assertEquals('Unauthorized (code: 40101)', $e->getMessage());
        }
    }

    function testWithServiceUnavailableResult() {
        try {
            $data = self::getFixture('service_unavailable_result');
            $r = (new Parser())->parse($data);
            $this->fail('Should throw Server exception');
        } catch (ServerException $e) {
            $this->assertEquals('Authentication service unavailable (code: 50301)', $e->getMessage());
        }
    }

    function testWithUndefinedErrorResult() {
        try {
            $data = self::getFixture('undefined_error_result');
            $r = (new Parser())->parse($data);
            $this->fail('Should throw Server exception');
        } catch (ServerException $e) {
            $this->assertEquals('Internal Server Error (code: 50001)', $e->getMessage());
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

    function testWithLinks() {
        $data = self::getFixture('valid_links_result');
        $result = (new Parser())->parse($data);
        $actual_post = $result->posts[0];

        $this->assertEquals([
            "https://1.bp.blogspot.com/-4uNjjiNQiug/WKguo1sBxwI/AAAAAAAAqKE/_eR7cY8Ft3cd2fYCx-2yXK8AwSHE_A2GgCLcB/s1600/aaea427ee3eaaf8f47d650f48fdf1242.jpg",
            "http://www.irsn.fr/EN/newsroom/News/Pages/20170213_Detection-of-radioactive-iodine-at-trace-levels-in-Europe-in-January-2017.aspx",
            "https://www.t.co/2P4IDmovzH",
            "https://www.twitter.com/Strat2Intel/status/832710701730844672"
        ], $actual_post->links);
    }

    function testWithIncompleteResult() {
        $data = self::getFixture('incomplete_result');
        $result = (new Parser())->parse($data);
        $this->assertTrue($result->incomplete_result);
    }

    function testWithCompleteResult() {
        $data = self::getFixture('valid_empty_result');
        $result = (new Parser())->parse($data);
        $this->assertFalse($result->incomplete_result);
    }
}
