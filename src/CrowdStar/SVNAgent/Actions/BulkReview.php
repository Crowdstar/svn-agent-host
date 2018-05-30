<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\BulkResponse;
use CrowdStar\SVNAgent\Response;

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

        $skip = false;
        foreach ($this->getPaths() as $path) {
            if (!$skip) {
                $request->setData(['path' => $path]);
                $r = (new Review($request, new Response($this->getLogger()), $this->getLogger()))->run();
                $response->addResponse($r);
                if ($r->hasError()) {
                    $skip = true;
                }
            } else {
                $response->addResponse((new Response())->setMessage('skipped because error found'));
            }
        }

        return $this;
    }
}
