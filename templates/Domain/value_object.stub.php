<?php

namespace {{namespace}};

class {{className}}
{
    public function __construct(
{{constructorParams}}
    ) {}
{{accessors}}
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}