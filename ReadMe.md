# Sloganator

[![Build Status](https://github.com/Gipetto/sloganator/actions/workflows/main.yml/badge.svg)](https://github.com/Gipetto/sloganator/actions/workflows/main.yml)
![Treefort54](https://img.shields.io/badge/Treefort-54-blue.svg)

Because, like, who doesn't like a good slogan?

This was designed to be bolted on to a MyBB forum, but could be adapted for anything that has a user system.


## Requirements

- PHP 8+
- SQLite3 PHP extension
- Web Server (Apache, Nginx)
- MyBB host forum (though the User object can be updated/extended to support any user system that is cookie based)


## Local Dev

Local development requires Docker. It does not require a local PHP, Apache, or SQLite3 install.

``` sh
$ make install
$ make dev-server
```

You'll find the sloganator UI at: `localhost:8080/mies/sloganator`
You'll find the forum stub at: `localhost:8080/mies`


## API

### Caching

The endpoints for `/v1/authors` and `/v1/slogans/latest` use simple file caches of serialized objects to avoid repetitive DB lookups. These caches are cleared upon creation of a new slogan. Caches are populated on these endpoint at call time. Each endpoint creates it own cache file.

### GET /v1/slogans

List Slogans

#### Parameters

| Parameter | Type   | Description |
| --------- | ------ | ----------- |
| page      | int32  | Controls which page of results is returned      |
| pageSize  | int32  | Controls the numbers of results per page        |
| author    | int32  | Filters the returned results by a single author |

#### 200 OK
``` json
{
    "slogans": [
        {
            "rowid": 1,
            "timestamp": 1625771223,
            "username": "Treefort Lover",
            "userid": 1,
            "slogan": "This is a slogan"
        },
        // ...
    ],
    "meta": {
        "page": 1,
        "pageSize": 100,
        "results": 1000,
        "previousPage": null,
        "nextPage": 2,
        "filter": []
    }
}
```

### POST /v1/slogans

Create a Slogan

### Authentication

Requires a valid login cookie to the host forum from which the `userid` and `username` are retrieved.

### Request Body

Content-Type: `application/json; charset=UTF8`  
Slogan: UTF-8 text, limit 150 chars

``` json
{
    "slogan": "Your great slogan!"
}
```

### 201 Created

``` json
{
    "rowid": 2,
    "timestamp": 1625771223,
    "username": "Treefort Lover",
    "userid": 1,
    "slogan": "Your great slogan!"
}
```

### Errors

- 400 Bad Request
    - Slogan length must be 150 chars or less
    - Slogan must not be empty
- 401 Unauthorized
    - Users must have a valid login cookie from the host forum
- 429 Too Many Requests
    - Users are limited to creating 1 slogan every 15 seconds

### GET /v1/slogans/latest

Get the latest Slogan

#### 200 OK

``` json
{
    "rowid": 1,
    "timestamp": 1625771223,
    "username": "Treefort Lover",
    "userid": 1,
    "slogan": "This is a slogan"
}
```

### GET /v1/authors

List slogan authors.  

__NOTE:__ Simple list for now. This will the rull response format treatment eventually...

#### Notes

Forum users like to fiddle with their usernames over time. The slogans are created with both the `userid` and `username` of the author at the time of creation so that we can keep track of unique authors, despite their username changes over time and without having to lookup anything in the host forum software.

#### 200 OK

``` json
[
    {
        "userid": 1,
        "usernames": [
            "Treefort Lover",
            "Party Leader"
        ]
    },
    ...
]
```


## User Interface

The Sloganator has 2 UI interfaces:

1. A simple list interface where users can peruse past slogans
2. A forum display widget that facilitates users adding new slogans

### List Interface

@TODO

### Forum Display Widget

@TODO