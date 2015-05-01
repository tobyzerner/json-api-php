<?php namespace Tobscure\JsonApi;

class Document
{
    protected $links;

    protected $included = [];

    protected $meta;

    protected $data;

    public function addIncluded($link)
    {
        $resources = $link->getLinkage()->getResources();

        foreach ($resources as $k => $resource) {
            // If the resource doesn't have any attributes, then we don't need to
            // put it into the included part of the document.
            if (! $resource->getAttributes()) {
                unset($resources[$k]);
            }
        }

        // Filter out any resources that we have already added to the document.
        $resources = $this->uniqueResources($resources);

        foreach ($resources as $resource) {
            foreach ($resource->getIncluded() as $link) {
                $this->addIncluded($link);
            }
        }

        if ($resources) {
            $this->included = array_merge($this->included, $resources);
        }

        return $this;
    }

    protected function uniqueResources($resources)
    {
        $ids = [];

        foreach ($this->included as $resource) {
            $included[] = [$resource->getType(), $resource->getId()];
        }

        foreach ($this->data->getResources() as $resource) {
            $included[] = [$resource->getType(), $resource->getId()];
        }

        $resources = array_filter($resources, function ($resource) use ($included) {
            return ! in_array([$resource->getType(), $resource->getId()], $included);
        });

        return $resources;
    }


    public function setData($element)
    {
        $this->data = $element;

        if ($element) {
            foreach ($element->getResources() as $resource) {
                foreach ($resource->getIncluded() as $link) {
                    $this->addIncluded($link);
                }
            }
        }

        return $this;
    }

    public function addLink($key, $value)
    {
        $this->links[$key] = $value;

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

    public function toArray()
    {
        $document = [];

        if (! empty($this->links)) {
            ksort($this->links);
            $document['links'] = $this->links;
        }

        if (! empty($this->data)) {
            $document['data'] = $this->data->toArray();
        }

        if (! empty($this->included)) {
            $document['included'] = [];
            foreach ($this->included as $resource) {
                $document['included'][] = $resource->toArray();
            }
        }

        if (! empty($this->meta)) {
            $document['meta'] = $this->meta;
        }

        return $document;
    }

    public function __toString()
    {
        return json_encode($this->toArray());
    }
}
