<?php

namespace {{ namespace }};

use {{ entityClassPath }};
use {{ repositoryInterfacePath }};
use {{ mapperClassPath }};
use {{ modelClassPath }};

class {{ entityName }}RepositoryEloquent implements {{ entityName }}RepositoryInterface
{
    public function __construct(
        private {{ entityName }}Mapper $mapper,
        private Eloquent{{ entityName }} $eloquentModel
    ) {}

    public function save({{ entityName }} $entity): {{ entityName }}
    {
        $model = $this->mapper->toEloquent($entity);
        $model->save();
        return $this->mapper->toDomain($model);
    }

    public function findById({{ entityName }}Id $id): ?{{ entityName }}
    {
        $model = $this->eloquentModel->findOrFail($id);

        return $model ? $this->mapper->toDomain($model) : null;
    }

    public function all(): array
    {
        return $this->eloquentModel->all()
            ->map(fn($m) => $this->mapper->toDomain($m))
            ->toArray();
    }

}