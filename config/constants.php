<?php
return [
    'status' => [
        'open' => 1,
        'inProgress' => 2,
        'close' => 3,
        'approve' => 4,
        'request' => 5
    ],
    'statusCategory' => [
        'disable' => 0,
        'enable' => 1,
    ],
    'priorityRequest' => [
        'low' => 1,
        'medium' => 2,
        'high' => 3
    ],
    'GET_PRIORITY_REQUEST' => [
        1 => 'low',
        2 => 'medium',
        3 => 'high'
    ],
    'GET_STATUS_REQUEST' => [
        1 => 'open',
        2 => 'inProgress',
        3 => 'close',
        4 => 'approve'
    ],
    'GET_ROLE_ID' => [
        'Admin' => 1,
        'Manager' => 2,
        'User' => 3
    ],
    'GET_MINUTES_CHANGE_PASSWORD' => 60,
    'GET_DEPARTMENT_ID' => [
        'HCNS' => 1,
    ],
    'DEFAULT_TAIL_EMAIL' => '@hblab.vn',
    'LINK_GET_INFO_USER_GOOGLE' => env('LINK_GET_INFO_USER_GOOGLE'),
    'statusUser' => [
        'disable' => 0,
        'enable' => 1,
    ],
    'updateUser' => [
        'manager' => 1,
        'admin' => 2,
        'disable' => 3,
        'user' => 4,
    ],
    'user' => [
        'null' => 1,
    ],
    'statusDepartment' => [
        'disable' => 0,
        'enable' => 1,
    ],
    'STATUS_CACHE' => [
        'QUERY_DB' => 1,
        'GET_CACHE' => 2,
        'QUERY_NO_CACHE' => 3,
    ],
];
