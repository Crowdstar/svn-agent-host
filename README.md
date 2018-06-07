[![Build Status](https://travis-ci.org/Crowdstar/svn-agent-host.svg?branch=master)](https://travis-ci.org/Crowdstar/svn-agent-host)
[![Latest Stable Version](https://poser.pugx.org/Crowdstar/svn-agent-host/v/stable.svg)](https://packagist.org/packages/crowdstar/svn-agent-host)
[![Latest Unstable Version](https://poser.pugx.org/Crowdstar/svn-agent-host/v/unstable.svg)](https://packagist.org/packages/crowdstar/svn-agent-host)
[![License](https://poser.pugx.org/Crowdstar/svn-agent-host/license.svg)](https://packagist.org/packages/crowdstar/svn-agent-host)

A native messaging host to handle SVN commands received from specific Chrome extension.

# Run Unit Tests

First, please have a local SVN server setup:

```bash
docker run --rm -d --name svn-server -p 80:80 elleflorio/svn-server
docker exec -t svn-server htpasswd -b /etc/subversion/passwd username password
docker exec -t svn-server svnadmin create /home/svn/project1
docker exec -t svn-server chmod -R 777 /home/svn/project1
```

An SVN repository now is available at _http://127.0.0.1/svn/project1_ (not _http://localhost/svn/project1_), and you can
access it with following credentials:

```text
username: username
password: password
```

Now please run Composer to install PHPUnit, and then run unit tests:

```bash
composer install -n
./vendor/bin/phpunit
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
