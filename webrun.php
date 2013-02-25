<?php

require_once __DIR__ . '/vendor/autoload.php';

$runner = new TestBase\Runner();
$runner->addDirectory(__DIR__ . '/tests');
$runner->run();
