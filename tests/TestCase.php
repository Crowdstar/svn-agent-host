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

namespace CrowdStar\Tests\SVNAgent;

/**
 * Class TestCase
 *
 * @package CrowdStar\Tests\SVNAgent
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Update directory separators in given path to make it work under current OS.
     *
     * @param string $path
     * @return string
     */
    protected function updateDirectorySeparator(string $path): string
    {
        return str_replace('/', DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, $path));
    }
}
