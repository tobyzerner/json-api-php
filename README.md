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

// Create a new resource to represent a post.
$resource = new PostResource($post);

// Create a new JSON-API document with that resource as the data.
$document = new Document($resource);

// Specify relationships to be included.
$document->setInclude(['author', 'comments']);

// Add metadata and links.
$document->addMeta('total', count($posts));
$document->addLink('self', 'http://example.com/api/posts');

// Output the document as JSON.
header('Content-Type: ' . $document::MEDIA_TYPE);
echo $document;
```

### Resources & Collections

The JSON-API spec describes *resource objects* as objects containing information about a single resource. A resource object is represented by the `Tobscure\JsonApi\ResourceInterface` interface. An `AbstractResource` class is provided with some basic functionality. At a minimum, subclasses must specify the resource `$type` and implement the `getId()` method:

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

To create a collection of resources, simply map your data to an array of Resource objects:

```php
$collection = array_map(function (Post $post) {
    return new PostResource($post);
}, $posts);

$document = new Document($collection);
```

#### Attributes & Sparse Fieldsets

Resource objects may implement a `getAttributes()` method to specify attributes:

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

For sparse fieldsets, you may specify which fields (attributes and relationships) are to be included on the Document. You must provide a multidimensional array organized by resource type:

```php
$document->setFields(['posts' => ['title', 'body']]);
```

The attributes returned by your resources will automatically be filtered according to the sparse fieldset for the resource type. If some attributes are expensive to calculate, then you may use the `$fields` argument to improve performance when sparse fieldsets are used. This argument will be `null` if no sparse fieldset has been specified for the resource type, or an `array` of fields if it has:

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

A JSON-API Document may contain one primary resource, or a collection of resources. The primary resource(s) will be recursively parsed for relationships with other resources; these resources will be added to the Document as **included** resources.

The `AbstractResource` class allows you to define a public method for each relationship that exists for a resource. A relationship method should return a `Tobscure\JsonApi\Relationship` instance.

```php
    public function author()
    {
        $resource = new UserResource($this->post->author);

        return new Relationship($resource);
    }
```

By default, the `AbstractResource` will convert relationship names from `kebab-case` and `snake_case` into a `camelCase` method name. If you wish to customize this behaviour, you may override the `getRelationship` method:

```php
    public function getRelationship($name)
    {
        // resolve Relationship for $name
    }
```

Once relationships are defined, you can specify which relationships should be included on the Document:

```php
$document->setInclude(['author', 'comments', 'comments.author']);
```

### Meta & Links

The `Document`, `Resource`, and `Relationship` classes allow you to add meta information:

```php
$document = new Document;
$document->addMeta('key', 'value');
$document->setMeta(['key' => 'value']);
```

They also allow you to add links in a similar way:

```php
$resource = new PostResource($post);
$resource->addLink('self', 'url');
$resource->setLinks(['key' => 'value']);
```

You can also easily add pagination links:

```php
$document->addPaginationLinks(
    'url', // The base URL for the links
    [],    // The query params provided in the request
    40,    // The current offset
    20,    // The current limit
    100    // The total number of results
);
```

To define metadata and/or links on resources implicitly, call the appropriate methods in the constructor:

```php
use Tobscure\JsonApi\AbstractResource;

class PostResource extends AbstractResource
{    
    public function __construct(Post $post)
    {
        $this->post = $post;

        $this->addLink('self', '/posts/' . $post->id);
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

Get the relationships requested for inclusion. Provide an array of available relationship paths; if anything else is present, an `InvalidParameterException` will be thrown.

```php
// GET /api?include=author,comments
$include = $parameters->getInclude(['author', 'comments', 'comments.author']); // ['author', 'comments']
```

#### getFields

Get the fields requested for inclusion, keyed by resource type.

```php
// GET /api?fields[articles]=title,body
$fields = $parameters->getFields(); // ['articles' => ['title', 'body']]
```

#### getSort

Get the requested sort criteria. Provide an array of available fields that can be sorted by; if anything else is present, an `InvalidParameterException` will be thrown.

```php
// GET /api?sort=-created,title
$sort = $parameters->getSort(['title', 'created']); // ['created' => 'desc', 'title' => 'asc']
```

#### getLimit and getOffset

Get the offset number and the number of resources to display using a page- or offset-based strategy. `getLimit` accepts an optional maximum. If the calculated offset is below zero, an `InvalidParameterException` will be thrown.

```php
// GET /api?page[number]=5&page[size]=20
$limit = $parameters->getLimit(100); // 20
$offset = $parameters->getOffset($limit); // 80

// GET /api?page[offset]=20&page[limit]=200
$limit = $parameters->getLimit(100); // 100
$offset = $parameters->getOffset(); // 20
```

### Error Handling

You can transform caught exceptions into JSON-API error documents using the `Tobscure\JsonApi\ErrorHandler` class. You must register the appropriate `Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface` instances.

```php
try {
    // API handling code
} catch (Exception $e) {
    $errors = new ErrorHandler;

    $errors->registerHandler(new InvalidParameterExceptionHandler);
    $errors->registerHandler(new FallbackExceptionHandler);

    $response = $errors->handle($e);

    $document = new Document;
    $document->setErrors($response->getErrors());

    return new JsonResponse($document, $response->getStatus());
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

