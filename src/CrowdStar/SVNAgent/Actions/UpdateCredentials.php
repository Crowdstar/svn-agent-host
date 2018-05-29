<?php

namespace CrowdStar\SVNAgent\Actions;

use MrRio\ShellWrap;

/**
 * Class UpdateCredentials
 *
 * @package CrowdStar\SVNAgent\Actions
 * @todo This doesn't work on Mac OS X since SVN credentials are stored in keychain and here we use credentials not
 *       from a terminal.
 */
class UpdateCredentials extends AbstractAction implements PathNotRequiredActionInterface
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $this->setMessage('SVN save credentials')->exec(
            function () {
                ShellWrap::bash($this->getConfig()->getRootDir() . '/vendor/bin/svn-save-credentials.sh');
                //ShellWrap::svn(
                //    'auth',
                //    '--remove',
                //    $this->getConfig()->getSvnRoot() //TODO: change this to base URL like http://example.com
                //);
                ShellWrap::svn(
                    'info',
                    $this->getConfig()->getSvnRoot(),
                    [
                        'username' => $this->getRequest()->getUsername(),
                        'password' => $this->getRequest()->getPassword(),
                    ]
                );
            }
        );

        return $this;
    }
}
