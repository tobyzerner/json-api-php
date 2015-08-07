# PHP JSON-API

[JSON-API](http://jsonapi.org) responses in PHP.

Works with version 1.0 RC3 of the spec.

## Install

via Composer:

```bash
composer require tobscure/json-api
```

## Usage

```php
use Tobscure\JsonApi\Document;

// Create a new JSON API Document
$document = new Document();

// Create a new serializer (see below), passing an array of 
// relationships to include
$serializer = new PostSerializer(['author', 'comments']);

// Create a new collection object using the serializer
$collection = $serializer->collection($posts);

// Set that collection as the document's data
$document->setData($collection);

// Add metadata and links
$document->addMeta('total', count($posts));
$document->addLink('next', 'http://example.com/posts?page[offset]=2');

// Get the document as an array and output it as JSON
echo json_encode($document->toArray());
```

### Elements

The JSON-API spec describes *resource objects* as objects containing information about a single resource, and *collection objects* as objects containing information about many resources. In this package:

- `Tobscure\JsonApi\Elements\Resource` represents a *resource object*
- `Tobscure\JsonApi\Elements\Collection` represents a *collection object*

Both Resources and Collections are termed as *Elements*. In conceptually the same way that the JSON-API spec describes, a Resource may **link** to any number of other Elements (Resource for has-one relationships, Collection for has-many). Similarly, a Collection may contain many Resources.

A JSON-API Document may contain one primary Element. The primary Element will be recursively parsed for **links** to other Elements; these Elements will be added to the Document as **included** resources.

### Serializers

A Serializer is responsible for constructing Element (Resource/Collection) objects for a certain resource type. Serializers should extend `Tobscure\JsonApi\AbstractSerializer`. At a minimum, a serializer must specify its **type** and provide a method to transform **attributes**:

```php
use Tobscure\JsonApi\AbstractSerializer;

class PostSerializer extends AbstractSerializer
{
    protected $type = 'posts';

    protected function getAttributes($post)
    {
        return [
            'title' => $post->title,
            'body'  => $post->body,
        ];
    }
}
```

By default, a Resource object's **id** attribute will be set as the `id` property on the model. A serializer can provide a method to override this:

```php
protected function getId($post)
{
    return $post->someOtherKey;
}
```

#### Relationships 

A Serializer should have a method for each relationship that can be linked or included on a resource. This method should return a Closure which accepts four arguments:

- `$model` (object) The parent model that is being serialized
- `$include` (boolean) Whether or not this relationship's resource(s) are being included, or just linked
- `$included` (array) The relationships that are to be included on this resource
- `$linked` (array) The relationships that are to be linked on this resource

The Closure should return a `Tobscure\JsonApi\Relationship` object, which represents a **relationship object**. When all of this is put together, it might look something like this:

```php
protected function comments()
{
    return function ($post, $include, array $included, array $linked) {
        $serializer = new CommentSerializer($included, $linked);
        $comments = $serializer->collection($include ? $post->comments : $post->commentIds);

        $relationship = new Relationship($comments);
        $relationship->setMeta('key', 'value');

        return $relationship;
    };
}
```

When a Serializer is instantiated, a list of relationships to **include** may be passed as the first constructor argument. (In the case of the primary Element's serializer, you will probably want this to be the exploded value of the ?include query param.) A list of relationships to **link** may be passed as the second constructor argument.

### Criteria

```php
use Tobscure\JsonApi\Criteria;

$criteria = new Criteria($_GET);

$include = $criteria->getInclude(); // ?include=foo,bar => ['foo', 'bar']

$sort = $criteria->getSort(); // ?sort=+foo,-bar => ['foo' => 'asc', 'bar' => 'desc']

$offset = $criteria->getOffset(); // ?page[offset]=10 => 10 (defaults to 0)

$limit = $criteria->getLimit();  // ?page[limit]=50 => 50 (defaults to null)
```

## License

PHP JSON-API is licensed under [The MIT License (MIT)](LICENSE).

