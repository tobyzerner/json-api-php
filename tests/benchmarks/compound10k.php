<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Relationship;
use Tobscure\JsonApi\ResourceIdentifier;
use Tobscure\JsonApi\ResourceInterface;

class Dan implements ResourceInterface
{
    public function getType()
    {
        return 'people';
    }

    public function getId()
    {
        return '9';
    }

    public function getAttributes(array $fields = null)
    {
        return [
            'first-name' => 'Dan',
            'last-name' => 'Gebhardt',
            'twitter' => 'dgeb'
        ];
    }

    public function getLinks()
    {
        return [
            'self' => 'http://example.com/people/9'
        ];
    }

    public function getMeta()
    {
        return [];
    }

    public function getRelationship($name)
    {
    }
};

class Comment05 implements ResourceInterface
{
    public function getType()
    {
        return 'articles';
    }

    public function getId()
    {
        return '5';
    }

    public function getAttributes(array $fields = null)
    {
        return [
            "body" => "First!"
        ];
    }

    public function getLinks()
    {
        return [
            "self" => "http://example.com/comments/5"
        ];
    }

    public function getMeta()
    {
        return [];
    }

    public function getRelationship($name)
    {
        if ($name === 'author') {
            return Relationship::fromData(new ResourceIdentifier('people', '2'));
        }
    }
}

class Comment12 implements ResourceInterface
{
    public function getType()
    {
        return 'articles';
    }

    public function getId()
    {
        return '12';
    }

    public function getAttributes(array $fields = null)
    {
        return [
            "body" => "I like XML better"
        ];
    }

    public function getLinks()
    {
        return [
            "self" => "http://example.com/comments/12"
        ];
    }

    public function getMeta()
    {
        return [];
    }

    public function getRelationship($name)
    {
        if ($name === 'author') {
            return Relationship::fromData(new Dan());
        }
    }
}

class Article implements ResourceInterface {
    public function getType()
    {
        return 'articles';
    }

    public function getId()
    {
        return '1';
    }

    public function getAttributes(array $fields = null)
    {
        return [
            'title' => 'JSON API paints my bikeshed!'
        ];
    }

    public function getLinks()
    {
        return [
            'self' => 'http://example.com/articles/1'
        ];
    }

    public function getMeta()
    {
        return [];
    }

    public function getRelationship($name)
    {
        if ($name === 'author') {
            $author = Relationship::fromData(new Dan());
            $author->setLink('self', 'http://example.com/articles/1/relationships/author');
            $author->setLink('related', 'http://example.com/articles/1/author');
            return $author;
        }
        if ($name === 'comments') {
            $comments = Relationship::fromData([new Comment05(), new Comment12()]);
            $comments->setLink("self", "http://example.com/articles/1/relationships/comments");
            $comments->setLink("related", "http://example.com/articles/1/comments");
            return $comments;
        }
    }
};
$article = new Article();
for ($i = 0; $i < 10000; $i++) {
    $doc = Document::fromData($article);
    $doc->setLink('self', 'http://example.com/articles');
    $doc->setLink('next', 'http://example.com/articles?page[offset]=2');
    $doc->setLink('last', 'http://example.com/articles?page[offset]=10');
    $doc->setInclude(['author', 'comments']);
    $json = json_encode($doc);
}
echo json_encode($doc, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);