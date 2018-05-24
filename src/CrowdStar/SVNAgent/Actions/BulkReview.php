<?php

namespace CrowdStar\SVNAgent\Actions;

/**
 * Class BulkReview
 * Review all local changes under given directories to SVN.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class BulkReview extends AbstractBulkAction
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $request = clone $this->getRequest();
        $request->setAction(ActionFactory::SVN_REVIEW);

        $responseData = [];
        foreach ($this->getPaths() as $path) {
            $request->setData(['path' => $path]);
            $response = (new Review($request, $this->getLogger()))->run();
            if ($response->hasError()) {
                $this->setError("Failed to review path {$path}:\n{$response->getError()}");
                break;
            } else {
                $responseData[$path] = $response->getResponse();
            }
        }

        if (!$this->hasError()) {
            $this->setResponseMessage(
                array_reduce(
                    array_keys($responseData),
                    function ($carry, $path) use ($responseData) {
                        return ($carry ? "{$carry}\n\n" : "") . "{$path}:\n{$responseData[$path]}";
                    },
                    ''
                )
            );
        }

        return $this;
    }
}
