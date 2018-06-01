<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Responses\BasicResponse;
use CrowdStar\SVNAgent\Responses\BulkResponse;

/**
 * Class BulkReview
 * Review all local changes under given directories to SVN.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class BulkReview extends AbstractBulkAction implements BulkActionInterface
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        /** @var BulkResponse $response */
        $response = $this->getResponse();
        $request  = clone $this->getRequest();
        $request->setAction(ActionFactory::SVN_REVIEW);

        foreach ($this->getPaths() as $path) {
            $request->setData(['path' => $path]);
            $response->addResponse(
                (new Review($request, new BasicResponse($this->getLogger()), $this->getLogger()))->run()
            );
        }

        return $this;
    }
}
