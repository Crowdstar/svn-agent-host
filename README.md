[![Build Status](https://travis-ci.org/Crowdstar/svn-agent-host.svg?branch=master)](https://travis-ci.org/Crowdstar/svn-agent-host)
[![AppVeyor Build Status](https://ci.appveyor.com/api/projects/status/gd4g9vksc8m7e4ep?svg=true)](https://ci.appveyor.com/project/deminy/svn-agent-host)
[![Latest Stable Version](https://poser.pugx.org/Crowdstar/svn-agent-host/v/stable.svg)](https://packagist.org/packages/crowdstar/svn-agent-host)
[![Latest Unstable Version](https://poser.pugx.org/Crowdstar/svn-agent-host/v/unstable.svg)](https://packagist.org/packages/crowdstar/svn-agent-host)
[![License](https://poser.pugx.org/Crowdstar/svn-agent-host/license)](https://packagist.org/packages/crowdstar/svn-agent-host)

A native messaging host to handle SVN commands received from specific Chrome extension.

This repository was for an internal project at [Glu Mobile](https://www.glu.com). We make part of the whole project open
source so that developers could use it as a demo to learn how to use PHP to

* write [native messaging host](https://developer.chrome.com/apps/nativeMessaging#native-messaging-host) for Chrome.
* wrap Subversion operations (without using [the Subversion extension](http://php.net/manual/en/book.svn.php) in PHP).

# Run Tests

We use _Docker_ to prepare our test environments. You may run unit tests, coding style checks, and other tests in Docker
containers with following commands:

```bash
PHP_VERSION=7.0 SVN_VERSION=1.8.19 ./bin/ci.sh
PHP_VERSION=7.1 SVN_VERSION=1.9.9  ./bin/ci.sh
PHP_VERSION=7.2 SVN_VERSION=1.10.3 ./bin/ci.sh
```

# Demo Code

Following demo code shows how to communicate with the native message host from a Chrome extension.

```javascript
// content.js: a Content Script file.
window.addEventListener(
    "message",
    function (event) {
        chrome.runtime.sendMessage(
            event.data,
            function (response) {
                console.log('response from the background script', response);
            }
        );
    },
    false
);
window.postMessage({action: "create", data: {"path": "path/1"}}, "*");

// background.js: a Background Script file.
chrome.runtime.onMessage.addListener(
    function (request, sender, sendResponse) {
        chrome.runtime.sendNativeMessage(
            'com.glu.crowdstar.svnagent', // name of the native messaging host.
            request,
            function (response) {
                console.log("response from the native messaging host: ", response);
                // sendResponse(response);
            }
        );

        return true;
    }
);
```
