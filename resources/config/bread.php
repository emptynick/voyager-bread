<?php

return [
    'bread_path'    => storage_path('bread'),

    'views' => [
        // Breakpoints
        'breakpoints'       => [
            'xl'    => [
                'name'    => 'Extra large',
                'width'   => 1200,
                'columns' => 12,
            ],
            'lg'    => [
                'name'    => 'Large',
                'width'   => 992,
                'columns' => 10,
            ],
            'md'    => [
                'name'    => 'Medium',
                'width'   => 768,
                'columns' => 8,
            ],
            'sm'    => [
                'name'    => 'Small',
                'width'   => 576,
                'columns' => 4,
            ],
            'xs'    => [
                'name'    => 'Extra small',
                'width'   => 0,
                'columns' => 2,
            ],
        ]
    ],
];
