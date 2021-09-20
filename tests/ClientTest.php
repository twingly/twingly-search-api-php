<?php

namespace Twingly\Tests;

use Twingly\Client;
use Twingly\AuthException;
use Twingly\Query;

class ClientTest extends \PHPUnit\Framework\TestCase {
    public static function setUpBeforeClass(): void {
        if(!getenv('TWINGLY_SEARCH_KEY')) {
            putenv('TWINGLY_SEARCH_KEY=test-key');
        }
    }

    public function setUp(): void {
        \VCR\VCR::configure()
            ->setCassettePath('tests/fixtures/vcr_cassettes')
            ->enableRequestMatchers(array('method', 'url'));
    }

    public function testNew() {
        $c = new Client('test-key');
        $this->assertEquals($c->user_agent, sprintf(Client::DEFAULT_USER_AGENT, Client::VERSION));
    }

    public function testWithoutApiKeyAsParameter() {
        $c = new Client();
        $this->assertEquals($c->api_key, getenv('TWINGLY_SEARCH_KEY'));
    }

    public function testWithNoApiKeyAtAll() {
        $temp_key = getenv('TWINGLY_SEARCH_KEY');
        putenv('TWINGLY_SEARCH_KEY=');

        try {
            $c = new Client();
            $this->fail('Should throw AuthException.');
        } catch (AuthException $e) {
            putenv('TWINGLY_SEARCH_KEY=' . $temp_key);
            $this->assertEquals('No API key has been provided.', $e->getMessage());
        }
    }

    public function testWithOptionalUserAgentGiven() {
        $c = new Client(false, 'Test User-Agent');
        $this->assertEquals($c->user_agent, 'Test User-Agent');
    }

    public function testQuery() {
        $c = new Client();
        $q = $c->query();
        $this->assertTrue($q instanceof Query);
    }

    public function testExecuteQueryWithInvalidAPIKey() {
        \VCR\VCR::turnOn();
        \VCR\VCR::insertCassette('search_without_valid_api_key');

        $temp_key = getenv('TWINGLY_SEARCH_KEY');
        putenv('TWINGLY_SEARCH_KEY=wrong');
        $c = new Client();
        try {
            $q = $c->query();
            $q->search_query = 'something';
            $c->execute_query($q);
            $this->fail('Should throw AuthException.');
        } catch (AuthException $e) {
            $this->assertStringContainsString('Unauthorized', $e->getMessage());
            putenv('TWINGLY_SEARCH_KEY=' . $temp_key);
        }
        \VCR\VCR::turnOff();
    }

    public function testEndpointUrl() {
        $c = new Client();
        $this->assertEquals($c->endpoint_url(), sprintf("%s%s", Client::BASE_URL, Client::SEARCH_PATH));
    }
}
