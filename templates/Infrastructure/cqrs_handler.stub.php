<?php

namespace {{ namespace }};

use {{ modelClassPath }};
use {{ commandClassPath }};

final class {{ handlerName }}Handler
{
    public function __construct(
        private {{ entityName }} $eloquentModel
    ) {}

    public function handle({{ handlerName }}Command $command)
    {
        // TO DO
        // Some busines logic
        // $this->eloquentModel->create($command);
    }
}