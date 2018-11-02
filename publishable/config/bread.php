<?php

//https://christoph-schweppe.gitbook.io/voyager-bread/getting-started/configurations
return [
    'bread_path'    => storage_path('bread'), //Without trailing slash
    'bread_buttons' => [
        'save_edit'     => true,
        'save_new'      => true,
    ],
    'breakpoints'   => [
        'lg'            => 1200,
        'md'            => 996,
        'sm'            => 768,
        'xs'            => 480,
        'xxs'           => 0,
    ],
];
