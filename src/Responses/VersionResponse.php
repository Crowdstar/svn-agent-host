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

namespace CrowdStar\SVNAgent\Responses;

use CrowdStar\SVNAgent\Config;
use CrowdStar\SVNAgent\SVNHelper;

/**
 * Class VersionResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class VersionResponse extends AbstractResponse
{
    /**
     * @inheritdoc
     * @see AbstractResponse::process()
     */
    public function process(string $output)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        // There is no need to include Composer version in the response, because:
        // 1. If Composer doesn't work, the package won't even work.
        // 2. Composer should have been preinstalled by the installer, which could use a specific version of Composer;
        //    Composer is maintained and upgraded through the installer, not through this package.
        return [
            'SVN Agent Host' => Config::VERSION,
            'PHP'            => PHP_VERSION,
            'Subversion'     => (new SVNHelper())->getSvnVersion(),
        ];
    }
}
