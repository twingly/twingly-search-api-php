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


class PostTest extends \PHPUnit\Framework\TestCase {
    function testPost() {
        $data = file_get_contents(dirname(__DIR__) . '/tests/fixtures/minimal_valid_result.xml');
        $r = (new Parser())->parse($data);
        $p = $r->posts[0];
        $this->assertTrue(is_string($p->id));
        $this->assertTrue(is_string($p->author));
        $this->assertTrue(is_string($p->url));
        $this->assertTrue(is_string($p->title));
        $this->assertTrue(is_string($p->text));
        $this->assertTrue(is_string($p->language_code));
        $this->assertTrue(is_string($p->location_code));
        $this->assertTrue(is_array($p->links));
        $this->assertTrue(is_array($p->coordinates));
        $this->assertTrue(is_array($p->images));
        $this->assertTrue($p->published_at instanceof \DateTime);
        $this->assertTrue($p->indexed_at instanceof \DateTime);
        $this->assertTrue($p->reindexed_at instanceof \DateTime);
        $this->assertTrue(is_int($p->inlinks_count));
        $this->assertTrue(is_string($p->blog_id));
        $this->assertTrue(is_string($p->blog_url));
        $this->assertTrue(is_string($p->blog_name));
        $this->assertTrue(is_int($p->authority));
        $this->assertTrue(is_int($p->blog_rank));
        $this->assertTrue(is_array($p->tags));
    }
}
