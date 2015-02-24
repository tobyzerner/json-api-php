<?php namespace Tobscure\JsonApi;

class Document
{
    protected $links;

    protected $linked;

    protected $meta;

    protected $primaryElement;

    public function addLink($path, $href, $type = null)
    {
        $this->links[$path] = $type ? compact('href', 'type') : $href;

        return $this;
    }

    public function addLinked($type, $element)
    {
        $resources = $element->getResources();

        foreach ($resources as $k => $resource) {
            $this->extractLinks($resource);

            // If the resource doesn't have any attributes, then we don't need to
            // put it into the linked part of the document.
            if (! $resource->getAttributes()) {
                unset($resources[$k]);
            }
        }

        // Filter out any resources that we have already added to the document.
        $resources = $this->uniqueResources($type, $resources);

        if ($resources) {
            // If there are resources to be included (sideloaded), then extract
            // this element's URL templates. We do this here so that a resource
            // type's root URL templates, e.g. {"posts": "api/posts/{posts.id}"},
            // is not included if it doesn't need to be.
            $this->extractHref($element);

            if (! isset($this->linked[$type])) {
                $this->linked[$type] = [];
            }
            $this->linked[$type] = array_merge($this->linked[$type], $resources);
        }

        return $this;
    }

    protected function uniqueResources($type, $resources)
    {
        $ids = [];

        if (! empty($this->linked[$type])) {
            foreach ($this->linked[$type] as $resource) {
                $ids[] = $resource->getId();
            }
        }

        if ($type == $this->primaryElement->getType()) {
            foreach ($this->primaryElement->getResources() as $resource) {
                $ids[] = $resource->getId();
            }
        }

        $resources = array_filter($resources, function ($resource) use ($ids) {
            return ! in_array($resource->getId(), $ids);
        });

        return $resources;
    }


    public function setPrimaryElement($element)
    {
        $this->primaryElement = $element;

        if ($element) {
            foreach ($element->getResources() as $resource) {
                $this->extractHref($resource);
                $this->extractLinks($resource);
            }
        }

        return $this;
    }

    public function extractHref($resource)
    {
        foreach ($resource->getHref() as $type => $href) {
            if ($type == $resource->getType()) {
                $path = $type;
            } else {
                $path = $resource->getType().'.'.$type;
            }
            $this->addLink($path, $href, $type);
        }
    }

    public function extractLinks($resource)
    {
        foreach ($resource->getLinks() as $name => $element) {
            $linkType = $element->getType();
            $path = $resource->getType().'.'.$name;
            $href = $element->getHref()[$linkType];
            $href = str_replace('{'.$linkType.'.id}', '{'.$path.'}', $href);
            $this->addLink($path, $href, $linkType);
        }
        foreach ($resource->getIncludes() as $name => $element) {
            $this->addLinked($element->getType(), $element);
        }
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

        if (! empty($this->primaryElement)) {
            $document[$this->primaryElement->getType()] = $this->primaryElement->toArray();
        }

        if (! empty($this->linked)) {
            $document['linked'] = [];

            foreach ($this->linked as $type => $resources) {
                $resources = array_map(
                    function ($resource) {
                        return $resource->toArray();
                    },
                    $resources
                );
                $document['linked'][$type] = $resources;
            }
        }

        if (! empty($this->meta)) {
            $document['meta'] = $this->meta;
        }

        return $document;
    }
}
