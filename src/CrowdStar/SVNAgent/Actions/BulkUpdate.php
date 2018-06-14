<?php

namespace CrowdStar\SVNAgent\Actions;

/**
 * Class BulkUpdate
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class BulkUpdate extends AbstractPathBasedBulkAction
{
    /**
     * @inheritdoc
     */
    protected $basicAction = ActionFactory::SVN_UPDATE;
}
