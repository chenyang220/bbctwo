<?php
/**
 * Sets up MinApp controller and serves files
 *
 * DO NOT EDIT! Configure this utility via config.php and groupsConfig.php
 *
 * @package Minify
 */
ini_set('display_errors', 1);
error_reporting(E_ALL & ~(E_STRICT | E_NOTICE));

$app = (require __DIR__ . '/bootstrap.php');
/* @var \Minify\App $app */

$app->runServer();
