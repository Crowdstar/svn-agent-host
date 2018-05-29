<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\SVNHelper;
use MrRio\ShellWrap;

/**
 * Class Create
 * If given directory not exists, create it in SVN.
 *
 * @package CrowdStar\SVNAgent\Actions
 * @todo Update response message/error after SVN patch checked out locally.
 */
class Create extends AbstractAction
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $url = $this->getSvnUri();
        if (!SVNHelper::urlExists($url, $this->getRequest())) {
            $this->setMessage('SVN mkdir')->exec(
                function () use ($url) {
                    ShellWrap::svn(
                        'mkdir',
                        $url,
                        '--parents',
                        [
                            'username' => $this->getRequest()->getUsername(),
                            'password' => $this->getRequest()->getPassword(),
                            'm'        => 'path added through SVN Agent',
                        ]
                    );
                }
            );
        }

        if (!$this->hasError()) {
            $this->setResponseMessage("SVN path '{$url}' created. You can now check it out locally.");
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function getPostActions(): array
    {
        return [
            new Update($this->getRequest(), $this->getResponse(), $this->getLogger()),
        ];
    }
}
