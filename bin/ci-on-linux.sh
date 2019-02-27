#!/usr/bin/env bash
#
# The continuous integration script to run different tests under Linux environments using Docker.
# Usage:
#     PHP_VERSION=7.2    SVN_VERSION=1.10.3 ./bin/ci-on-linux.sh
#     PHP_VERSION=7.1.19 SVN_VERSION=1.10.0 ./bin/ci-on-linux.sh
#

set -e

# Switch to directory where this shell script sits.
pushd `dirname $0` > /dev/null
CURRENT_SCRIPT_PATH=`pwd -P`
# Switch back to current directory.
popd > /dev/null

cd "${CURRENT_SCRIPT_PATH}/.."

if [ -z "${PHP_VERSION}" ] || [ -z "${SVN_VERSION}" ] ; then
    echo "Error: Please specify environment variable PHP_VERSION and SVN_VERSION."
    echo "       For example:"
    echo "           PHP_VERSION=7.2    SVN_VERSION=1.10.3 ./bin/ci.sh"
    echo "           PHP_VERSION=7.1.19 SVN_VERSION=1.10.0 ./bin/ci-on-linux.sh
    exit 1
fi

# Create Docker files.
sed "s/%%PHP_VERSION%%/${PHP_VERSION}/g; s/%%SVN_VERSION%%/${SVN_VERSION}/g" docker-compose.yml.tpl > docker-compose.yml

# Launch Docker containers.
docker-compose -p sah stop
docker-compose -p sah up -d --force-recreate
docker ps

. ./bin/init-svn-server.sh

# Check PHP and Subversion versions.
docker exec -t `docker ps | grep svn-agent-host | awk '{print $NF}'` sh -c "php --version && svn --version"

# Load third-party Composer packages.
docker exec -t `docker ps | grep svn-agent-host | awk '{print $NF}'` sh -c "composer update -n"

# Run tests.
docker exec -t `docker ps | grep agent-host | awk '{print $NF}'` sh -c "./vendor/bin/phplint"
docker exec -t `docker ps | grep agent-host | awk '{print $NF}'` sh -c "./vendor/bin/phpcs -v --standard=PSR2 src tests"
docker exec -t `docker ps | grep agent-host | awk '{print $NF}'` sh -c "./vendor/bin/phpunit"

# Stop the Docker containers once tests are done.
docker-compose -p sah stop
