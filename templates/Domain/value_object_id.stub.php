<?php

namespace {{ namespace }};

final readonly class {{ entityName }}Id
{
    public function __construct(
        private string $value
    ) {}

    public function value(string $value): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}