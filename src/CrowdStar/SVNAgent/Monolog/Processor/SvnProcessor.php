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

namespace CrowdStar\SVNAgent\Monolog\Processor;

use CrowdStar\SVNAgent\Config;
use CrowdStar\SVNAgent\SVNHelper;
use Monolog\Logger;

/**
 * Class SvnProcessor
 *
 * @package CrowdStar\SVNAgent\Monolog\Processor
 */
class SvnProcessor
{
    /**
     * @var int
     */
    private $level;

    /**
     * @var string
     */
    private static $cache;

    /**
     * GitProcessor constructor.
     *
     * @param int|string $level
     */
    public function __construct($level = Logger::WARNING)
    {
        $this->level = Logger::toMonologLevel($level);
    }

    /**
     * @param  array $record
     * @return array
     */
    public function __invoke(array $record): array
    {
        if ($record['level'] >= $this->level) {
            $record['extra']['os']  = PHP_OS;
            $record['extra']['php'] = PHP_VERSION;

            // Few actions need to run on Windows directly, like the Open action (to open a SVN working directory in
            // File Explorer on Windows). Actions running on Windows directly don't need to work on SVN repositories,
            // and we may not have Subversion installed on Windows for that.
            if (!Config::singleton()->onWindows()) {
                $record['extra']['svn'] = self::getSvnVersion();
            }
        }

        return $record;
    }

    /**
     * @return string
     */
    private static function getSvnVersion(): string
    {
        if (!isset(self::$cache)) {
            self::$cache = (new SVNHelper())->getSvnVersion();
        }

        return self::$cache;
    }
}
