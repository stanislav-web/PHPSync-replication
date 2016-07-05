<?php
/**
 * Development stage configuration
 */

$config = [
    'connect'   =>  [
        'dsn'   => 'mysql:dbname=****;host=****',
        'username'      => '',
        'password'      => '',
        'charset'       => 'utf8',
        'debug'         => 2,
        'persistent'    => false
    ],

    'binlogPath' => TMP_ROOT.'/bru1-relay-master-log-file-info.log',
    'binlogInterval' => 12,

    'entities' => [
        'all'  =>  [
            'delay' => 60,
            'logger'   => [
                'file'      => DOCUMENT_ROOT.'/logs/all.log',
                'format'    => '[date][level] message',
                'date'      => 'Y-m-d H:i:s',
            ]
        ],
        'shop'  =>  [
            'delay' => 60,
            'logger'   => [
                'file'      => DOCUMENT_ROOT.'/logs/shop.log',
                'format'    => '[date][level] message',
                'date'      => 'Y-m-d H:i:s',
            ]
        ],
        'banner'  =>  [
	        'delay' => 60,
            'logger'   => [
                'file'      => DOCUMENT_ROOT.'/logs/banner.log',
                'format'    => '[date][level] message',
                'date'      => 'Y-m-d H:i:s',
            ]
        ],
        'brand'  =>  [
	        'delay' => 60,
            'logger'   => [
                'file'      => DOCUMENT_ROOT.'/logs/brand.log',
                'format'    => '[date][level] message',
                'date'      => 'Y-m-d H:i:s',
            ]
        ],
        'category'  =>  [
            'delay' => 60,
            'logger'   => [
                'file'      => DOCUMENT_ROOT.'/logs/category.log',
                'format'    => '[date][level] message',
                'date'      => 'Y-m-d H:i:s',
            ]
        ],
        'tag'  =>  [
            'delay' => 60,
            'logger'   => [
                'file'      => DOCUMENT_ROOT.'/logs/tag.log',
                'format'    => '[date][level] message',
                'date'      => 'Y-m-d H:i:s',
            ]
        ],
        'product'  =>  [
            'delay' => 60,
            'logger'   => [
                'file'      => DOCUMENT_ROOT.'/logs/product.log',
                'format'    => '[date][level] message',
                'date'      => 'Y-m-d H:i:s',
            ]
        ],
        'price'  =>  [
            'delay' => 60,
            'logger'   => [
                'file'      => DOCUMENT_ROOT.'/logs/price.log',
                'format'    => '[date][level] message',
                'date'      => 'Y-m-d H:i:s',
            ]
        ],
        'buy'  =>  [
            'delay' => 60,
            'logger'   => [
                'file'      => DOCUMENT_ROOT.'/logs/buy.log',
                'format'    => '[date][level] message',
                'date'      => 'Y-m-d H:i:s',
            ]
        ],
        'buytogether'  =>  [
            'delay' => 60,
            'logger'   => [
                'file'      => DOCUMENT_ROOT.'/logs/buytogether.log',
                'format'    => '[date][level] message',
                'date'      => 'Y-m-d H:i:s',
            ]
        ],
        'region'  =>  [
            'delay' => 60,
            'logger'   => [
                'file'      => DOCUMENT_ROOT.'/logs/region.log',
                'format'    => '[date][level] message',
                'date'      => 'Y-m-d H:i:s',
            ]
        ],
        'payment'  =>  [
            'delay' => 60,
            'logger'   => [
                'file'      => DOCUMENT_ROOT.'/logs/payment.log',
                'format'    => '[date][level] message',
                'date'      => 'Y-m-d H:i:s',
            ]
        ],
        'delivery'  =>  [
            'delay' => 60,
            'logger'   => [
                'file'      => DOCUMENT_ROOT.'/logs/delivery.log',
                'format'    => '[date][level] message',
                'date'      => 'Y-m-d H:i:s',
            ]
        ],
        'document'  =>  [
            'delay' => 60,
            'logger'   => [
                'file'      => DOCUMENT_ROOT.'/logs/document.log',
                'format'    => '[date][level] message',
                'date'      => 'Y-m-d H:i:s',
            ]
        ],
        'hotline'  =>  [
	        'delay' => 60,
	        'sync'  =>  [
		        'url'      =>  'http://back95.ru/****/',
		        'method'    =>  '****',
		        'token'     =>  '****',
                'entities'  => [
                    'hotline' => 1,
                    'hotlineItems' => 1,
                ],
	        ],
	        'logger'   => [
		        'file'      => DOCUMENT_ROOT.'/logs/hotline.log',
		        'format'    => '[date][level] message',
		        'date'      => 'Y-m-d H:i:s',
	        ]
        ],
        'statistics'  =>  [
	        'delay' => 60,
	        'logger'   => [
		        'file'      => DOCUMENT_ROOT.'/logs/statistics.log',
		        'format'    => '[date][level] message',
		        'date'      => 'Y-m-d H:i:s',
	        ]
        ],
    ],

    'notification' =>[
	    'recipients' => 'lilla.my.1070@gmail.com,stanisov@gmail.com',
	    'mail'  =>  [
		    'url'      =>  'http://back95.ru/****/',
		    'method'    =>  '****',
		    'token'     =>  '****',
	    ]
    ],

];