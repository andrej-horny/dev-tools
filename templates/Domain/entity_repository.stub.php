<?php

namespace {{ repositoryNamespace }};

use {{ entityNamespace }}\{{ entityName }};
use {{ entityNamespace }}\{{ entityName }}Id;
use {{ baseRepositoryNamespace }}\{{ baseRepositoryInterface }};

interface {{ entityName }}RepositoryInterface extends {{ baseRepositoryInterface }}
{
    public function findById({{ entityName }}Id $id): ?{{ entityName }};
    public function all(): array;
    public function save({{ entityName }} $entity): ?{{ entityName }};
}
