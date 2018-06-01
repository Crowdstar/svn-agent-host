<?php

namespace CrowdStar\SVNAgent\Traits;

use CrowdStar\SVNAgent\Responses\Action;
use CrowdStar\SVNAgent\Responses\Actions;

/**
 * Trait ResponseActionsTrait
 *
 * @package CrowdStar\SVNAgent\Traits
 */
trait ResponseActionsTrait
{
    /**
     * @var Actions
     */
    protected $actions;

    /**
     * @return Actions
     */
    public function getActions(): Actions
    {
        return $this->actions;
    }

    /**
     * @param Actions $actions
     * @return $this
     */
    public function setActions(Actions $actions)
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @param Action $action
     * @return $this
     */
    protected function addAction(Action $action)
    {
        $this->getActions()->addAction($action);

        return $this;
    }
}
