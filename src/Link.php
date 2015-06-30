<?php namespace Tobscure\JsonApi;

class Link
{
    protected $linkage;

    protected $self;

    protected $related;

    protected $meta;

    public function __construct($linkage)
    {
        $this->linkage = $linkage;
    }

    public function getLinkage()
    {
        return $this->linkage;
    }

    public function setLinkage($linkage)
    {
        $this->linkage = $linkage;

        return $this;
    }

    public function addMeta($key, $value)
    {
        $this->meta[$key] = $value;

        return $this;
    }

    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    public function setSelf($self)
    {
        $this->self = $self;

        return $this;
    }

    public function setRelated($related)
    {
        $this->related = $related;

        return $this;
    }

    public function toArray()
    {
        $link = [];

	    $link['linkage'] = [];
        if (! empty($this->linkage)) {
            $link['linkage'] = $this->linkage->toArray(false);
        }

        if (! empty($this->self)) {
            $link['self'] = $this->self;
        }

        if (! empty($this->related)) {
            $link['related'] = $this->related;
        }

        if (! empty($this->meta)) {
            $link['meta'] = $this->meta;
        }

        return $link;
    }
}
