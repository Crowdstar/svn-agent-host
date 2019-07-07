version: '3'

# Used to run different CI tests.
# Please check script ./bin/ci.sh to see how it is used.
services:
  svn-agent-host:
    image: deminy/php-svn:php-%%PHP_VERSION%%-svn-%%SVN_VERSION%%
    command: tail -f /dev/null
    links:
      - svn-server
    volumes:
      - .:/docker-php-svn
  svn-server:
    image: krisdavison/svn-server:v3.0
    ports:
      - 80
