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

    function testWithPostContainingOneTag() {
        $data = self::getFixture('minimal_valid_result');
        $result = (new Parser())->parse($data);
        $actual_post = $result->posts[0];

        $expected_post = new Post();
        $expected_post->set_values([
           "url"          => "http://oppogner.blogg.no/1409602010_bare_m_ha.html",
           "title"        => "Bare MÅ ha!",
           "summary"      => "Ja, velkommen til høsten ...",
           "languageCode" => "no",
           "published"    => "2014-09-02 06:53:26Z",
           "indexed"      => "2014-09-02 09:00:53Z",
           "blogUrl"      => "http://oppogner.blogg.no/",
           "blogName"     => "oppogner",
           "authority"    => "1",
           "blogRank"     => "1",
           "tags"         => ["Blogg"],
        ]);

        $this->assertEquals($expected_post, $actual_post);
    }

    function testWithPostContainingMultipleTags() {
        $data = self::getFixture('minimal_valid_result');
        $result = (new Parser())->parse($data);
        $actual_post = $result->posts[1];

        $expected_summary = <<<POST_SUMMARY
Träning. Och Swedish House Mafia. Det verkar vara ett lyckat koncept. "Don't you worry child" och "Greyhound" är nämligen de två mest spelade träningslåtarna under januari 2013 på Spotify.

Relaterade inlägg:
Swedish House Mafia – ny låt!
Ny knivattack på Swedish House Mafia-konsert
Swedish House Mafia gör succé i USA
POST_SUMMARY;

        $expected_post = new Post();
        $expected_post->set_values([
            "url"          => "http://www.skvallernytt.se/hardtraning-da-galler-swedish-house-mafia",
            "title"        => "Hårdträning – då gäller Swedish House Mafia",
            "summary"      => $expected_summary,
            "languageCode" => "sv",
            "published"    => "2013-01-29 15:21:56Z",
            "indexed"      => "2013-01-29 15:22:52Z",
            "blogUrl"      => "http://www.skvallernytt.se/",
            "blogName"     => "Skvallernytt.se",
            "authority"    => "38",
            "blogRank"     => "4",
            "tags"         => ["Okategoriserat", "Träning", "greyhound", "koncept", "mafia"],
        ]);

        $this->assertEquals($expected_post, $actual_post);
    }

    function testWithPostContainingNoTags() {
      $data = self::getFixture('minimal_valid_result');
      $result = (new Parser())->parse($data);
      $actual_post = $result->posts[2];

      $expected_post = new Post();
      $expected_post->set_values([
          "url"          => "http://didriksinspesielleverden.blogg.no/1359472349_justin_bieber.html",
          "title"        => "Justin Bieber",
          "summary"      => "OMG! Justin Bieber Believe acoustic albumet er nå ute på spotify. Han er helt super. Love him. Personlig liker jeg best beauty and a beat og as long as you love me, kommenter gjerne hva dere synes! <3 #sus YOLO",
          "languageCode" => "no",
          "published"    => "2013-01-29 15:12:29Z",
          "indexed"      => "2013-01-29 15:14:37Z",
          "blogUrl"      => "http://didriksinspesielleverden.blogg.no/",
          "blogName"     => "Didriksinspesielleverden",
          "authority"    => "0",
          "blogRank"     => "1",
          "tags"         => [],
      ]);

      $this->assertEquals($expected_post, $actual_post);
    }
}
