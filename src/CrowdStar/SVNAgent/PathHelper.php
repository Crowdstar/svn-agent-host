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

namespace CrowdStar\SVNAgent;

use CrowdStar\SVNAgent\Exceptions\ClientException;

/**
 * Class PathHelper
 *
 * @package CrowdStar\SVNAgent
 */
class PathHelper
{
    /**
     * @param string $path
     * @return string
     * @throws ClientException
     */
    public static function normalizePath(string $path): string
    {
        $path = self::trim($path);
        if (empty($path)) {
            throw new ClientException('given path is empty');
        }

        // SVN URL like https://svn.apache.org/repos/asf (without trailing slash) returns HTTP 301 response back.
        // Here we make sure there are always slashes before and after given SVN path.
        return DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $path
     * @return string
     */
    public static function trim(string $path): string
    {
        return trim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $path
     * @return string
     */
    public static function ltrim(string $path): string
    {
        return ltrim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $path
     * @return string
     */
    public static function rtrim(string $path): string
    {
        return rtrim($path, DIRECTORY_SEPARATOR);
    }
}
