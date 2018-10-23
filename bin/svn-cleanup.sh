#!/usr/bin/env bash

# NOTES:
#     1. must have flag -x set here in order to have exit code captured properly by package mrrio/shellwrap when error
#        happens.
#     2. redirect standard output to /dev/null.
#     3. it works both on Mac and on Ubuntu.
set -ex

# pwd
svn cleanup

case "$(uname -s)" in
    Linux*)
        svn st | grep '^?' | awk '{$1=""; print $0}' | xargs -I {} -r rm -rf "{}"
        svn st | grep '^!' | awk '{$1=""; print $0}' | xargs -I {} -r svn revert -R "{}" > /dev/null
        svn st | grep '^M' | awk '{$1=""; print $0}' | xargs -I {} -r svn revert -R "{}" > /dev/null
        ;;
    Darwin*) # on Mac
        svn st | grep '^?' | awk '{$1=""; print $0}' | xargs -I {} rm -rf "{}"
        svn st | grep '^!' | awk '{$1=""; print $0}' | xargs -I {} svn revert -R "{}" > /dev/null
        svn st | grep '^M' | awk '{$1=""; print $0}' | xargs -I {} svn revert -R "{}" > /dev/null
        ;;
    *)
        echo "Error: the SVN Agent host program 'svn-cleanup.sh' should be used under Ubuntu or Mac only."
        exit 1
        ;;
esac

