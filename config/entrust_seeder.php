<?php

return [
    'role_structure' => [
        'admin' => [
            'users' => 'c,r,u,d',
            'admin' => 'c,r,u,d',
            'customer' => 'c,u,r,d'
        ],
        'super-agent' => [
            'users' => 'c,r,u',
            'agent' => 'r,c,u',
            'customer' => 'c,u,r'
        ],
        'agent' => [
            'customer' => 'c,u,r'
        ],
    ],
    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
    ],
];
