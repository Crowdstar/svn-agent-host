<?php

namespace CrowdStar\SVNAgent\Responses;

/**
 * Class Actions
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class Actions
{
    /**
     * @var Action[]
     */
    protected $actions;

    /**
     * Actions constructor.
     *
     * @param array $actions
     */
    public function __construct(array $actions = [])
    {
        $this->setActions($actions);
    }

    /**
     * @return Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param Action[] $actions
     * @return $this
     */
    public function setActions(array $actions): Actions
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @param Action $action
     * @return Action
     */
    public function addAction(Action $action): Actions
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(
            function (Action $action) {
                return $action->toArray();
            },
            $this->getActions()
        );
    }
}
