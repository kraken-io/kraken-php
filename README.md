kraken-php
==========

With this official Kraken PHP library you can plug into the power and speed of [Kraken.io](http://kraken.io/) Image Optimizer.

* [Getting Started](#getting-started)
* [How To Use](#how-to-use)
* [Wait and Callback URL](#wait-and-callback-url)
  * [Wait Option](#wait-option)
  * [Callback URL](#callback-url)
* [Authentication](#authentication)
* [Usage - Image URL](#usage---image-url)
* [Usage - Image Upload](#usage---image-upload)
* [Lossy Optimizations](#lossy-optimizations)
* [Image Resizing](#image-resizing)
* [Amazon S3 and Rackspace Cloud Files Integration](#amazon-s3-and-rackspace-cloud-files)
  * [Amazon S3](#amazon-s3)
  * [Rackspace Cloud Files](#rackspace-cloud-files)


## Getting Started

First you need to sign-up for a [Kraken API](http://kraken.io/plans/) and obtain your unique **API Key** and **API Secret**. You will find both under [API Credentials](http://kraken.io/account/api-credentials). Once you have set-up your account, you can start using Kraken API in your applications.

## How to use

You can optimize your images in two ways - by providing an URL of the image you want to optimize or by uploading an image file directly to Kraken API.

The first option (image URL) is great for images that are already in production or any other place on the Internet. The second one (direct upload) is ideal for your deployment process, build script or the on-the-fly processing of your user's uploads where you don't have the images available on-line yet.

## Wait and Callback URL

Kraken gives you two options for fetching optimization results. With `wait` option the results will be returned immediately in the response. With `callback_url` the results will be posted to the URL specified in your request.

### Wait option

With the `wait` option turned on for every request to the API, the connection will be hold open unil the image has been optimized. Once this is done you will get a immediate response with a JSON object containing optimization results. To use this option simply set `"wait": true` property in your request.

**Request:**

````js
{
  "auth": {
    "api_key": "your-api-key",
    "api_secret": "your-api-secret"
  },
  "url": "http://image-url.com/file.jpg",
  "wait": true
}
````

**Response**

````js
{
  "success": true,
  "file_name": "file.jpg",
  "original_size": 324520,
  "kraked_size": 165358,
  "saved_bytes": 159162,
  "kraked_url": "https://api.kraken.io/files/d2bcaa126cc07728/file.jpg"
}
````

### Callback URL

With the Callback URL the HTTPS connection will be terminated immediately and a unique `id` will be returned in the response body. After the optimization is over Kraken will POST a message to the `callback_url` specified in your request. The ID in the response will reflect the ID in the results posted to your Callback URL.

We recommend [requestb.in](http://requestb.in) as an easy way to capture optimization results for initial testing.

**Request:**

````js
{
  "auth": {
    "api_key": "your-api-key",
    "api_secret": "your-api-secret"
  },
  "url": "http://image-url.com/file.jpg",
  "callback_url": "http://awesome-website.com/kraken_results"
}
````

**Response:**

````js
{
  "id": "0194b7d7cb"
}
````

**Results posted to the Callback URL:**

````js
{
  "id": "0194b7d7cb"
  "success": true,
  "file_name": "file.jpg",
  "original_size": 324520,
  "kraked_size": 165358,
  "saved_bytes": 159162,
  "kraked_url": "https://api.kraken.io/files/b15e7f341645a65a/file.jpg"
}
````

## Authentication

First step is to authenticate with Kraken API by providing your unique API Key and API Secret while creating new Kraken instance:

````php
<?php

require_once("Kraken.php");

$kraken = new Kraken("your-api-key", "your-api-secret");
````

## Usage - Image URL

To optimize an image by providing image URL use the `kraken->url()` method. You will need to provide two mandatory parameters in an array - `url` to the image and `wait` or `callback_url`:

````php
<?php

require_once("Kraken.php");

$kraken = new Kraken("your-api-key", "your-api-secret");

$params = array(
    "url" => "http://url-to-image.com/file.jpg",
    "wait" => true
);

$data = $kraken->url($params);
````

Depending on a choosen response option (Wait or Callback URL) in the `$data` array you will find either the optimization ID or optimization results containing `success` property, file name, original file size, kraked file size, amount of savings and optimized image URL:

````php
array(6) {
    'success' =>
    bool(true)
    'file_name' =>
    string(8) "file.jpg"
    'original_size' =>
    int(62422)
    'kraked_size' =>
    int(52783)
    'saved_bytes' =>
    int(9639)
    'kraked_url' =>
    string(65) "https://api.kraken.io/files/3a30e2eb204d7607/file.jpg"
}
````

If no saving were found, the API will return an object containing `"success":false` and a proper error message:

````php
array(6) {
    'success' =>
    bool(false)
    'file_name' =>
    string(8) "file.jpg"
    'error' =>
    string(43) "This image can not be optimized any further"
}
````

## Usage - Image Upload

If you want to upload your images directly to Kraken API use the `kraken->upload()` method. You will need to provide two mandatory parameters in an array - `file` which is the absolute path to the file and `wait` or `callback_url`.

In the `$data` array you will find the same optimization properties as with `url` option above.

````php
<?php

require_once("Kraken.php");

$kraken = new Kraken("your-api-key", "your-api-secret");

$params = array(
    "file" => "/path/to/image/file.jpg",
    "wait" => true
);

$data = $kraken->upload($params);
````

## Image Resizing

Image resizing option is great for creating thumbnails or preview images in your applications. Kraken will first resize the given image and then optimize it with it's vast array of optimization algorythms. The `resize` option needs a few parameters to be passed like desired `width` and/or `height` and a mandatory `strategy` property. For example:

````php
<?php

require_once("Kraken.php");

$kraken = new Kraken("your-api-key", "your-api-secret");

$params = array(
    "file" => "/path/to/image/file.jpg",
    "wait" => true,
    "resize" => array(
        "width" => 100,
        "height" => 75,
        "strategy" => "crop"
    )
);

$data = $kraken->upload($params);
````

The `strategy` property can have one of the following values:

- `exact` - Resize by exact width/height. No aspect ratio will be maintained.
- `portrait` - Exact width will be set, height will be adjusted according to aspect ratio.
- `landscape` - Exact height will be set, width will be adjusted according to aspect ratio.
- `auto` - The best strategy (portrait or landscape) will be selected for a given image according to aspect ratio.
- `crop` - This option will crop your image to the exact size you specify with no distortion.

## Amazon S3 and Rackspace Cloud Files

Kraken API allows you to store optimized images directly in your S3 bucket or Cloud Files container. With just a few addidtional parameters your optimized images will be pushed to your external storage in no-time.

### Amazon S3

**Mandatory Parameters:**
- `key` - Your unique Amazon "Access Key ID".
- `secret` - Your unique Amazon "Secret Access Key".
- `bucket` - Name of a destination container on your Amazon S3 account.

**Optional Parameters:**
- `path` - Destination path in your S3 bucket (e.g. `"images/layout/header.jpg"`). Defaults to root `"/"`.
- `acl` - Permissions of a destination object. This can be `"public_read"` or `"private"`. Defaults to `"public_read"`.

The above parameters must be passed in a `s3_store` key:

````php
<?php

require_once("Kraken.php");

$kraken = new Kraken("your-api-key", "your-api-secret");

$params = array(
    "file" => "/path/to/image/file.jpg",
    "wait" => true,
    "s3_store" => array(
        "key" => "your-amazon-access-key",
        "secret" => "your-amazon-secret-key",
        "bucket" => "destination-bucket"
    )
);

$data = $kraken->upload($params);
````

The `$data` array will contain `kraked_url` key pointing directly to the optimized file location in your Amazon S3 account:

````php
"kraked_url" => "http://s3.amazonaws.com/YOUR_CONTAINER/path/to/file.jpg"
````

### Rackspace Cloud Files

**Mandatory Parameters:**
- `user` - Your Rackspace username.
- `key` - Your unique Cloud Files API Key.
- `container` - Name of a destination container on your Cloud Files account.

**Optional Parameters:**
- `path` - Destination path in your container (e.g. `"images/layout/header.jpg"`). Defaults to root `"/"`.

The above parameters must be passed in a `cf_store` key:

````php
<?php

require_once("Kraken.php");

$kraken = new Kraken("your-api-key", "your-api-secret");

$params = array(
    "file" => "/path/to/image/file.jpg",
    "wait" => true,
    "cf_store" => array(
        "user" => "your-rackspace-username",
        "key" => "your-rackspace-api-key",
        "container" => "destination-container"
    )
);

$data = $kraken->upload($params);
````

If your container is CDN-enabled, the optimization results will contain `kraked_url` ket pointing directly to the optimized file location in your Cloud Files container, for example:

````php
"kraked_url" => "http://e9ffc04970a269a54eeb-cc00fdd2d4f11dffd931005c9e8de53a.r2.cf1.rackcdn.com/path/to/file.jpg"
````

If your container is not CDN-enabled `kraked_url` will point to optimized image URL in the Kraken API.

## LICENSE - MIT

Copyright (c) 2013 Nekkra UG

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
