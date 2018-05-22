<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Exceptions\ClientException;

/**
 * Class BulkCommits
 * Commit all local changes under given directories to SVN.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class BulkCommits extends AbstractAction implements LocklessActionInterface, PathNotRequiredActionInterface
{
    /**
     * Maximum number of paths to commit.
     */
    const MAX_PATHS = 15;

    /**
     * @var array
     */
    protected $paths = [];

    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $request = clone $this->getRequest();
        $request->setAction(ActionFactory::SVN_COMMIT);

        $responseData = array_combine($this->getPaths(), 'uncommitted');
        foreach ($this->getPaths() as $path) {
            $request->setData(['path' => $path]);
            $response = (new Commit($request, $this->getLogger()))->run();
            if ($response->hasError()) {
                $responseData[$path] = $response->getError();
                break;
            } else {
                $responseData[$path] = 'committed';
            }
        }

        // This is just to combine response messages to a string in the format like:
        //     /svn/path/1: committed.
        //     /svn/path/2: committed.
        //     /svn/path/3: committed.
        // or
        //     /svn/path/1: committed.
        //     /svn/path/2: Folder '/svn/path/2' not exist.
        //     /svn/path/3: uncommitted.
        $this->setResponseMessage(
            array_reduce(
                array_keys($responseData),
                function ($carry, $path) use ($responseData) {
                    return ($carry ? "{$carry}\n" : "") . "{$path}: $responseData[$path].";
                },
                ''
            )
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * @param array $paths
     * @return BulkCommits
     * @throws ClientException
     */
    public function setPaths(array $paths): BulkCommits
    {
        if (count($paths) > self::MAX_PATHS) {
            throw new ClientException('up to ' . self::MAX_PATHS . ' paths can be committed together');
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
