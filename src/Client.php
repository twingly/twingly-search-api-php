<?php

namespace Twingly;

require 'vendor/autoload.php';

/**
 * Twingly Search API client
 * 
 * @package Twingly
 */
class Client {
    const VERSION = '1.0.0';
    const BASE_URL = 'https://api.twingly.com';
    const SEARCH_PATH = '/analytics/Analytics.ashx';
    const DEFAULT_USER_AGENT = 'Twingly Search PHP Client/%s';

    public $api_key = null;
    public $_user_agent = null;
    public $guzzle;

    /**
     * Client constructor.
     *
     * @param string $api_key Twingly Search API Key
     * @param string $user_agent User Agent for client
     */
    function __construct($api_key = false, $user_agent = false)
    {

        if(!$api_key) {
            $api_key = $this->_env_api_key();
        }

        if(!$api_key) {
            $this->_api_key_missing();
        }

        $this->api_key = $api_key;

        if(!$user_agent) {
            $user_agent = sprintf(Client::DEFAULT_USER_AGENT, Client::VERSION);
        }
        $this->_user_agent = $user_agent;

        $this->guzzle = new \GuzzleHttp\Client([
            'base_uri' => Client::BASE_URL,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => $this->_user_agent
            ],
            'verify' => false
        ]);
    }

    /**
     * Returns a new Query object connected to this client
     *
     * @return Query
     */
    public function query() {
        return new Query($this);
    }

    /**
     * Executes the given Query and returns the result
     *
     * @param Query $query the query to be executed
     *
     * @return Result
     * @throws AuthException
     * @throws QueryException
     * @throws ServerException
     */
    public function execute_query($query) {
        $response_body = $this->_get_response($query)->getBody();
        $parser = new Parser();
        return $parser->parse($response_body);
    }

    /**
     * @return string API endpoint URL
     */
    public function endpoint_url() {
        return sprintf('%s%s', Client::BASE_URL, Client::SEARCH_PATH);
    }

    private function _env_api_key() {
        return getenv('TWINGLY_SEARCH_KEY');
    }

    private function _get_response($query) {
        $response = $this->guzzle->get($query->url());
        if(($response->getStatusCode() >= 200)&&($response->getStatusCode() < 300)) {
            return $response;
        } else {
            if ($response->getStatusCode() >= 500) {
                throw new ServerException($response);
            } else {
                throw new QueryException($response);
            }
        }
    }

    private function _api_key_missing() {
        throw new AuthException('No API key has been provided.');
    }
}