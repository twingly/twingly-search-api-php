<?php

namespace Twingly;

/**
 * A blog post
 *
 * @package Twingly
 */
class Post {

    /**
     * @var int the post ID
     */
    public $id = '';
    /**
     * @var string the post author
     */
    public $author = '';
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
    public $text = '';
    /**
     * @var string the blog location
     */
    public $location_code = '';
    /**
     * @var string ISO two letter language code for the language that the post was written in
     */
    public $language_code = '';
    /**
     * @var array geographical coordinates from blog post
     * @deprecated Use latitude and longitude instead
     */
    public $coordinates = [];
    /**
     * @var float|null geographical latitude coordinate from blog post
     */
    public $latitude = null;
    /**
     * @var float|null geographical longitude coordinate from blog post
     */
    public $longitude = null;
    /**
     * @var float all links from the blog post to other resources
     */
    public $links = [];
    /**
     * @var array tags
     */
    public $tags = [];
    /**
     * @var array image URLs from the posts
     */
    public $images = [];
    /**
     * @var \DateTime the time, in UTC, when the post was indexed by Twingly
     */
    public $indexed_at;
    /**
     * @var \DateTime the time, in UTC, when the post was published
     */
    public $published_at;
    /**
     * @var \DateTime the time, in UTC, when the post was last changed in the Twingly database
     */
    public $reindexedAt;
    /**
     * @var int number of links found in other blog posts
     */
    public $inlinks_count = 0;
    /**
     * @var string the blogid
     */
    public $blog_id = '';
    /**
     * @var string the blog URL
     */
    public $blog_url = '';
    /**
     * @var string name of the blog
     */
    public $blog_name = '';
    /**
     * @var int the blog's authority/influence
     *          (https://app.twingly.com/blog_search?tab=documentation)
     */
    public $authority = 0;
    /**
     * @var int the rank of the blog, based on authority and language
     *          (https://app.twingly.com/blog_search?tab=documentation)
     */
    public $blog_rank = 0;

    /**
     * Sets all instance variables for the Post, given a array
     *
     * @param array $params containing blog post data
     */
    public function set_values($params) {
        $this->id = (string)$params['id'];
        $this->author = (string)$params['author'];
        $this->url = (string)$params['url'];
        $this->title = (string)$params['title'];
        $this->text = (string)$params['text'];
        $this->language_code = (string)$params['languageCode'];
        $this->location_code = (string)$params['locationCode'];
        $this->coordinates = $params['coordinates'];
        $this->latitude = $this->get_with_default($this->coordinates['latitude'], null);
        $this->longitude = $this->get_with_default($this->coordinates['longitude'], null);
        $this->links = $params['links'];
        $this->tags = $params['tags'];
        $this->images = isset($params['images']) ? ($params['images']) : [];
        $this->published_at = \DateTime::createFromFormat(\DateTime::ISO8601, $params['publishedAt'], new \DateTimeZone('UTC'));
        $this->indexed_at = \DateTime::createFromFormat(\DateTime::ISO8601, $params['indexedAt'], new \DateTimeZone('UTC'));
        $this->reindexed_at = \DateTime::createFromFormat(\DateTime::ISO8601, $params['reindexedAt'], new \DateTimeZone('UTC'));
        $this->inlinks_count = (int)$params['inlinksCount'];
        $this->blog_url = (string)$params['blogUrl'];
        $this->blog_name = (string)$params['blogName'];
        $this->blog_id = (string)$params['blogId'];
        $this->authority = (int)$params['authority'];
        $this->blog_rank = (int)$params['blogRank'];
    }

    private function get_with_default(&$var, $default) {
        return isset($var) ? $var : $default;
    }
}
