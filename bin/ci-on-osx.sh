#!/usr/bin/env bash
#
# The continuous integration script to run on macOS.
#

set -e

# Switch to directory where this shell script sits.
pushd `dirname $0` > /dev/null
CURRENT_SCRIPT_PATH=`pwd -P`
# Switch back to current directory.
popd > /dev/null

cd "${CURRENT_SCRIPT_PATH}/.."

docker run --rm -d --name svn-server -p 80:80 elleflorio/svn-server
./bin/init-svn-server.sh
./vendor/bin/phpunit
docker stop svn-server
