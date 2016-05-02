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


class ResultTest extends \PHPUnit_Framework_TestCase {
    function testResult() {
        $data = file_get_contents(dirname(__DIR__) . '/tests/fixtures/valid_result.xml');
        $r = (new Parser())->parse($data);
        $this->assertTrue(is_array($r->posts));
        $this->assertTrue(is_int($r->number_of_matches_total));
        $this->assertEquals($r->number_of_matches_total, 60768);
        $this->assertTrue(is_int($r->number_of_matches_returned));
        $this->assertEquals($r->number_of_matches_returned, 1000);
        $this->assertTrue(is_float($r->seconds_elapsed));
        $this->assertEquals($r->seconds_elapsed, 0.022);
        $this->assertFalse($r->all_results_returned());
    }
}
