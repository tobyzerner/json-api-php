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

$resource = new PostResource($post);

$document = Document::fromData($resource);

$document->setInclude(['author', 'comments']);
$document->setFields(['posts' => ['title', 'body']]);

$document->setMetaItem('total', count($posts));
$document->setSelfLink('http://example.com/api/posts/1');

header('Content-Type: ' . $document::MEDIA_TYPE);
echo json_encode($document);
```

### Resources

Resources are used to create JSON-API [resource objects](http://jsonapi.org/format/#document-resource-objects). They must implement `Tobscure\JsonApi\ResourceInterface`. An `AbstractResource` class is provided with some basic functionality. Subclasses must specify the resource `$type` and implement the `getId()` method:

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

A JSON-API document can then be created from an instantiated resource:

```php
$resource = new PostResource($post);

$document = Document::fromData($resource);
```

To output a collection of resource objects, map your data to an array of resources:

```php
$resources = array_map(function (Post $post) {
    return new PostResource($post);
}, $posts);

$document = Document::fromData($resources);
```

#### Attributes & Sparse Fieldsets

To add [attributes](http://jsonapi.org/format/#document-resource-object-attributes) to your resource objects, you may implement the `getAttributes()` method in your resource:

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

To output resource objects with a [sparse fieldset](http://jsonapi.org/format/#fetching-sparse-fieldsets), pass in an array of [fields](http://jsonapi.org/format/#document-resource-object-fields) (attributes and relationships), organised by resource type:

```php
$document->setFields(['posts' => ['title', 'body']]);
```

The attributes returned by your resources will automatically be filtered according to the sparse fieldset for the resource type. However, if some attributes are expensive to calculate, then you can use the `$fields` argument provided to `getAttributes()`. This will be an `array` of fields, or `null` if no sparse fieldset has been specified.

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

You can [include related resources](http://jsonapi.org/format/#document-compound-documents) alongside the document's primary data. First you must define the available relationships on your resource. The `AbstractResource` base class allows you to define a method for each relationship. Relationship methods should return a `Tobscure\JsonApi\Relationship` instance, containing the related resource(s).

```php
    protected function getAuthorRelationship()
    {
        $resource = new UserResource($this->post->author);

        return Relationship::fromData($resource);
    }
```

You can then specify which relationship paths should be included on the document:

```php
$document->setInclude(['author', 'comments', 'comments.author']);
```

By default, the `AbstractResource` implementation will convert included relationship names from `kebab-case` and `snake_case` into a `getCamelCaseRelationship` method name. If you wish to customize this behaviour, you may override the `getRelationship` method:

```php
    public function getRelationship($name)
    {
        // resolve Relationship for $name
    }
```

### Meta Information & Links

The `Document`, `AbstractResource`, and `Relationship` classes allow you to add [meta information](http://jsonapi.org/format/#document-meta):

```php
$document->setMeta(['key' => 'value']);
$document->setMetaItem('key', 'value');
$document->removeMetaItem('key');
```

They also allow you to add [links](http://jsonapi.org/format/#document-links). A link's value may be a string, or a `Tobscure\JsonApi\Link` instance.

```php
use Tobscure\JsonApi\Link;

$resource->setSelfLink('url');
$relationship->setRelatedLink(new Link('url', ['some' => 'metadata']));
```

You can also easily generate [pagination links](http://jsonapi.org/format/#fetching-pagination) on `Document` and `Relationship` instances:

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
        $this->setMetaItem('some', 'metadata for ' . $post->id);
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

You can create a `Document` containing [error objects](http://jsonapi.org/format/#error-objects) using `Tobscure\JsonApi\Error` instances:

```php
use Tobscure\JsonApi\Error;

$error = new Error;

$error->setId('1');
$error->setAboutLink('url');
$error->setMeta('key', 'value');
$error->setStatus(400);
$error->setCode('123');
$error->setTitle('Something Went Wrong');
$error->setDetail('You dun goofed!');
$error->setSourcePointer('/data/attributes/body');
$error->setSourceParameter('include');

$document = Document::fromErrors([$error]);
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

