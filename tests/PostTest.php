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


class PostTest extends \PHPUnit_Framework_TestCase {
    function testPost() {
        $data = file_get_contents(dirname(__DIR__) . '/tests/fixtures/valid_result.xml');
        $r = (new Parser())->parse($data);
        $p = $r->posts[0];
        $this->assertTrue(is_string($p->url));
        $this->assertTrue(is_string($p->title));
        $this->assertTrue(is_string($p->summary));
        $this->assertTrue(is_string($p->language_code));
        $this->assertTrue($p->published instanceof \DateTime);
        $this->assertTrue($p->indexed instanceof \DateTime);
        $this->assertTrue(is_string($p->blog_url));
        $this->assertTrue(is_string($p->blog_name));
        $this->assertTrue(is_int($p->authority));
        $this->assertTrue(is_int($p->blog_rank));
        $this->assertTrue(is_array($p->tags));
    }
}