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
		    'rules' => [
			    ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/article'],
			    ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/discussion'],
			    ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/user'],
			    ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/file'],
			    ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/statistic'],
			    ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/auth', 'pluralize' => false],
			    // 文章分类
			    'OPTIONS,GET v1/article-categories' => 'v1/article-category',
			    'OPTIONS,POST v1/article-categories' => 'v1/article-category/create',
			    'OPTIONS,GET v1/article-categories/<id>' => 'v1/article-category',
			    'OPTIONS,PUT v1/article-categories/<id>' => 'v1/article-category/update',
			    'OPTIONS,DELETE v1/article-categories/<id>' => 'v1/article-category/delete',
			    // 微信用户
			    'OPTIONS,POST v1/user-wechat/login' => 'v1/user-wechat/login',
			    'OPTIONS,POST v1/user-wechat/register' => 'v1/user-wechat/register',
			    // 阿里云短信
			    'OPTIONS,GET v1/sms-aliyun/send' => 'v1/sms-aliyun/send',
			    'OPTIONS,GET v1/sms-aliyun/captcha/<random>' => 'v1/sms-aliyun/captcha',
			    // 七牛
			    'OPTIONS,GET v1/qiniu/token' => 'v1/qiniu/token',
			    // 用户
			    'OPTIONS,GET v1/users/fetch' => 'v1/user/fetch',
                // 讨论
                'OPTIONS,GET v1/discussion/random' => 'v1/discussion/random',
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
