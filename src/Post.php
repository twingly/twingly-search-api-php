<?php

namespace Twingly;

/**
 * A blog post
 *
 * @package Twingly
 */
class Post {
    /**
     * @var string the post URL
     */
    public $url = '';
    /**
     * @var string the post title
     */
    public $title = '';
    /**
     * @var string the blog post text
     */
    public $summary = '';
    /**
     * @var string ISO two letter language code for the language that the post was written in
     */
    public $language_code = '';
    /**
     * @var \DateTime published the time, in UTC, when this post was published
     */
    public $published;
    /**
     * @var \DateTime indexed the time, in UTC, when this post was indexed by Twingly
     */
    public $indexed;
    /**
     * @var string the blog URL
     */
    public $blog_url = '';
    /**
     * @var string name of the blog
     */
    public $blog_name = '';
    /**
     * @var int  authority the blog's authority/influence
     *           (https://developer.twingly.com/resources/search/#authority)
     */
    public $authority = 0;
    /**
     * @var int  the rank of the blog, based on authority and language
     *           (https://developer.twingly.com/resources/search/#authority)
     */
    public $blog_rank = 0;
    /**
     * @var array tags
     */
    public $tags = [];

    /**
     * Sets all instance variables for the Post, given a array
     *
     * @param array $params containing blog post data
     */
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