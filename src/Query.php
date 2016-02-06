<?php

namespace Twingly;

class Query {
    public $pattern = '';
    public $language = '';
    public $client = false;
    public $start_time = false;
    public $end_time = false;

    function __construct($client) {
        $this->client = $client;
    }

    function url() {
        return sprintf("%s?%s", $this->client->endpoint_url(), $this->url_parameters());
    }

    function execute() {
        return $this->client->execute_query($this);
    }

    function url_parameters() {
        return http_build_query($this->request_parameters());
    }

    function request_parameters() {
        if(empty($this->pattern)) {
            throw new \Twingly\QueryException("Missing pattern");
        }

        return [
            'key' => $this->client->api_key,
            'searchpattern' => $this->pattern,
            'documentlang' => $this->language,
            'ts' => $this->_ts(),
            'tsTo' => $this->_tsTo(),
            'xmloutputversion' => 2
        ];
    }

    function _ts() {
        if($this->start_time instanceof \DateTime) {
            return date('Y-m-d H:i:s', $this->start_time->getTimestamp());
        } else {
            return '';
        }
    }

    function _tsTo() {
        if($this->end_time instanceof \DateTime) {
            return date('Y-m-d H:i:s', $this->end_time->getTimestamp());
        } else {
            return '';
        }
    }
}