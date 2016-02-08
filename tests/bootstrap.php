<?php

// Enable Composer autoloader
/** @var \Composer\Autoload\ClassLoader $autoloader */
$autoloader = require dirname(__DIR__) . '/vendor/autoload.php';

// Register test classes
$autoloader->addPsr4('Twingly\Tests\\', __DIR__);

\VCR\VCR::turnOn();
\VCR\VCR::turnOff();