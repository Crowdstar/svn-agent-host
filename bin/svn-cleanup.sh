#!/usr/bin/env bash

set -e

# pwd
svn st | grep '^?' | awk '{print $2}' | xargs rm -rf
svn cleanup
