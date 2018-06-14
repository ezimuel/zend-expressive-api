# Zend Expressive API - Skeleton example

This is a (proposed) skeleton application for building REST APIs using [zend-expressive](https://github.com/zendframework/zend-expressive).

The representational format used is [HAL-JSON](https://tools.ietf.org/html/draft-kelly-json-hal-08),
and the error reporting format used is [Problem Details for HTTP APIs](https://tools.ietf.org/html/rfc7807).

Moreover, the skeleton uses [OAuth2](https://oauth.net/2/) for use with
authentication.

In the skeleton, we provide an example `/api/users[/{id}]` route; you can
find more information in the [REST example](#REST-example) section.

## Setup

You need to use [Composer](https://getcomposer.org/) to install the project.
You can run the following command:

```bash
$ composer install
```

Once installed, we also recommend that you initially use development mode, which
you can enable using:

```bash
$ composer development-enable
```

## Vagrant

You can execute [Vagrant](https://www.vagrantup.com/) to setup a Linux
environment to run the `zend-expressive-api` application.

This setup will install the following environment:

- Linux Ubuntu 18.04
- PHP 7.2.5
- nginx 1.5.1
- SQLite 3.22

In order to run Vagrant you need a VM hypervisor like [VirtualBox](https://www.virtualbox.org/)
that can be execute on Win, Mac and Linux operating systems.

To execute the Vagrant box you can use the command as follows:

```bash
vagrant up
```

This will require some times (the first execution). When finished, you can see
the application running at `localhost:8080`.

The web directory of the nginx server is configured to the `public` folder
(`/home/ubuntu/zend-expressive-api` in the VM). You have also the logs of the
web server (access_log, error_log) configured in the `log` local folder.

If you want to connect to the VM you can SSH into it, using the command:

```bash
vagrant ssh
```

If you want to close/stop the VM you can use the following command:

```bash
vagrant destroy
```

## REST example

We provide a REST API to a _User_ resource backed by a simple
[SQLite](https://www.sqlite.org) database with a schema as follows:

```sql
CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(80),
  email VARCHAR(255) NOT NULL,
  password VARCHAR(60) NOT NULL
);
```

In order to work with the examples, you will need to create the sample database,
as well as the OAuth2 database. You can do so as follows:

```bash
# Creating and populating the sample database
$ sqlite3 data/users.sqlite < data/schema.sql
$ sqlite3 data/users.sqlite < data/data.sql

# Creating and populating the OAuth2 database
$ sqlite3 data/oauth2.sqlite < vendor/zendframework/zend-expressive-authentication-oauth2/data/oauth2.sql
$ sqlite3 data/oauth2.sqlite < data/oath2_test_users.sql
```

We publish the following URLs:

- GET `/api/user[/{id:\d+}]`
- POST `/api/user` *
- PATCH `/api/user/{id:\d+}` *
- DELETE `/api/user/{id:\d+}` *

(* = requires OAuth2 Authentication)

In order to execute the REST API, you need to run the application via a web
server. To use the PHP internal web server, you can use the following command:

```bash
$ composer serve
```

Below are some usage examples, using [HTTPie](https://httpie.org/) as a client.

### GET /api/users

Request:

```bash
$ http GET :8080/api/users
```

Response:

```http
HTTP/1.1 200 OK
Connection: close
Content-Type: application/hal+json
Date: Mon, 07 May 2018 14:54:46 +0200
Host: localhost:8080

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

### GET /api/users/1

Request:

```bash
$ http GET :8080/api/users/1
```

Response:

```http
HTTP/1.1 200 OK
Connection: close
Content-Type: application/hal+json
Date: Mon, 07 May 2018 14:54:46 +0200
Host: localhost:8080

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
```

Note that the individual users **do not** include the password; we never want to
return passwords from our API!

### POST

Request:

```bash
$ http POST :8080/api/users name=Baz email=baz@host.com password=12345678 "Authorization: Bearer ..."
```

Response:

```http
HTTP/1.1 201 Created
Connection: close
Content-Type: application/hal+json
Date: Mon, 07 May 2018 15:03:05 +0200
Host: localhost:8080
Location: /api/users/3

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

> Note: this method requires an OAuth2 bearer token; see the [OAuth2
> section](#oauth2) for details on how to obtain one.

### PATCH

Request:

```bash
$ http PATCH :8080/api/users/3 name=Enrico "Authorization: Bearer ..."
```

Response:

```http
HTTP/1.1 200 OK
Connection: close
Content-Type: application/hal+json
Date: Mon, 07 May 2018 15:03:59 +0200
Host: localhost:8080

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

> Note: this method requires an OAuth2 bearer token; see the [OAuth2
> section](#oauth2) for details on how to obtain one.

### DELETE

Request:

```bash
$ http DELETE :8080/api/users/3 "Authorization: Bearer ..."
```

Response:

```http
HTTP/1.1 204 No Content
Connection: close
Content-type: text/html; charset=UTF-8
Date: Mon, 07 May 2018 15:04:44 +0200
Host: localhost:8080
```

> Note: this method requires an OAuth2 bearer token; see the [OAuth2
> section](#oauth2) for details on how to obtain one.

### Errors

Whenever an error occurs, the API _should_ raise a Problem Details response.

As an example, if we were to do the following request:

```bash
$ http POST :8080/api/users "Authorization: Bearer ..." username="This is not a valid key"
```

you should see a response like the following:

```http
HTTP/1.1 400 Bad Request
Connection: close
Content-Type: application/problem+json
Date: Wed, 09 May 2018 21:22:08 +0000
Host: localhost:8080

{
    "detail": "Invalid parameter",
    "parameters": {
        "email": {
            "isEmpty": "Value is required and can't be empty"
        },
        "password": {
            "isEmpty": "Value is required and can't be empty"
        }
    },
    "status": 400,
    "title": "Invalid parameter",
    "type": "https://example.com/api/doc/invalid-parameter"
}
```

As another example, requesting an invalid user:

```bash
$ http GET :8080/api/users/9999999999
```

Response:

```http
HTTP/1.1 404 Not Found
Connection: close
Content-Type: application/problem+json
Date: Wed, 09 May 2018 21:37:04 +0000
Host: localhost:8080

{
    "detail": "User not found",
    "status": 404,
    "title": "Resource not found",
    "type": "https://example.com/api/doc/resource-not-found"
}
```

## OAuth2

In order to get a Bearer token for OAuth2 you need to execute the following
command (using the default SQLite OAuth2 database example):

```bash
$ http -f POST :8080/oauth grant_type=password username=user_test \
> password=test client_id=client_test client_secret=test scope=test
```

This will produce output similar to the following:

```http
HTTP/1.1 200 OK
Cache-Control: no-store
Connection: close
Content-Type: application/json; charset=UTF-8
Date: Mon, 07 May 2018 17:49:39 +0200
Host: localhost:8080
Pragma: no-cache

{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJS...Aw",
    "expires_in": 86400,
    "refresh_token": "def502009bbaf70068c8b4007c1b9645d173ce5183...ba3",
    "token_type": "Bearer"
}
```

In order to execute the POST, PATCH, and DELETE methods you need to add the
`access_token` via the `Authorization` header, as follows (with HTTPie command):

```bash
http POST :8080/api/users "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJS...Aw"
```
