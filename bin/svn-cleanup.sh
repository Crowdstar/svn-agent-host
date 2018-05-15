#!/usr/bin/env bash

# NOTE: must have flag -x set here in order to have exit code captured properly by package mrrio/shellwrap when error happens.
set -ex

# pwd
svn cleanup
svn st | grep '^?' | awk '{print $2}' | xargs rm -rf
svn st | grep '^!' | awk '{print $2}' | xargs svn revert -R
svn st | grep '^M' | awk '{print $2}' | xargs svn revert -R
