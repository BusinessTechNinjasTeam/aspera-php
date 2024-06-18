<?php

namespace AsperaPHP\API;

class Collection
{
    public array $items;

    public function __construct(
        array $items
    ) {
        $this->items = $items;
    }

    public function first()
    {
        return reset($this->items);
    }

    public function last()
    {
        return end($this->items);
    }

    public function as($class): Collection
    {
        return new Collection(array_map(function($item) use ($class) {
            return call_user_func([$class, 'fromObject'], $item);
        }, $this->items));
    }
}
