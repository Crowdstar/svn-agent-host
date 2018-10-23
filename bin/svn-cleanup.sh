#!/usr/bin/env bash

# NOTES:
#     1. must have flag -x set here in order to have exit code captured properly by package mrrio/shellwrap when error
#        happens.
#     2. redirect standard output to /dev/null.
set -ex

# pwd
svn cleanup
svn st | grep '^?' | awk '{print $2}' | xargs -r rm -rf
svn st | grep '^!' | awk '{print $2}' | xargs -r svn revert -R > /dev/null
svn st | grep '^M' | awk '{print $2}' | xargs -r svn revert -R > /dev/null
