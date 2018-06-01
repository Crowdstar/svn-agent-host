<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Traits\PathBasedResponseTrait;
use CrowdStar\SVNAgent\SVNHelper;
use MrRio\ShellWrap;

/**
 * Class Create
 * If given directory not exists, create it in SVN.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Create extends AbstractAction implements PathBasedActionInterface
{
    use PathBasedResponseTrait;

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
