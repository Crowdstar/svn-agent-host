#!/usr/bin/env bash
#
# Initialize the SVN server container so that:
#     1. The SVN repository is available at http://127.0.0.1/svn/project1 (not http://localhost/svn/project1).
#     1. The SVN repository can be accessed with following credential:
#        username: username
#        password: password
#

set -ex

docker exec -t `docker ps | grep svn-server | awk '{print $NF}'` htpasswd -b /etc/subversion/passwd username password
docker exec -t `docker ps | grep svn-server | awk '{print $NF}'` svnadmin create /home/svn/project1
docker exec -t `docker ps | grep svn-server | awk '{print $NF}'` chmod -R 777 /home/svn/project1
