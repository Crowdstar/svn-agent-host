<?php

namespace CrowdStar\SVNAgent\Actions;

use MrRio\ShellWrap;
use MrRio\ShellWrapException;

/**
 * Class Commit
 * Commit all local changes under given directory to SVN.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Commit extends AbstractAction
{
    /**
     * @inheritdoc
     * @todo use a single script file to run all commands.
     */
    public function processAction(): AbstractAction
    {
        $dir = $this->getSvnDir();
        if (is_readable($dir)) {
            chdir($dir);

            // @see https://stackoverflow.com/a/11066348/2752269 svn delete removed files
            ShellWrap::svn(
                'status | grep \'^!\' | awk \'{print $2}\' | xargs svn delete'
            );
            try {
                // @see https://stackoverflow.com/a/4046862/2752269 How do I 'svn add' all unversioned files to SVN?
                ShellWrap::svn(
                    'add --force * --auto-props --parents --depth infinity -q'
                );
            } catch (ShellWrapException $e) {
                // This "svn add" command fails if nothing to add.
            }

            $this->setMessage('SVN commit')->exec(
                function () use ($dir) {
                    ShellWrap::svn(
                        'commit',
                        [
                            'username' => $this->getRequest()->getUsername(),
                            'password' => $this->getRequest()->getPassword(),
                            'm'        => 'changes committed through SVN Agent',
                        ]
                    );

                    // Delete the local folder after committed.
                    // TODO: backup it first before deleting it.
                    chdir('..');
                    ShellWrap::rm('-rf', $dir);
                }
            );

            //ShellWrap::svn(
            //    'up',
            //    [
            //        'username' => $this->getRequest()->getUsername(),
            //        'password' => $this->getRequest()->getPassword(),
            //    ]
            //);
        } else {
            $this->setError("Folder '{$dir}' not exist");
        }

        return $this;
    }
}
