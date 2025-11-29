<?php

namespace {{ serviceNamespace }};

use {{ entityNamespace }}\{{ entityName }};
use {{ repositoryNamespace }}\{{ entityName }}RepositoryInterface;

class {{ entityName }}Service
{
    public function __construct(
        private {{ entityName }}RepositoryInterface $repository,
    ) {}

    public function handle({{ entityName }} $entity): ?{{ entityName }}
    {
        return $this->repository->save($entity);
    }
}