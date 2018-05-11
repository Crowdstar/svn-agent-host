#!/usr/bin/env bash
# Kill running processes of this messaging host and delete lock file so that new actions from the extension can be
# fired.
#
# Usage:
#     ~bin/kill-host-processes.sh current-pid chrome-extension-id /path/to/lock/file
#     # current-pid is passed in to bypass current process from being killed.
#     # chrome-extension-id is passed in to filter out messaging host processes to be killed.
#     # /path/to/lock/file is passed in to get it deleted.
#
# @see https://github.com/Crowdstar/svn-agent-host/blob/master/src/CrowdStar/SVNAgent/Actions/Unlock.php
#

# NOTE: must have flag -x set here in order to have exit code captured properly by package mrrio/shellwrap when error happens.
set -ex


myPid=$1
extId=$2
lockFIle=$3

if [ -z $myPid ] ; then
    echo "Error: current process ID can not be empty."
    exit 1
fi
if [ ${#extId} -ne 32 ] ; then
    echo "Error: extension ID must be of 32-character long."
    exit 1
fi

# @see https://stackoverflow.com/a/11547409/2752269 How to get the PID of a process by giving the process name in Mac OS X ?
for pid in `ps -A | grep -m1 ${extId} | grep svn-agent-host.php | awk '{print $1}'` ; do
    # don't kill current process.
    if [ "${pid}" != "${myPid}" ] ; then
        kill -9 "${pid}"
    fi
done

if [ -f "${lockFIle}" ] ; then
    rm -f "${lockFIle}"
fi
