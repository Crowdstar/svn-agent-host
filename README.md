[![Build Status](https://travis-ci.org/Crowdstar/svn-agent-host.svg?branch=master)](https://travis-ci.org/Crowdstar/svn-agent-host)

A native messaging host to handle SVN commands received from specific Chrome extension.

# Native Messaging Responses

There are two different type of responses:

## 1. Responses for Simple Actions

e.g, create, review, commit, etc.

Responses are return in following format:

```json
{
    "response": "response message here"
}
```

In case error happens, responses are returned in the format of:

```json
{
    "error": "error message here"
}
```

Error message could also be some pre-defined error code from class _\CrowdStar\SVNAgent\Error_. In this case, you will
need to customize your error message on client side:

```json
{
    "error": "e2801"
}
```


## 2. Responses for Bulk Actions

Bulk actions are a set of simple actions. Here are some bulk actions implemented already: bulk review, bulk commit.

Responses are return as an array of simple action responses. The array is in the same order as input data:

```json
{
    "response": [
        {
            "response": "response message here"
        },
        {
            "response": "response message here"
        },
        {
            "response": "response message here"
        }
    ]
}
```

In case error happens when processing a simple action, there are two fields returned in the response: field _error_
contains an error code "e2811", while field _response_ contains an array of simple action responses:

```json
{
    "error": "e2811",
    "response": [
        {
            "response": "response message here"
        },
        {
            "error": "error message here"
        },
        {
            "response": "skipped and not processed"
        }
    ]
}
```

For any other error happens, responses are returned in the format of:

```json
{
    "error": "error message here"
}
```
