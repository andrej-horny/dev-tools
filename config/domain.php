<?php
return [
    // Default namespace -> folder mapping for generated classes
    'default_namespaces' => [
        'entity' => 'Domain\\Entities',
        'value_object_id' => 'Domain\\Entities',
        'value_object' => 'Domain\\ValueObjects',
        'aggregate' => 'Domain\\Aggregates',
        'domain_event' => 'Domain\\Events',
        'entity_repository' => 'Domain\\Repositories',
        'entity_service' => 'Domain\\Services',
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
        'entity' => __DIR__ . '/../templates/Domain/entity.stub.php',
        'value_object_id' => __DIR__ . '/../templates/Domain/value_object.stub.php',
        'value_object' => __DIR__ . '/../templates/Domain/value_object.stub.php',
        'aggregate' => __DIR__ . '/../templates/Domain/entity.stub.php',
        'domain_event' => __DIR__ . '/../templates/Domain/entity.stub.php',
        'entity_repository' => __DIR__ . '/../templates/Domain/entity_repository.stub.php',
        'entity_service' => __DIR__ . '/../templates/Domain/entity_service.stub.php',
    ],
];
