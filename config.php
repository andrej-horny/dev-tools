<?php
return [
    // Default namespace -> folder mapping for generated classes
    'default_namespaces' => [
        'entity' => 'App\\Domain\\Entities',
        'value_object_id' => 'App\\Domain\\Entities',
        'value_object' => 'App\\Domain\\ValueObjects',
        'aggregate' => 'App\\Domain\\Aggregates',
        'domain_event' => 'App\\Domain\\Events',
        'entity_repository' => 'App\\Domain\\Repositories',
        'entity_service' => 'App\\Domain\\Services',
    ],

    // Default output directories (relative to project root)
    'default_paths' => [
        'entity' => 'src/Domain/Entities',
        'value_object_id' => 'src/Domain/Entities',
        'value_object' => 'src/Domain/ValueObjects',
        'aggregate' => 'src/Domain/Aggregates',
        'domain_event' => 'src/Domain/Events',
        'entity_repository' => 'src/Domain/Repositories',
        'entity_service' => 'src/Domain/Services',
    ],

    // Default template paths
    'templates' => [
        'entity' => __DIR__ . '/templates/Domain/entity.stub.php',
        'value_object_id' => __DIR__ . '/templates/Domain/value_object.stub.php',
        'value_object' => __DIR__ . '/templates/Domain/value_object.stub.php',
        'aggregate' => __DIR__ . '/templates/Domain/entity.stub.php',
        'domain_event' => __DIR__ . '/templates/Domain/entity.stub.php',
        'entity_repository' => __DIR__ . '/templates/Domain/entity_repository.stub.php',
        'entity_service' => __DIR__ . '/templates/Domain/entity_service.stub.php',
    ],
];
