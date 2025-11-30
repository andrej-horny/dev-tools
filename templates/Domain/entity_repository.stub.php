<?php

namespace {{ namespace }};

use {{ entityClassPath }};
use {{ entityClassPath }}Id;

interface {{ entityName }}RepositoryInterface
{
    public function findById({{ entityName }}Id $id): ?{{ entityName }};
    public function all(): array;
    public function save({{ entityName }} $entity): ?{{ entityName }};
}
