<?php

namespace {{ namespace }};

use {{ modelClassPath }};
use {{ entityClassPath }};
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class {{ entityName }}Mapper
{
    public function __construct(
        private Eloquent{{ entityName }} $eloquentModel,
    ) {}

    public function toDomain(Eloquent{{ entityName }} $model): {{ entityName }}
    {
        return new {{ entityName }}(
            id: new {{ entityName }}Id($model->id),
            // uri: $model->uri,
            // title: $model->title,
        );
    }

    public function toEloquent({{ entityName }} $entity): Eloquent{{ entityName }}
    {
        $model = $this->eloquentModel->firstOrNew(['id' => $entity->id()]);
        // $model->uri = $entity->uri();
        // $model->title = $entity->title();
        return $model;
    }

    public function toDomainCollection(EloquentCollection $models): array
    {
        return $models
            ->map(
                fn($model) =>
                $this->toDomain($model)
            )
            ->all();
    }
}