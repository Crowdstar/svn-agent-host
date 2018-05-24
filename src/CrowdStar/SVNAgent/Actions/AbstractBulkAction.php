<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Exceptions\ClientException;

/**
 * Class AbstractBulkAction
 *
 * @package CrowdStar\SVNAgent\Actions
 */
abstract class AbstractBulkAction extends AbstractAction implements
    LocklessActionInterface,
    PathNotRequiredActionInterface
{
    /**
     * Maximum number of paths to handle.
     */
    const MAX_PATHS = 15;

    /**
     * @var array
     */
    protected $paths = [];

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
     * @inheritdoc
     */
    protected function init(): AbstractAction
    {
        $paths = $this->getRequest()->get('paths');

        return $this->setPaths($paths ? explode(PATH_SEPARATOR, $paths) : [])->validate();
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
