<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\BulkResponse;
use CrowdStar\SVNAgent\Response;

/**
 * Class BulkCommits
 * Commit all local changes under given directories to SVN.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class BulkCommits extends AbstractBulkAction implements BulkActionInterface
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        /** @var BulkResponse $response */
        $response = $this->getResponse();
        $request  = clone $this->getRequest();
        $request->setAction(ActionFactory::SVN_COMMIT);

        $skip = false;
        foreach ($this->getPaths() as $path) {
            if (!$skip) {
                $request->setData(['path' => $path]);
                $r = (new Commit($request, new Response($this->getLogger()), $this->getLogger()))->run();
                if ($r->hasError()) {
                    $response->addResponse($r);
                    $skip = true;
                } else {
                    $response->addResponse((new Response())->setMessage('committed'));
                }
            } else {
                $response->addResponse((new Response())->setMessage('uncommitted'));
            }
        }

        return $this;
    }
}
