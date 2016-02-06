<?php

namespace Twingly;

class Result {
    public $number_of_matches_returned = 0;
    public $seconds_elapsed = 0.0;
    public $number_of_matches_total = 0;

    public $posts = [];

    public function all_results_returned() {
        return $this->number_of_matches_returned == $this->number_of_matches_total;
    }

    public function __toString()
    {
        $matches = sprintf("posts, number_of_matches_returned=%d, number_of_matches_total=%d",
            $this->number_of_matches_returned, $this->number_of_matches_total);

        return sprintf("#<%s:0x%s %s>", get_class($this), spl_object_hash($this), $matches);
    }
}