version: '3'

# Used to run different CI tests.
# Please check script ./bin/ci.sh to see how it is used.
services:
  svn-agent-host:
    image: crowdstar/svn-agent-host:ALPINE_TAG
    command: tail -f /dev/null
    links:
      - svn-server
    volumes:
      - .:/svn-agent-host
  svn-server:
    image: elleflorio/svn-server
    ports:
      - 80
