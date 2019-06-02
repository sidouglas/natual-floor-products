=== Plugin Name ===
Contributors: Simon Douglas
Donate link: si@simondouglas.com
Tags: comments, spam
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

** Rest API **

28-04-2019
get all products: /wp-json/natural-floor-products/v1/products
post endpoint: /wp-json/natural-floor-products/v1/products/post

If an id exists then the post is an update operation, otherwise it's an insert.
Responses are in the WP_REST API format code/message/data

```
{
    "code": "rest_missing_callback_param",
    "message": "Missing parameter(s): token",
    "data": {
        "status": 400,
        "params": [
            "token"
        ]
    }
}
```
