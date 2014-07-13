# PHP JSON API

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

A Serializer is responsible for constructing Element (Resource/Collection) objects for a certain resource type. Serializers must extend `Tobscure\JsonApi\SerializerAbstract`. At a minimum, a serializer must specify its **type**, provide a method to transform **attributes**, and specify the API URL at which the resource type can be accessed:

```php
use Tobscure\JsonApi\SerializerAbstract;

class PostSerializer extends SerializerAbstract {

    protected $type = 'posts';

    protected function attributes(Post $post)
    {
        return [
            'title' => $post->title
            'body'  => $post->body
        ];
    }

    public function getUrl()
    {
        return 'http://example.com/api/posts';
    }

}
```

For each **relationship** with another resource, a Serializer should have a method named `link{RelationshipName}` and/or a method named `include{RelationshipName}`. These methods are to return an Element representing the relationship value — a Resource for a to-one relationship, and a Collection for a to-many relationship.

These methods differ in the amount of detail they add to the document: **link** returns an Element only containing the ID(s) of the linked resource(s), whereas **include** returns an Element containing complete representations of the linked resource(s), which are subsequently included in the **linked** section of the Document.

```php
    protected function linkAuthor(Post $post)
    {
        $serializer = new PeopleSerializer;
        return $serializer->item($post->authorId);
    }

    protected function includeAuthor(Post $post, $relations)
    {
        $serializer = new PeopleSerializer($relations);
        return $serializer->item($post->author);
    }
```

When a Serializer is instantiated, a list of relations to **include** may be passed as a constructor argument. (In the case of the primary Element's serializer, you will probably want this to be the exploded value of the ?include= query param.)

Default relations to **link** and **include** can be specified on the serializer:

```php
    protected $link = ['comments'];

    protected $include = ['author'];
```
