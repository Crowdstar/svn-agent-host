FROM alpine:ALPINE_TAG

# An image to run different CI tests in it.
# Please check script ./bin/ci.sh to see how it is used.
# @see https://hub.docker.com/_/alpine/ Official Docker repository of Alpine
#
# Image alpine:3.5: PHP 7.0 and Subversion 1.9;
# Image alpine:3.6: PHP 7.1 and Subversion 1.9;
# Image alpine:3.7: PHP 7.1 and Subversion 1.9;
# Image alpine:3.8: PHP 7.2 and Subversion 1.10.
#
RUN \
  apk update            && \
  apk policy php7       && \
  apk policy subversion && \
  apk add --no-cache \
    curl             \
    openssl          \
    php7             \
    php7-dom         \
    php7-json        \
    php7-mbstring    \
    php7-openssl     \
    php7-phar        \
    php7-simplexml   \
    php7-tokenizer   \
    php7-xml         \
    subversion    && \
  curl                    \
    -sf                   \
    --connect-timeout 5   \
    --max-time         15 \
    --retry            5  \
    --retry-delay      2  \
    --retry-max-time   60 \
    http://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer && \
  composer --version
