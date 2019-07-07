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

use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\PathHelper;
use CrowdStar\SVNAgent\Responses\RenameResponse;
use CrowdStar\SVNAgent\SVNHelper;
use MrRio\ShellWrap;

/**
 * Class Rename
 * Rename given directory both locally and in SVN.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Rename extends AbstractPathBasedAction
{
    /**
     * @var string
     */
    protected $toPath;

    /**
     * A dummy path-based action object with property "path" pointing to $this->toPath.
     * @var DummyPathBasedAction
     */
    protected $toAction;

    /**
     * @inheritdoc
     */
    protected function init(): AbstractAction
    {
        $this->setPath($this->getRequest()->get('path'));
        $this->setToPath($this->getRequest()->get('toPath'));

        $request = clone $this->getRequest();
        $request->setData(['path' => $this->getToPath()]);
        $this->setToAction(new DummyPathBasedAction($request, $this->getLogger()));

        return $this->validate()->initResponse();
    }

    /**
     * @inheritdoc
     */
    protected function validate(): AbstractAction
    {
        // Check if the source path and the destination path are the same.
        if ($this->getSvnDir() == $this->getToAction()->getSvnDir()) {
            throw new ClientException('source path and destination path are the same');
        }

        // Check the source path.
        if (!file_exists($this->getSvnDir())) {
            throw new ClientException("source path '{$this->getSvnDir()}' not exist");
        }

        // Check the destination path.
        if (!$this->getToAction()->getSvnDir()) {
            throw new ClientException('field "toPath" not passed in as should');
        }
        if (file_exists($this->getToAction()->getSvnDir())) {
            throw new ClientException("destination path '{$this->getToAction()->getSvnDir()}' already exists");
        }

        // Check the source URL.
        if (!SVNHelper::urlExists($this->getSvnUri(), $this->getRequest())) {
            throw new ClientException("source URL '{$this->getSvnUri()}' not exist");
        }

        // Check the destination URL.
        if (SVNHelper::urlExists($this->getToAction()->getSvnUri(), $this->getToAction()->getRequest())) {
            throw new ClientException("destination URL '{$this->getToAction()->getSvnUri()}' already exists");
        }

        return parent::validate();
    }

    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $fromDir = $this->getSvnDir();
        $fromUrl = $this->getSvnUri();
        $toDir   = $this->getToAction()->getSvnDir();
        $toUrl   = $this->getToAction()->getSvnUri();

        rename($fromDir, $toDir);
        $this->setMessage('SVN move')->exec(
            function () use ($fromUrl, $toUrl) {
                ShellWrap::svn(
                    'move',
                    $fromUrl,
                    $toUrl,
                    SVNHelper::getOptions($this->getRequest(), ['m' => 'rename path'])
                );
            }
        );
        $this->setMessage('SVN switch')->exec(
            function () use ($toUrl, $toDir) {
                ShellWrap::svn('switch', $toUrl, $toDir, SVNHelper::getOptions($this->getRequest()));
            }
        );

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function initResponse(): AbstractAction
    {
        return $this->setResponse(new RenameResponse($this->getPath()));
    }

    /**
     * @return string
     */
    public function getToPath(): string
    {
        return $this->toPath;
    }

    /**
     * @param string $path
     * @return Rename
     * @throws ClientException
     */
    public function setToPath(string $path): self
    {
        $this->toPath = PathHelper::normalizePath($path);

        return $this;
    }

    /**
     * @return DummyPathBasedAction
     */
    public function getToAction(): DummyPathBasedAction
    {
        return $this->toAction;
    }

    /**
     * @param DummyPathBasedAction $action
     * @return Rename
     */
    public function setToAction(DummyPathBasedAction $action): self
    {
        $this->toAction = $action;

        return $this;
    }
}
