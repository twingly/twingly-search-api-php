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


class QueryTest extends \PHPUnit_Framework_TestCase {
    private $client;

    function __construct() {
        $this->client = new Client();
    }

    function setUp() {
        \VCR\VCR::configure()->setCassettePath('tests/fixtures/vcr_cassettes');
    }

    function testQueryNew() {
        $q = $this->client->query();
        $this->assertTrue($q instanceof Query);
    }

    function testQueryWithoutClient() {
        try {
            $q = new Query();
            $this->fail('Should throw Extension');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \Exception);
        }
    }

    function testQueryWithValidPattern() {
        $q = $this->client->query();
        $q->pattern = 'christmas';
        $this->assertContains('xmloutputversion=2', $q->url());
    }

    function testQueryWithoutValidPattern() {
        try {
            $q = $this->client->query();
            $q->url();
            $this->fail('Should throw QueryException');
        } catch (QueryException $e) {
            $this->assertTrue($e instanceof QueryException);
        }
    }

    function testQueryWithEmptyPattern(){
        try {
            $q = $this->client->query();
            $q->pattern = '';
            $q->url();
            $this->fail('Should throw QueryException');
        } catch (QueryException $e) {
            $this->assertTrue($e instanceof QueryException);
        }
    }

    function testQueryShouldAddLanguage(){
        $q = $this->client->query();
        $q->pattern = 'spotify';
        $q->language = 'en';
        $this->assertEquals($q->request_parameters()['documentlang'], 'en');
    }

    function testQueryShouldAddStartTime(){
        $q = $this->client->query();
        $q->pattern = 'spotify';
        $q->start_time = \DateTime::createFromFormat('Y-m-d H:i:s', '2012-12-28 09:01:22');
        $this->assertEquals($q->request_parameters()['ts'], '2012-12-28 09:01:22');
    }

    function testQueryShouldAddEndTime(){
        $q = $this->client->query();
        $q->pattern = 'spotify';
        $q->end_time = \DateTime::createFromFormat('Y-m-d H:i:s', '2012-12-28 09:01:22');
        $this->assertEquals($q->request_parameters()['tsTo'], '2012-12-28 09:01:22');
    }

    function testQueryShouldEncodeUrlParameters() {
        $q = $this->client->query();
        $q->pattern = 'spotify';
        $q->end_time = \DateTime::createFromFormat('Y-m-d H:i:s', '2012-12-28 09:01:22');
        $this->assertContains('tsTo=2012-12-28+09%3A01%3A22', $q->url_parameters());
    }

    function testQueryPattern() {
        $q = $this->client->query();
        $q->pattern = 'spotify';
        $this->assertContains('searchpattern=spotify', $q->url_parameters());
    }

    public function testExecuteQueryWithInvalidAPIKey() {
        \VCR\VCR::turnOn();
        \VCR\VCR::insertCassette('search_for_spotify_on_sv_blogs');

        $q = $this->client->query();
        $q->pattern = 'spotify page-size:10';
        $q->language = 'sv';
        $r = $q->execute();
        $this->assertGreaterThan(0, count($r->posts));

        \VCR\VCR::turnOff();
    }
}