<?php

// Set environment variables before Laravel loads
$_ENV['APP_ENV'] = 'testing';
$_SERVER['APP_ENV'] = 'testing';

// Load Composer autoloader
require __DIR__.'/../vendor/autoload.php';
