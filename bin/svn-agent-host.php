#!/usr/bin/env php
<?php
/**************************************************************************
 * Copyright 2018 Glu Mobile Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *************************************************************************/

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
    dirname(__DIR__, 2), // under folder vendor/bin/.
    dirname(__DIR__, 4), // under folder vendor/crowdstar/svn-agent-host/bin/.
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
use CrowdStar\SVNAgent\Responses\ErrorResponse;

$config = Config::singleton()->init($dir);

$config->getLogger()->info('Native messaging host SVN Agent started');

try {
    $action = ActionFactory::fromRequest(Request::readRequest());
} catch (ClientException $e) {
    $response = new ErrorResponse($e->getMessage());
}
if (!empty($action)) {
    $response = $action->run();
}
$response->sendResponse();

$config->getLogger()->info('Native messaging host SVN Agent stopped');
