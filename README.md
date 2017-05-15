# Twingly Search API PHP

[![Build Status](https://travis-ci.org/twingly/twingly-search-api-php.png?branch=master)](https://travis-ci.org/twingly/twingly-search-api-php)

A PHP library for Twingly's Search API (previously known as Analytics API). Twingly is a blog search service that provides a searchable API known as [Twingly Search API](https://developer.twingly.com/resources/search/).

## Installation

Install via Composer

```shell
php composer.phar require twingly/twingly-search

# Or if you have installed composer globally
composer require twingly/twingly-search
```

## Usage

```php
use Twingly\Client;

$client = new Client();

$query = $client->query();
$query->search_query = 'github page-size:10 lang:sv';
$result = $query->execute();

foreach($result->posts as $post) {
    echo $post->url . "\n";
}
```

The `twingly-search` library talks to a commercial blog search API and requires an API key. Best practice is to set the `TWINGLY_SEARCH_KEY` environment variable to the obtained key. `\Twingly\Client` can be passed a key at initialization if your setup does not allow environment variables.

This library is documented with [phpdoc](http://www.phpdoc.org/). To generate documentation call

```shell
phpdoc -d ./src -t ./docs
```

Example code can be found in [examples/](examples/).

To learn more about the capabilities of the API, please read the [Twingly Search API documentation](https://developer.twingly.com/resources/search/).

## Requirements

* API key, [sign up](https://www.twingly.com/try-for-free) via [twingly.com](https://www.twingly.com/) to get one
* PHP 5.6, 7.0

## Development

Install PHP and [Composer], on OS X:

    brew tap homebrew/php
    brew install php70    # or another supported version
    brew install composer

Install project dependencies:

    composer install

Run tests:

    ./vendor/bin/phpunit

Run examples:

    TWINGLY_SEARCH_KEY=<KEY> php examples/hello_world.php

### Release

`twingly-search` is released on [Packagist].

1. Bump the version in [Client.php](src/Client.php), follow [Semantic Versioning 2.0.0](http://semver.org/).
1. Create a tag with the same version and push it to GitHub:

        git tag <VERSION> && git push --follow-tags

1. You're done! (*This repo has a GitHub service hook that notifies [Packagist] when a new version is created.*)

[Composer]: https://getcomposer.org/
[Packagist]: https://packagist.org

## License

The MIT License (MIT)

Copyright (c) 2016 Twingly AB

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
