#!/usr/bin/env php
<?php
/**
 * This native messaging host borrows ideas from following two URLs:
 * @see https://github.com/landor/matchcommand chrome extension to run system commands from text matched in sites
 *      The native messaging host in this GitHub repository doesn't work well, but it gives us basic ideas on how to
 *      write Chrome native messaging host in PHP.
 *      Issue found: code execution is blocked when reading data from stdin; it won't execute until the Chrome extension
 *      is unloaded.
 * @see https://stackoverflow.com/a/48443161/2752269 Chrome Native messaging with PHP
 *      The code piece included fixes issue in above GitHub repository.
 */

require_once __DIR__ . '/vendor/autoload.php';

use CrowdStar\SVNAgent\Action;
use CrowdStar\SVNAgent\Config;
use CrowdStar\SVNAgent\Request;

$logger = Config::singleton()->getLogger();

$logger->info('Native messaging host SVN Agent started');

$request  = new Request();
$response = Action::fromRequest($request->readRequest())->process()->getResponse();
$response->sendResponse();

$logger->info('Native messaging host SVN Agent stopped');
