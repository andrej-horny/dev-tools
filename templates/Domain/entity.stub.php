<?php

namespace {{ namespace }};

final class {{ entityName }}
{
    public function __construct(
        private {{ entityName }}Id $id,
{{ constructorParams }}
    ) {}

    public function id(): {{ entityName }}Id
    {
        return $this->id;
    }
{{ accessors }}
}

