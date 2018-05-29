#!/usr/bin/env bash
#
# Update SVN option to save credentials automatically.
#

set -ex

if [ -f ~/.subversion/config ] ; then
    # By default SVN saves credentials, so just comment out option "store-passwords" to save credentials automatically
    sed -i '' 's/^[# 	]*store\-passwords[ 	]*=/# store-passwords =/g' ~/.subversion/config
fi
