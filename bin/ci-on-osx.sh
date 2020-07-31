#!/usr/bin/env bash
#
# The continuous integration script to run on macOS.
#
# After upgrading to Catalina 10.15, you could experience following issue when running the unit tests:
#     "svn: error: The subversion command line tools are no longer provided by Xcode"
# It's because SVN is deprecated in Xcode 11, as mentioned here:
#     https://developer.apple.com/documentation/macos-release-notes/macos-catalina-10_15-release-notes
# There are different solutions for that, like installing SVN with brew (brew install svn). For details, please check
#     https://stackoverflow.com/a/60903732/2752269
#

set -e

# Switch to directory where this shell script sits.
pushd `dirname $0` > /dev/null
CURRENT_SCRIPT_PATH=`pwd -P`
# Switch back to current directory.
popd > /dev/null

cd "${CURRENT_SCRIPT_PATH}/.."

docker run --rm -d --name svn-server -p 80:80 krisdavison/svn-server:v3.0
./bin/init-svn-server.sh
./vendor/bin/phpunit
docker stop svn-server
