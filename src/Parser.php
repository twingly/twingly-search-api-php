<?php

namespace Twingly;

class Parser {
    /**
     * Parse an API response body.
     *
     * @param string $document containing an API response XML text
     * @return Result
     * @throws AuthException
     * @throws ServerException
     */
    public function parse($document) {
        $doc = simplexml_load_string($document);

        if($doc->getName() == 'html') {
            throw new \Twingly\ServerException((string)$doc->xpath('//text()')[0]);
        }

        if(isset($doc->operationResult)) {
            if((string)$doc->operationResult->attributes()->resultType == 'failure') {
                if(strpos((string)$doc->operationResult, 'API key') !== FALSE) {
                    throw new AuthException((string)$doc->operationResult);
                } else {
                    throw new ServerException((string)$doc->operationResult);
                }
            }
        }

        return $this->_create_result($doc);
    }

    private function _create_result($data_node) {
        $result = new Result();

        $result->number_of_matches_returned = (int)$data_node->attributes()->numberOfMatchesReturned;
        $result->seconds_elapsed = (float)$data_node->attributes()->secondsElapsed;
        $result->number_of_matches_total = (int)$data_node->attributes()->numberOfMatchesTotal;

        $result->posts = [];

        foreach($data_node->xpath('//post[@contentType="blog"]') as $p) {
            $result->posts[] = $this->_parse_post($p);
        }

        return $result;
    }

    private function _parse_post($element) {
        $post_params = [
            'tags' => []
        ];

        foreach($element->children() as $child) {
            if($child->getName() == 'tags') {
                $post_params[$child->getName()] = $this->_parse_tags($child);
            } else {
                $post_params[$child->getName()] = (string)$child;
            }
        }

        $post = new Post();
        $post->set_values($post_params);
        return $post;
    }

    private function _parse_tags($element) {
        $tags = [];
        foreach($element->xpath('//tag') as $tag) {
            $tags[] = (string)$tag;
        }
        return $tags;
    }

    private function _handle_failure($failure) {
        $ex = new \Twingly\Exception();
        $ex->from_api_response_message($failure);
    }

    private function _handle_non_xml_document($document){
        $response_text = (string)$document->xpath('//text()')[0];
        throw new \Twingly\ServerException($response_text);
    }
}