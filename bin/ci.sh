#!/usr/bin/env bash
#
# The continuous integration script to run different tests.
# Usage:
#     ./bin/ci.sh [TAG_OF_AN_ALPINE_IMAGE]
#     ./bin/ci.sh 3.8
#     or
#     ALPINE_TAG=3.8 ./bin/ci.sh
# @see https://hub.docker.com/_/alpine/ Official Docker repository of Alpine
#

set -e

# Switch to directory where this shell script sits.
pushd `dirname $0` > /dev/null
CURRENT_SCRIPT_PATH=`pwd -P`
# Switch back to current directory.
popd > /dev/null

cd "${CURRENT_SCRIPT_PATH}/.."

if [ -z "${ALPINE_TAG}" ] ; then
    if [ -z "${1}" ] ; then
        echo "Error: Please specify a Docker tag of the Alpine image to be used."
        echo "       For example:"
        echo "           ./bin/ci.sh 3.8"
        echo "           or"
        echo "           ALPINE_TAG=3.8 ./bin/ci.sh"
        exit 1
    else
        ALPINE_TAG="${1}"
    fi
fi

# Create Docker files.
for file in "docker-compose.yml" "Dockerfile"; do
    sed "s/ALPINE_TAG/${ALPINE_TAG}/g" "${file}.tpl" > "${file}"
done

# Build the Docker image and launch Docker containers.
docker build --no-cache -t "crowdstar/svn-agent-host:${ALPINE_TAG}" .
docker-compose -p sah stop
docker-compose -p sah up -d --force-recreate
docker ps

function getContainerName()
{
    docker ps | grep ${1} | awk '{print $NF}'
}

# Initialize the SVN server container.
docker exec -t `getContainerName svn-server` htpasswd -b /etc/subversion/passwd username password
docker exec -t `getContainerName svn-server` svnadmin create /home/svn/project1
docker exec -t `getContainerName svn-server` chmod -R 777 /home/svn/project1

# Check PHP and Subversion versions.
docker exec -t `getContainerName svn-agent-host` sh -c "php --version && svn --version"

# Load third-party Composer packages.
docker exec -t `getContainerName svn-agent-host` sh -c "composer update -n"

# Run tests.
docker exec -t `getContainerName svn-agent-host` sh -c "./vendor/bin/phplint"
docker exec -t `getContainerName svn-agent-host` sh -c "./vendor/bin/phpcs -v --standard=PSR2 src tests"
docker exec -t `getContainerName svn-agent-host` sh -c "./vendor/bin/phpunit"

# Stop the Docker containers once tests are done.
docker-compose -p sah stop