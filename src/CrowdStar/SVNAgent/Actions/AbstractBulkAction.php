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
use CrowdStar\SVNAgent\Responses\BulkResponse;
use CrowdStar\SVNAgent\Exceptions\Exception;
use ReflectionClass;
use ReflectionException;

/**
 * Class AbstractBulkAction
 *
 * @package CrowdStar\SVNAgent\Actions
 */
abstract class AbstractBulkAction extends AbstractAction implements
    BulkActionInterface,
    LocklessActionInterface,
    PathNotRequiredActionInterface
{
    /**
     * Maximum number of paths to handle.
     */
    const MAX_PATHS = 40;

    /**
     * @var array
     */
    protected $paths = [];

    /**
     * @var string
     */
    protected $basicAction;

    /**
     * @return array
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * @param array $paths
     * @return $this
     * @throws ClientException
     */
    public function setPaths(array $paths): AbstractBulkAction
    {
        if (count($paths) > self::MAX_PATHS) {
            throw new ClientException('up to ' . self::MAX_PATHS . ' paths can be handled together');
        }

        $this->paths = $paths;

        return $this;
    }

    /**
     * @return string
     */
    protected function getBasicAction(): string
    {
        return $this->basicAction;
    }

    /**
     * @param string $basicAction
     * @return AbstractBulkAction
     * @throws Exception
     * @throws ReflectionException
     */
    protected function setBasicAction(string $basicAction): AbstractBulkAction
    {
        if (!array_key_exists($basicAction, ActionFactory::ACTION_CLASSES)) {
            throw new Exception("unsupported basic action '{$basicAction}'");
        }
        $className = ActionFactory::ACTION_CLASSES[$basicAction];
        $class     = new ReflectionClass($className);
        if ($class->implementsInterface(BulkActionInterface::class)) {
            throw new Exception("bulk action class '{$className}' cannot be used as basic action");
        }

        $this->basicAction = $basicAction;

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function initResponse(): AbstractAction
    {
        return $this->setResponse(new BulkResponse());
    }

    /**
     * @inheritdoc
     */
    protected function init(): AbstractAction
    {
        return $this->setPaths($this->getRequest()->get('paths'))->validate()->initResponse();
    }

    /**
     * @inheritdoc
     */
    protected function validate(): AbstractAction
    {
        if (!$this->getPaths()) {
            throw new ClientException('Paths not passed in');
        }

        return parent::validate();
    }
}
