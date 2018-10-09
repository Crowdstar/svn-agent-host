[![Build Status](https://travis-ci.org/Crowdstar/svn-agent-host.svg?branch=master)](https://travis-ci.org/Crowdstar/svn-agent-host)
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
# # Use a base image alpine:3.7, which has PHP 7.1 and Subversion 1.9 installed for testing purpose.
./bin/ci.sh 3.7
# or
# Use a base image alpine:3.8, which has PHP 7.2 and Subversion 1.10 installed for testing purpose.
./bin/ci.sh 3.8
```

# Native Messaging Responses

## 1. Bulk Review

### 1.1. Successful

```json
{
    "success": true,
    "response": [
        {
            "path": "/svn/path/1/",
            "success": true,
            "actions": [
                {
                    "type": "?",
                    "file": "file1.dat"
                },
                {
                    "type": "?",
                    "file": "file2.dat"
                }
            ]
        },
        {
            "path": "/svn/path/2/",
            "success": true,
            "actions": [
                {
                    "type": "!",
                    "file": "file1.dat"
                },
                {
                    "type": "!",
                    "file": "file2.dat"
                }
            ]
        }
    ]
}
```

### 1.2. Partially Failed

```json
{
    "success": true,
    "response": [
        {
            "path": "/svn/path/1/",
            "success": false,
            "message": "error message"
        },
        {
            "path": "/svn/path/2/",
            "success": true,
            "actions": [
                {
                    "type": "!",
                    "file": "file1.dat"
                },
                {
                    "type": "!",
                    "file": "file2.dat"
                }
            ]
        }
    ]
}
```

### 1.3. Failed

```json
{
    "success": false,
    "message": "error message"
}
```

## 2. Bulk Commit

### 2.1. Successful

```json
{
    "success": true,
    "response": [
        {
            "path": "/svn/path/1/",
            "success": true
        },
        {
            "path": "/svn/path/2/",
            "success": true
        }
    ]
}
```

### 2.2. Partially Failed

```json
{
    "success": true,
    "response": [
        {
            "path": "/svn/path/1/",
            "success": true
        },
        {
            "path": "/svn/path/2/",
            "success": false,
            "message": "error message"
        }
    ]
}
```

### 2.3. Failed

```json
{
    "success": false,
    "message": "error message"
}
```

## 3. Review

### 3.1. Successful

```json
{
    "success": true,
    "path": "/svn/path/",
    "actions": [
        {
            "type": "?",
            "file": "file1.dat"
        },
        {
            "type": "?",
            "file": "file2.dat"
        }
    ]
}
```

### 3.2. Failed

```json
{
    "success": false,
    "message": "error message"
}
```

## 4. Commit

### 4.1. Successful

```json
{
    "success": true,
    "path": "/svn/path/"
}
```

### 4.2. Failed

```json
{
    "success": false,
    "message": "error message"
}
```

## 5. Checkout

### 5.1. Successful

```json
{
    "success": true,
    "path": "/svn/path/",
    "revision": 48973,
    "actions": [
        {
            "type": "A",
            "file": "file1.dat"
        },
        {
            "type": "A",
            "file": "file2.dat"
        }
    ]
}
```

### 5.2. Failed

```json
{
    "success": false,
    "message": "error message"
}
```

## 6. Rename

### 6.1. Successful

```json
{
    "success": true,
    "revision": 48973
}
```

### 6.2. Failed

```json
{
    "success": false,
    "message": "error message"
}
```
