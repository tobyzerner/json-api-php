# PHP JSON-API

[JSON-API](http://jsonapi.org) responses in PHP.

## Install

via Composer:

    "tobscure/json-api": "*"

## Usage

```php
use Tobscure\JsonApi\Document;

// Create a new JSON API Document
$document = new Document;

// Create a new serializer (see below), passing an array of 
// relations to include
$serializer = new PostSerializer(['author', 'comments']);

// Create a new collection element using the serializer
$collection = $serializer->collection($posts);

// Set that collection as the document's primary element
$document->setPrimaryElement($collection);

// Add metadata
$document->addMeta('total', count($posts));

// Get the document as an array and output it as JSON
echo json_encode($document->toArray());
```

### Elements

The JSON-API spec describes *resource objects* as objects containing information about a single resource, and *collection objects* as objects containing information about many resources. In this package:

- `Tobscure\JsonApi\Elements\Resource` represents a *resource object*
- `Tobscure\JsonApi\Elements\Collection` represents a *collection object*

Both Resources and Collections are termed as *Elements*. In conceptually the same way that the JSON-API spec describes, a Resource may **link** to any number of other Elements (Resource for has-one relationships, Collection for has-many.) Similarly, a Collection may contain many Resources.

A JSON-API Document may contain one primary Element. The primary Element will be recursively parsed for **links** to other Elements; these Elements will be added to the Document as **linked** resources.

### Serializers

A Serializer is responsible for constructing Element (Resource/Collection) objects for a certain resource type. Serializers must extend `Tobscure\JsonApi\SerializerAbstract`. At a minimum, a serializer must specify its **type**, provide a method to transform **attributes**, and specify the URL template at which the resource type can be accessed:

```php
use Tobscure\JsonApi\SerializerAbstract;

class PostSerializer extends SerializerAbstract
{
    protected $type = 'posts';

    protected function attributes(Post $post)
    {
        return [
            'title' => $post->title,
            'body'  => $post->body,
        ];
    }

    protected function href()
    {
        return [
            'posts' => 'http://example.com/api/posts/{posts.id}'
        ];
    }
}
```

#### URL Templates

For each **has-many relationship**, a Serializer should include a URL template in the array returned by the `href` method. This URL template will automatically be included in the top-level `links` section of the Document.

```php
    protected function href()
    {
        return [
            'posts'    => 'http://example.com/api/posts/{posts.id}',
            'comments' => 'http://example.com/api/posts/{posts.id}/comments'
        ];
    }
```

#### Links 

For each relationship where the resource ID(s) are specified inline, a Serializer should have a method named `link{RelationshipName}`. This method should return an Element representing the relationship value: a Resource for a to-one relationship, and a Collection for a to-many relationship. The Resources should not contain any attributes — only IDs.

```php
    protected function linkComments(Post $post)
    {
        $serializer = new CommentSerializer;
        return $serializer->collection($post->commentIds);
    }
```

Relations to link in this manner should be specified on the serializer:

```php
    protected $link = ['comments'];
```

#### Includes

For each relationship where the resource object(s) can be sideloaded, a Serializer should have a method named `include{RelationshipName}`. This method should return an Element representing the relationship value: a Resource for a to-one relationship, and a Collection for a to-many relationship.

```php
    protected function includeAuthor(Post $post, $relations)
    {
        $serializer = new PersonSerializer($relations);
        return $serializer->resource($post->author);
    }
```

When a Serializer is instantiated, a list of relations to **include** may be passed as a constructor argument. (In the case of the primary Element's serializer, you will probably want this to be the exploded value of the ?include query param.)

Default relations to **include** can be specified on the serializer:

```php
    protected $include = ['author'];
```
