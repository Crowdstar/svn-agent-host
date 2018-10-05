#!/usr/bin/env bash
#
# A wrapper script used for the Windows version of the SVN Agent extension to communicate with this native messaging
# host, which is installed on Windows through WSL (the Windows Subsystem for Linux).
#

set -e

# Switch to directory where this shell script sits.
pushd "`dirname "$0"`" > /dev/null
BIN_DIR=`pwd -P`
# Switch back to current directory.
popd > /dev/null

"${BIN_DIR}/svn-agent-host.php" "$@"
