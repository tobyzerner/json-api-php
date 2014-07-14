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

        if (! $resources) {
            return;
        }

        if (! isset($this->linked[$type])) {
            $this->linked[$type] = [];
        }
        $this->linked[$type] = array_merge($this->linked[$type], $resources);
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

        foreach ($element->getResources() as $resource) {
            $this->extractHref($resource);
            $this->extractLinks($resource);
        }
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
            $this->extractHref($element);

            $linkType = $element->getType();
            $path = $resource->getType().'.'.$name;
            $href = $element->getHref()[$linkType];
            $href = str_replace('{'.$linkType.'.id}', '{'.$path.'}', $href);
            $this->addLink($path, $href, $linkType);

            $this->addLinked($element->getType(), $element);
        }
    }

    public function addMeta($key, $value)
    {
        $this->meta[$key] = $value;
    }

    public function setMeta($meta)
    {
        $this->meta = $meta;
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
