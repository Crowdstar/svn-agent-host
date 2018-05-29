<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Response;

/**
 * Class BulkCommits
 * Commit all local changes under given directories to SVN.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class BulkCommits extends AbstractBulkAction
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $request = clone $this->getRequest();
        $request->setAction(ActionFactory::SVN_COMMIT);

        $responseData = array_fill_keys($this->getPaths(), 'uncommitted');
        foreach ($this->getPaths() as $path) {
            $request->setData(['path' => $path]);
            $response = (new Commit($request, new Response($this->getLogger()), $this->getLogger()))->run();
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
}
