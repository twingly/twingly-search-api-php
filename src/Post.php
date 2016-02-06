<?php

namespace Twingly;

class Post {
    public $url = '';
    public $title = '';

    public $summary = '';
    public $language_code = '';
    public $published;
    public $indexed;
    public $blog_url = '';
    public $blog_name = '';
    public $authority = 0;
    public $blog_rank = 0;
    public $tags = [];

    public function set_values($params) {
        $this->url = (string)$params['url'];
        $this->title = (string)$params['title'];
        $this->summary = (string)$params['summary'];
        $this->language_code = (string)$params['languageCode'];
        $this->published = \DateTime::createFromFormat('Y-m-d H:i:sZ', $params['published']);
        $this->indexed = \DateTime::createFromFormat('Y-m-d H:i:sZ', $params['indexed']);
        $this->blog_url = (string)$params['blogUrl'];
        $this->blog_name = (string)$params['blogName'];
        $this->authority = (int)$params['authority'];
        $this->blog_rank = (int)$params['blogRank'];
        $this->tags = $params['tags'];
    }
}