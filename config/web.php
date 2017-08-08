<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'PL6_DWqur8HwRC4HaB1rkK-0RkONkdcN',
	        'parsers' => [
		        'application/json' => 'yii\web\JsonParser',
	        ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
	    'urlManager' => [
		    'enablePrettyUrl' => true,
		    'enableStrictParsing' => true,
		    'showScriptName' => false,
		    'rules' => [ // 全部用单数
			    ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/article', 'pluralize' => false],
			    ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/user', 'pluralize' => false],
			    ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/file', 'pluralize' => false],
			    ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/statistic', 'pluralize' => false],
			    ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/auth', 'pluralize' => false],
			    'OPTIONS,GET v1/qiniu/token' => 'v1/qiniu/token',
			    'OPTIONS,GET v1/user/fetch' => 'v1/user/fetch',
		    ],
	    ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
