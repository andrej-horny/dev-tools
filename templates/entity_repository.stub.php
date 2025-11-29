<?php

namespace {{ repositoryNamespace }};

use {{ entityNamespace }}\{{ entityName }};
use {{ entityNamespace }}\{{ entityName }}Id;
use {{ repositoryNamespace }}\{{ baseRepositoryInterface }};

interface {{ entityName }}Repository extends \{{ baseRepositoryInterface }}
{
    public function findById({{ entityName }}Id $id): ?{{ entityName }};
}
