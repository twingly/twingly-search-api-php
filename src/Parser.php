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
            $this->handle_non_xml_document($doc);
        }

        if($doc->getName() == 'error') {
                $this->handle_failure($doc);
        }

        return $this->create_result($doc);
    }

    private function create_result($data_node) {
        $result = new Result();

        $result->number_of_matches_returned = (int)$data_node->attributes()->numberOfMatchesReturned;
        $result->seconds_elapsed = (float)$data_node->attributes()->secondsElapsed;
        $result->number_of_matches_total = (int)$data_node->attributes()->numberOfMatchesTotal;
        $result->incomplete_result = $this->get_bool((string)$data_node->attributes()->incompleteResult);

        $result->posts = [];

        foreach($data_node->xpath('//post') as $p) {
            $result->posts[] = $this->parse_post($p);
        }

        return $result;
    }

    private function parse_post($element) {
        $post_params = [
            'tags' => [],
            'images' => [],
            'links' => [],
            'coordinates' => []
        ];

        foreach($element->children() as $child) {
            if($child->getName() == 'tags' || $child->getName() == 'links' || $child->getName() == 'images') {
                $post_params[$child->getName()] = $this->parse_array($child);
            }
            else if($child->getName() == 'coordinates') {
                $post_params['coordinates'] = $this->parse_coordinates($child);
            }
            else {
                $post_params[$child->getName()] = (string)$child;
            }
        }

        $post = new Post();
        $post->set_values($post_params);
        return $post;
    }

    private function parse_array($element) {
        $tags = []; // can be tags or links or images
        foreach($element->children() as $tag) {
            $tags[] = (string)$tag;
        }
        return $tags;
    }

    private function parse_coordinates($element) {
        if($element->count() > 0) {
            return [
                'latitude' => (float)$element->latitude,
                'longitude' => (float)$element->longitude
            ];
        }
        return [];
    }

    private function handle_failure($failure) {
        $ex = new \Twingly\Exception();
        $code = $failure->attributes()->code;
        $message = (string) $failure->message;
        $ex->from_api_response($code, $message);
    }

    private function handle_non_xml_document($document){
        $response_text = (string)$document->xpath('//text()')[0];
        throw new \Twingly\ServerException($response_text);
    }

    /**
     * Helper function to parse string to boolean
     * @param $value String to parse
     * @return bool|null
     */
    private function get_bool($value) {
        switch( strtolower($value) ) {
            case 'true':
                return true;
            case 'false':
                return false;
            default:
                return NULL;
        }
    }
}
