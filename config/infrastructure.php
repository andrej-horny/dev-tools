<?php
return [
    // Default namespace -> folder mapping for generated classes
    'default_namespaces' => [
        'mapper' => 'Infrastructure\\Persistence\\Eloquent\\Mappings',
        'model' => 'Infrastructure\\Persistence\\Eloquent\\Models',
        'repository' => 'Infrastructure\\Persistence\\Eloquent\\Repositories',
        // External classes
        'ulid_cast' => 'Dpb\\Utils\\Infrastructure\\Casts\\UlidBinary',
        'ulid_trait' => 'Dpb\\Utils\\Infrastructure\\Traits\\HasBinaryUlid',
    ],

    // Default output directories (relative to project root)
    'default_paths' => [
        'mapper' => 'src/Infrastructure/Persistence/Eloquent/Mappings',
        'model' => 'src/Infrastructure/Persistence/Eloquent/Models',
        'repository' => 'src/Infrastructure/Persistence/Eloquent/Repositories',
    ],

    // Default template paths
    'templates' => [
        'mapper' => __DIR__ . '/../templates/Infrastructure/mapper.stub.php',
        'model' => __DIR__ . '/../templates/Infrastructure/model.stub.php',
        'repository' => __DIR__ . '/../templates/Infrastructure/repository.stub.php',
    ],
];