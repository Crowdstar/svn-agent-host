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

namespace CrowdStar\SVNAgent\Traits;

use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\PathHelper;

/**
 * Trait PathTrait
 *
 * @package CrowdStar\SVNAgent\Traits
 */
trait PathTrait
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return $this
     * @throws ClientException
     */
    public function setPath(string $path)
    {
        $this->path = PathHelper::normalizePath($path);

        return $this;
    }
}
