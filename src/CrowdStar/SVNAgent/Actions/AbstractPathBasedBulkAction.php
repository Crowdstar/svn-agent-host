<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Responses\BulkResponse;

/**
 * Class AbstractPathBasedBulkAction
 *
 * @package CrowdStar\SVNAgent\Actions
 */
abstract class AbstractPathBasedBulkAction extends AbstractBulkAction
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $request = clone $this->getRequest();
        $request->setAction($this->getBasicAction());
        /** @var BulkResponse $bulkResponse */
        $bulkResponse = $this->getResponse();

        foreach ($this->getPaths() as $path) {
            $request->setData(['path' => $path]);
            $bulkResponse->addResponse(ActionFactory::fromRequest($request, $this->getLogger())->run());
        }

        return $this;
    }
}
