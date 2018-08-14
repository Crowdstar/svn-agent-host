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

/**
 * Class Error
 *
 * @package CrowdStar\SVNAgent
 */
class Error
{
    const LOCK_FAILED     = 'e2801';
    const SERVER_OUTDATED = 'e2811';
    const CLIENT_OUTDATED = 'e2812';

    const ERRORS = [
        self::LOCK_FAILED     => 'failed to gain lock',
        self::SERVER_OUTDATED => 'messaging host upgrade required',
        self::CLIENT_OUTDATED => 'Chrome extension upgrade required',
    ];
}
