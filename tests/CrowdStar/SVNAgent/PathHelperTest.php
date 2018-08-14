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

use CrowdStar\SVNAgent\PathHelper;

/**
 * Class PathHelperTest
 *
 * @package CrowdStar\Tests\SVNAgent
 */
class PathHelperTest extends TestCase
{
    /**
     * @return array
     */
    public function dataTrim(): array
    {
        return [
            [
                'a',
                'a',
                'single-level path without slashes around it',
            ],
            [
                'a',
                '/a',
                'single-level path with leading slash only',
            ],
            [
                'a',
                'a/',
                'single-level path with trailing slash only',
            ],
            [
                'a',
                '/a/',
                'single-level path with slashes around it',
            ],
            [
                'a',
                '///a///',
                'single-level path with extra slashes around it',
            ],
            [
                'a/b/c',
                'a/b/c',
                'multiple-level path without slashes around it',
            ],
            [
                'a/b/c',
                '/a/b/c',
                'multiple-level path with leading slash only',
            ],
            [
                'a/b/c',
                'a/b/c/',
                'multiple-level path with trailing slash only',
            ],
            [
                'a/b/c',
                '/a/b/c/',
                'multiple-level path with slashes around it',
            ],
            [
                'a/b/c',
                '///a/b/c///',
                'multiple-level path with extra slashes around it',
            ],
        ];
    }

    /**
     * @dataProvider dataTrim
     * @covers PathHelper::trim()
     * @param string $expected
     * @param string $path
     * @param string $message
     */
    public function testTrim(string $expected, string $path, string $message)
    {
        $this->assertSame($expected, PathHelper::trim($path), $message);
    }

    /**
     * @return array
     */
    public function dataLtrim(): array
    {
        return [
            [
                'a',
                'a',
                'single-level path without slashes around it',
            ],
            [
                'a',
                '/a',
                'single-level path with leading slash only',
            ],
            [
                'a/',
                'a/',
                'single-level path with trailing slash only',
            ],
            [
                'a/',
                '/a/',
                'single-level path with slashes around it',
            ],
            [
                'a///',
                '///a///',
                'single-level path with extra slashes around it',
            ],
            [
                'a/b/c',
                'a/b/c',
                'multiple-level path without slashes around it',
            ],
            [
                'a/b/c',
                '/a/b/c',
                'multiple-level path with leading slash only',
            ],
            [
                'a/b/c/',
                'a/b/c/',
                'multiple-level path with trailing slash only',
            ],
            [
                'a/b/c/',
                '/a/b/c/',
                'multiple-level path with slashes around it',
            ],
            [
                'a/b/c///',
                '///a/b/c///',
                'multiple-level path with extra slashes around it',
            ],
        ];
    }

    /**
     * @dataProvider dataLtrim
     * @covers PathHelper::ltrim()
     * @param string $expected
     * @param string $path
     * @param string $message
     */
    public function testLtrim(string $expected, string $path, string $message)
    {
        $this->assertSame($expected, PathHelper::ltrim($path), $message);
    }
    /**
     * @return array
     */
    public function dataRtrim(): array
    {
        return [
            [
                'a',
                'a',
                'single-level path without slashes around it',
            ],
            [
                '/a',
                '/a',
                'single-level path with leading slash only',
            ],
            [
                'a',
                'a/',
                'single-level path with trailing slash only',
            ],
            [
                '/a',
                '/a/',
                'single-level path with slashes around it',
            ],
            [
                '///a',
                '///a///',
                'single-level path with extra slashes around it',
            ],
            [
                'a/b/c',
                'a/b/c',
                'multiple-level path without slashes around it',
            ],
            [
                '/a/b/c',
                '/a/b/c',
                'multiple-level path with leading slash only',
            ],
            [
                'a/b/c',
                'a/b/c/',
                'multiple-level path with trailing slash only',
            ],
            [
                '/a/b/c',
                '/a/b/c/',
                'multiple-level path with slashes around it',
            ],
            [
                '///a/b/c',
                '///a/b/c///',
                'multiple-level path with extra slashes around it',
            ],
        ];
    }

    /**
     * @dataProvider dataRtrim
     * @covers PathHelper::rtrim()
     * @param string $expected
     * @param string $path
     * @param string $message
     */
    public function testRtrim(string $expected, string $path, string $message)
    {
        $this->assertSame($expected, PathHelper::rtrim($path), $message);
    }
}
