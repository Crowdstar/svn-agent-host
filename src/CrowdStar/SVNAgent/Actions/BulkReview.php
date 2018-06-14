<?php

namespace CrowdStar\SVNAgent\Actions;

/**
 * Class BulkReview
 * Review all local changes under given directories to SVN.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class BulkReview extends AbstractPathBasedBulkAction
{
    /**
     * @inheritdoc
     */
    protected $basicAction = ActionFactory::SVN_REVIEW;
}
