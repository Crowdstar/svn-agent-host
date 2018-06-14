<?php

namespace CrowdStar\SVNAgent\Actions;

/**
 * Class BulkCommits
 * Commit all local changes under given directories to SVN.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class BulkCommits extends AbstractPathBasedBulkAction
{
    /**
     * @inheritdoc
     */
    protected $basicAction = ActionFactory::SVN_COMMIT;
}
