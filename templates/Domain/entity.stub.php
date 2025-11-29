<?php

namespace {{namespace}};

class {{className}}
{
    public function __construct(
        private {{className}}Id $id,
{{constructorParams}}
    ) {}

    public function id(): {{className}}Id
    {
        return $this->id;
    }
{{accessors}}
}

