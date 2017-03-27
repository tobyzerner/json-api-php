# PHP JSON-API

[![Build Status](https://img.shields.io/travis/tobscure/json-api/master.svg?style=flat)](https://travis-ci.org/tobscure/json-api)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/tobscure/json-api.svg?style=flat)](https://scrutinizer-ci.com/g/tobscure/json-api/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/tobscure/json-api.svg?style=flat)](https://scrutinizer-ci.com/g/tobscure/json-api)
[![Pre Release](https://img.shields.io/packagist/vpre/tobscure/json-api.svg?style=flat)](https://github.com/tobscure/json-api/releases)
[![License](https://img.shields.io/packagist/l/tobscure/json-api.svg?style=flat)](https://packagist.org/packages/tobscure/json-api)

[JSON-API](http://jsonapi.org) responses in PHP.

Works with version 1.0 of the spec.

## Install

via Composer:

```bash
composer require tobscure/json-api
```

## Usage

```php
use Tobscure\JsonApi\Document;

// Create a resource object for a post.
$resource = new PostResource($post);

// Create a JSON-API document with that resource as the primary data.
$document = new Document($resource);

// Specify included relationships and sparse fieldsets.
$document->setInclude(['author', 'comments']);
$document->setFields(['posts' => ['title', 'body']]);

// Add metadata and links.
$document->addMeta('total', count($posts));
$document->setSelfLink('http://example.com/api/posts');

// Output the document with the JSON-API media type.
header('Content-Type: ' . $document::MEDIA_TYPE);
echo $document;
```

### Resources & Collections

The JSON-API spec describes [resource objects](http://jsonapi.org/format/#document-resource-objects) as objects representing about a single resource. A resource object is represented by the `Tobscure\JsonApi\ResourceInterface` interface.

You should create a class which implements this interface for each resource type in your API. A base `AbstractResource` class is provided with some basic functionality. At a minimum, subclasses must specify the resource `$type` and implement the `getId()` method:

```php
use Tobscure\JsonApi\AbstractResource;

class PostResource extends AbstractResource
{
    protected $type = 'posts';

    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function getId()
    {
        return $this->post->id;
    }
}
```

An instantiated resource object can then be added to the JSON-API document:

```php
$resource = new PostResource($post);

$document = new Document($resource);
```

To output a collection of resources, map your data to an array of Resource objects:

```php
$collection = array_map(function (Post $post) {
    return new PostResource($post);
}, $posts);

$document = new Document($collection);
```

#### Attributes & Sparse Fieldsets

To add [attributes](http://jsonapi.org/format/#document-resource-object-attributes) to your resources, you may implement the `getAttributes()` method:

```php
    public function getAttributes(array $fields = null)
    {
        return [
            'title' => $this->post->title,
            'body'  => $this->post->body,
            'date'  => $this->post->date
        ];
    }
```

To support [sparse fieldsets](http://jsonapi.org/format/#fetching-sparse-fieldsets), you may specify which [fields](http://jsonapi.org/format/#document-resource-object-fields) (attributes and relationships) are to be included on the Document. You must provide a multidimensional array organized by resource type:

```php
$document->setFields(['posts' => ['title', 'body']]);
```

The attributes returned by your Resources will automatically be filtered according to the sparse fieldset for the resource type. However, if some attributes are expensive to calculate, then you may use the `$fields` argument provided to `getAttributes()` to improve performance when sparse fieldsets are used. This argument will be `null` if no sparse fieldset has been specified for the resource type, or an `array` of fields if it has:

```php
    public function getAttributes(array $fields = null)
    {
        // Calculate the "expensive" attribute only if this field will show up
        // in the final output
        if ($fields === null || in_array('expensive', $fields)) {
            $attributes['expensive'] = $this->getExpensiveAttribute();
        }

        return $attributes;
    }
```

#### Relationships

To support the [inclusion of related resources](http://jsonapi.org/format/#fetching-includes) alongside the document's primary resources (and output [compound documents](http://jsonapi.org/format/#document-compound-documents)), first you must define the available relationships on your Resource implementation. The `AbstractResource` base class allows you to define a method for each relationship that exists for a resource type. Relationship methods should return a `Tobscure\JsonApi\Relationship` instance, containing the related Resource(s).

```php
    protected function author()
    {
        $resource = new UserResource($this->post->author);

        return new Relationship($resource);
    }
```

You can then specify which relationships should be included on the Document:

```php
$document->setInclude(['author', 'comments', 'comments.author']);
```

By default, the `AbstractResource` implementation will convert included relationship names from `kebab-case` and `snake_case` into a `camelCase` method name. If you wish to customize this behaviour, you may override the `getRelationship` method:

```php
    public function getRelationship($name)
    {
        // resolve Relationship for $name
    }
```

### Meta Information & Links

The `Document`, `Resource`, and `Relationship` classes allow you to add [meta information](http://jsonapi.org/format/#document-meta):

```php
$document->addMeta('key', 'value');
$document->setMeta(['key' => 'value']);
```

They also allow you to add [links](http://jsonapi.org/format/#document-links):

```php
$resource->setSelfLink(new Link('url', ['meta' => 'information']));

$relationship->setRelatedLink('url');
```

You can also easily generate [pagination](http://jsonapi.org/format/#fetching-pagination) links:

```php
$document->setPaginationLinks(
    'url', // The base URL for the links
    $_GET, // The query params provided in the request
    40,    // The current offset
    20,    // The current limit
    100    // The total number of results
);
```

To define meta information and/or links globally for a resource type, call the appropriate methods in the constructor:

```php
use Tobscure\JsonApi\AbstractResource;

class PostResource extends AbstractResource
{    
    public function __construct(Post $post)
    {
        $this->post = $post;

        $this->setSelfLink('/posts/' . $post->id);

        $this->addMeta('some', 'metadata for ' . $post->id);
    }

    // ...
}
```

### Parameters

The `Tobscure\JsonApi\Parameters` class allows you to easily parse and validate query parameters in accordance with the specification.

```php
use Tobscure\JsonApi\Parameters;

$parameters = new Parameters($_GET);
```

#### getInclude

Get the relationships requested for [inclusion](http://jsonapi.org/format/#fetching-includes). Provide an array of available relationship paths; if anything else is present, an `InvalidParameterException` will be thrown.

```php
// GET /api?include=author,comments
$include = $parameters->getInclude(['author', 'comments', 'comments.author']); // ['author', 'comments']

$document->setInclude($include);
```

#### getFields

Get the [sparse fieldsets](http://jsonapi.org/format/#fetching-sparse-fieldsets) requested for inclusion, keyed by resource type.

```php
// GET /api?fields[articles]=title,body
$fields = $parameters->getFields(); // ['articles' => ['title', 'body']]

$document->setFields($fields);
```

#### getSort

Get the requested [sort fields](http://jsonapi.org/format/#fetching-sorting). Provide an array of available fields that can be sorted by; if anything else is present, an `InvalidParameterException` will be thrown.

```php
// GET /api?sort=-created,title
$sort = $parameters->getSort(['title', 'created']); // ['created' => 'desc', 'title' => 'asc']
```

#### getLimit and getOffset

Get the offset number and the number of resources to display using a [page- or offset-based strategy](http://jsonapi.org/format/#fetching-pagination). `getLimit` accepts an optional maximum. If the calculated offset is below zero, an `InvalidParameterException` will be thrown.

```php
// GET /api?page[number]=5&page[size]=20
$limit = $parameters->getLimit(100); // 20
$offset = $parameters->getOffset($limit); // 80

// GET /api?page[offset]=20&page[limit]=200
$limit = $parameters->getLimit(100); // 100
$offset = $parameters->getOffset(); // 20
```

#### getFilter

Get the contents of the [filter](http://jsonapi.org/format/#fetching-filtering) query parameter.

```php
// GET /api?filter[author]=toby
$filter = $parameters->getFilter(); // ['author' => 'toby']
```

### Errors

The `Tobscure\JsonApi\Error\ErrorResponseInterface` interface represents the information required to produce an error response: a HTTP status code to respond with, and an array of [error objects](http://jsonapi.org/format/#error-objects). 

A couple of implementations are already provided:

* `Tobscure\JsonApi\Error\InternalServerErrorResponse` for a generic 500 Internal Server Error response.
* `Tobscure\JsonApi\Error\InvalidParameterErrorResponse` for an error response corresponding to an `InvalidParameterException`.

A Document containing the errors from an error response can be created using the `fromErrorResponse` constructor:

```php
use Tobscure\JsonApi\Error\InternalServerErrorResponse;

$response = new InternalServerErrorResponse;

$document = Document::fromErrorResponse($response);

http_response_code($response->getStatusCode());
header('Content-Type: ' . $document::MEDIA_TYPE);
echo $document;
```

In order to translate a caught `Exception` into a JSON-API error response, you should implement the `ErrorResponseInterface` for each type of `Exception` you wish to handle:

```php
class MyCustomErrorResponse implements ErrorResponseInterface
{
    protected $e;

    public function __construct(MyCustomException $e)
    {
        $this->e = $e;
    }

    public function getStatusCode()
    {
        return 400;
    }

    public function getErrors()
    {
        $error = new Error;

        $error->setId('1');
        $error->setAboutLink('url');
        $error->setMeta(['key' => 'value']);
        $error->setStatusCode(400);
        $error->setErrorCode('my-custom-error-code');
        $error->setTitle('My Custom Error');
        $error->setDetail('You dun goofed!');
        $error->setSourcePointer('/data/attributes/custom');
        $error->setSourceParameter('include');

        return [$error];
    }
}
```

You can then instantiate the correct error response according to the type of `Exception` that has been caught:

```php
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Error\InternalServerErrorResponse;
use Tobscure\JsonApi\Error\InvalidParameterErrorResponse;
use Tobscure\JsonApi\Exception\InvalidParameterException;

try {
    // API handling code
} catch (Exception $e) {
    switch (true) {
        case $e instanceof MyCustomException:
            $response = new MyCustomErrorResponse($e);
            break;

        case $e instanceof InvalidParameterException:
            $response = new InvalidParameterErrorResponse($e);
            break;

        default:
            $response = new InternalServerErrorResponse;
    }

    $document = Document::fromErrorResponse($response);

    http_response_code($response->getStatusCode());
    header('Content-Type: ' . $document::MEDIA_TYPE);
    echo $document;
}
```

## Examples

* [Flarum](https://github.com/flarum/core/tree/master/src/Api) is forum software that uses tobscure/json-api to power its API.

## Contributing

Feel free to send pull requests or create issues if you come across problems or have great ideas. Any input is appreciated!

### Running Tests

```bash
$ vendor/bin/phpunit
```

## License

This code is published under the [The MIT License](LICENSE). This means you can do almost anything with it, as long as the copyright notice and the accompanying license file is left intact.

