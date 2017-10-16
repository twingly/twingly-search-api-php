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

    function testQueryWithoutSearchPattern() {
        try {
            $q = $this->client->query();
            $q->url();
            $this->fail('Should throw QueryException');
        } catch (QueryException $e) {
            $this->assertTrue($e instanceof QueryException);
        }
    }

    function testQueryWithEmptySearchQuery(){
        try {
            $q = $this->client->query();
            $q->search_query = '';
            $q->url();
            $this->fail('Should throw QueryException');
        } catch (QueryException $e) {
            $this->assertTrue($e instanceof QueryException);
        }
    }

    function testQueryShouldAddStartTime(){
        $q = $this->client->query();
        $q->search_query = 'spotify';
        $q->start_time = \DateTime::createFromFormat('Y-m-d H:i:s', '2012-12-28 09:01:22');
        $this->assertContains('start-date:2012-12-28T09:01:22', $q->request_parameters()['q']);
    }

    function testQueryWithStartTimeInTimezoneOtherThanUtc(){
        $q = $this->client->query();
        $q->search_query = 'spotify';
        $q->start_time = \DateTime::createFromFormat('Y-m-d H:i:s P', '2012-12-28 09:01:22 +01:00');
        $this->assertContains('start-date:2012-12-28T08:01:22', $q->request_parameters()['q']);
    }

    function testStartTimeUtcConversionDoesntModifyOriginalObject(){
        $q = $this->client->query();
        $q->search_query = 'spotify';
        $q->start_time = \DateTime::createFromFormat('Y-m-d H:i:s P', '2012-12-28 09:01:22 +01:00');
        $q->request_parameters();
        $this->assertEquals($q->start_time->format('Y-m-d H:i:s P'), '2012-12-28 09:01:22 +01:00');
    }

    function testQueryShouldAddEndTime(){
        $q = $this->client->query();
        $q->search_query = 'spotify';
        $q->end_time = \DateTime::createFromFormat('Y-m-d H:i:s', '2012-12-28 09:01:22');
        $this->assertContains('end-date:2012-12-28T09:01:22', $q->request_parameters()['q']);
    }

    function testQueryWithEndTimeInTimezoneOtherThanUtc(){
        $q = $this->client->query();
        $q->search_query = 'spotify';
        $q->end_time = \DateTime::createFromFormat('Y-m-d H:i:s P', '2012-12-28 09:01:22 +01:00');
        $this->assertContains('end-date:2012-12-28T08:01:22', $q->request_parameters()['q']);
    }

    function testEndTimeUtcConversionDoesntModifyOriginalObject(){
        $q = $this->client->query();
        $q->search_query = 'spotify';
        $q->end_time = \DateTime::createFromFormat('Y-m-d H:i:s P', '2012-12-28 09:01:22 +01:00');
        $q->request_parameters();
        $this->assertEquals($q->end_time->format('Y-m-d H:i:s P'), '2012-12-28 09:01:22 +01:00');
    }

    function testQueryShouldEncodeUrlParameters() {
        $q = $this->client->query();
        $q->search_query = 'spotify';
        $q->end_time = \DateTime::createFromFormat('Y-m-d H:i:s', '2012-12-28 09:01:22');
        $this->assertContains('end-date%3A2012-12-28T09%3A01%3A22', $q->url_parameters());
    }

    function testQueryPattern() {
        $q = $this->client->query();
        $q->search_query = 'spotify';
        $this->assertContains('spotify', $q->url_parameters());
    }

    public function testExecuteQueryWithValidAPIKey() {
        \VCR\VCR::turnOn();
        \VCR\VCR::insertCassette('search_for_spotify_on_sv_blogs');
        \VCR\VCR::getEventDispatcher()->addListener(\VCR\VCREvents::VCR_BEFORE_RECORD, array($this, 'cleanRequest'));

        $q = $this->client->query();
        $q->search_query = 'spotify page-size:10 language:sv';
        $r = $q->execute();
        $this->assertGreaterThan(0, count($r->posts));

        \VCR\VCR::turnOff();
    }

    public function cleanRequest(\Symfony\Component\EventDispatcher\Event $event, $eventName)
    {
        $request = $event->getRequest();
        $url = $request->getUrl();
        $url = preg_replace('/apikey=.*&/', 'apikey=hidden&', $url);
        $request->setUrl($url);
    }
}
