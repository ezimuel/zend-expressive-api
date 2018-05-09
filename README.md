# Zend Expressive API - Skeleton example

This is a skeleton application for building REST API using [zend-expressive](https://github.com/zendframework/zend-expressive).

The representational format used is [HAL-JSON](https://tools.ietf.org/html/draft-kelly-json-hal-08)
and the error reporting is performed using [Problem Details](https://tools.ietf.org/html/rfc7807).

Moreover, we used [OAuth2](https://oauth.net/2/) to authenticate the POST, PATCH
and DELETE HTTP methods.

In the skeleton we provided an example using `/api/users[/{id}]` route, you can
find more information in the [REST example](#REST-example) section.

## Setup

You need to use [composer](https://getcomposer.org/) to install the project.
You can run the following command:

```bash
$ composer install
```

## REST example

We provide a REST API using a User resource with a simple [SQLite](https://www.sqlite.org)
database with schema as follows:

```sql
CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(80),
  email VARCHAR(255) NOT NULL,
  password VARCHAR(60) NOT NULL
);
```

We published the following URLs:

- GET `/api/user[/{id:\d+}]`
- POST `/api/user` *
- PATCH `/api/user/{id:\d+}` *
- DELETE `/api/user/{id:\d+}` *

* = requires OAuth2 Authentication

In order to execute the REST API you need to use the `public/index.php` file.
Using the internal web server of PHP you can use the following command:

```bash
$ php -S 0.0.0.0:8080 -t public public/index.php
```

Here we reported some example of usage using [HTTPie](https://httpie.org/) client:

#### GET

Request:

```bash
$ http GET http://localhost:8080/api/user
```

Response:

```
HTTP/1.1 200 OK
Connection: close
Content-Type: application/hal+json
Date: Mon, 07 May 2018 14:54:46 +0200
Host: localhost:8080
X-Powered-By: PHP/7.2.4-1+ubuntu17.10.1+deb.sury.org+1

{
    "_embedded": {
        "users": [
            {
                "_links": {
                    "self": {
                        "href": "http://localhost:8080/api/users/1"
                    }
                },
                "email": "foo@host.com",
                "id": "1",
                "name": "Foo"
            }
        ]
    },
    "_links": {
            "self": {
                "href": "http://localhost:8080/api/users?page=1"
            }
        },
        "_page": 1,
        "_page_count": 1,
        "_total_items": 1
}
```

Note that the individual users **do not** include the password; we never want to
return passwords from our API!

#### POST

Request:

```bash
$ http POST http://localhost:8080/api/users name=Baz email=baz@host.com password=12345678
```

Response:

```
HTTP/1.1 201 Created
Connection: close
Content-Type: application/hal+json
Date: Mon, 07 May 2018 15:03:05 +0200
Host: localhost:8080
Location: /api/users/3
X-Powered-By: PHP/7.2.4-1+ubuntu17.10.1+deb.sury.org+1

{
    "_links": {
        "self": {
            "href": "http://localhost:8080/api/users/3"
        }
    },
    "email": "baz@host.com",
    "id": "3",
    "name": "Baz"
}

```

The user `Baz` has been created in the following location `/api/user/3`.

Passwords are stored internally using the [bcrypt](https://en.wikipedia.org/wiki/Bcrypt)
algorithm. You can examine them in the database to verify.

#### PATCH

Request:

```bash
$ http PATCH http://localhost:8080/api/users/3 name=Enrico
```

Response:

```
HTTP/1.1 200 OK
Connection: close
Content-Type: application/hal+json
Date: Mon, 07 May 2018 15:03:59 +0200
Host: localhost:8080
X-Powered-By: PHP/7.2.4-1+ubuntu17.10.1+deb.sury.org+1

{
    "_links": {
        "self": {
            "href": "http://localhost:8080/api/users/3"
        }
    },
    "email": "baz@host.com",
    "id": "3",
    "name": "Enrico"
}

```

#### DELETE

Request:

```bash
$ http DELETE http://localhost:8080/api/users/3
```

Response:

```
HTTP/1.1 204 No Content
Connection: close
Content-type: text/html; charset=UTF-8
Date: Mon, 07 May 2018 15:04:44 +0200
Host: localhost:8080
X-Powered-By: PHP/7.2.4-1+ubuntu17.10.1+deb.sury.org+1
```

## OAuth2

In order to get a Bearer token for OAuth2 you need to execute the following
command (using the default SQLite OAuth2 database example):

```bash
http POST http://localhost:8080/oauth grant_type=password username=user_test
     password=test client_id=client_test client_secret=test scope=test -f
```

This will produce an output as follows:

```
HTTP/1.1 200 OK
Cache-Control: no-store
Connection: close
Content-Type: application/json; charset=UTF-8
Date: Mon, 07 May 2018 17:49:39 +0200
Host: localhost:8080
Pragma: no-cache
X-Powered-By: PHP/7.2.4-1+ubuntu17.10.1+deb.sury.org+1

{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJS...Aw",
    "expires_in": 86400,
    "refresh_token": "def502009bbaf70068c8b4007c1b9645d173ce5183...ba3",
    "token_type": "Bearer"
}
```

In order to execute the POST, PATCH and DELETE methods you need to add the
`access_token` as `Authorization` header, as follows (with HTTPie command):

```bash
http POST http://localhost:8080/api/users 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJS...Aw'
```
