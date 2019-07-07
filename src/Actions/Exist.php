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

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Traits\SimpleResponseTrait;
use CrowdStar\SVNAgent\SVNHelper;

/**
 * Class Exist
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Exist extends AbstractPathBasedAction
{
    use SimpleResponseTrait;

    const TYPE_FOLDER = 'folder';
    const TYPE_URL    = 'url';

    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $type = $this->getRequest()->get('type');
        switch ($type) {
            case self::TYPE_FOLDER:
                $dir = $this->getSvnDir();
                if (is_dir($dir)) {
                    $this->prepareResponse("Folder {$dir} exists");
                } else {
                    $this->setError("Folder {$dir} not exists");
                }
                break;
            case self::TYPE_URL:
                $url = $this->getSvnUri();
                if (SVNHelper::urlExists($url, $this->getRequest())) {
                    $this->prepareResponse("URL {$url} exists");
                } else {
                    $this->setError("URL {$url} not exists");
                }
                break;
            default:
                $this->setError("invalid type '{$type}'");
                break;
        }

        return $this;
    }
}
