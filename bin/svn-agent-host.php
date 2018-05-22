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

$loaded = false;
$dirs   = [
    __DIR__,
    dirname(__DIR__, 1),
    dirname(__DIR__, 4),
];
foreach ($dirs as $dir) {
    $file = $dir . '/vendor/autoload.php';
    if (file_exists($file)) {
        require_once $file;
        $loaded = true;
        break;
    }
}
if (!$loaded) {
    echo "Error: Composer autoloading file not found.\n";
    exit(1);
}

use CrowdStar\SVNAgent\Actions\ActionFactory;
use CrowdStar\SVNAgent\Config;
use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Request;
use CrowdStar\SVNAgent\Response;

$config = Config::singleton()->init($dir);

$config->getLogger()->info('Native messaging host SVN Agent started');

try {
    $action = ActionFactory::fromRequest(Request::readRequest());
} catch (ClientException $e) {
    $response = (new Response())->setError($e->getMessage());
}
if (!empty($action)) {
    $response = $action->run();
}
$response->sendResponse();

$config->getLogger()->info('Native messaging host SVN Agent stopped');
