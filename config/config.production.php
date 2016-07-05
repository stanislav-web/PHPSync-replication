<?php
/**
 * Production stage configuration
 */

$config = [
    'connect'   =>  [
        'dsn'   => 'mysql:dbname=****;host=****',
        'username'      => '',
        'password'      => '',
        'charset'       => 'utf8',
        'debug'         => 0,
        'persistent'    => false
    ],
    'delay' => 60
];