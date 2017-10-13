##### 简介
- blog项目包含3部分：blog-api,blog-admin,blog-weiapp
- blog-api(本项目)提供接口，blog-admin负责数据录入，blog-weiapp负责数据展示

##### 相关项目
- 微信小程序：https://github.com/rxlisbest/blog-weiapp.git
- 后台：https://github.com/rxlisbest/blog-admin.git

##### 相关技术
- yii
- 七牛云储存，文档https://developer.qiniu.com

##### 安装步骤
- git clone https://github.com/rxlisbest/blog-api.git
- 配置config/db.php文件
```
    <?php
        return [  
            'class' => 'yii\db\Connection',  
            'dsn' => 'mysql:host=[数据库地址];dbname=[数据库名]',  
            'username' => '[用户名]',  
            'password' => '[密码]',  
            'charset' => 'utf8',  
        ];
```
- 在目录config/中新建params.php文件，并将[]内的替换成自己的
```
<?php
    return [
        'adminEmail' => 'admin@example.com',
        'qiniu' => [
            'accessKey' => '[七牛账号的AK]',
            'secretKey' => '[七牛账号的SK]',
            'bucket' => '[新建的bucket名]',
            'persistentOps' => 'avthumb/mp4',
            'transcodeType' => 'video/mp4',
            'persistentNotifyUrl' => '',
            'persistentPipeline' => '[新建的pipe名]',
            'domain' => '[绑定的CDN域名]',
        ],
        'wechat' => [
            'AppID' => '[微信公众号AppID]',
            'AppSecret' => '[微信公众号AppSecret]',
        ],
        'aliyun' => [
            'sms' => [
                'AppCode' => '[阿里云市场短信AppCode]',
                'SignName' => '我要做网站',
                'TemplateCode' => [
                    'register' => ['code'=>'[阿里云市场短信code]', 'daily_limit' => 5]
                ],
                'time_interval' => 120,
                'daily_limit' => 100,
                'expire' => 900
            ]
        ],
        'sensitive_words' => ['敏感词']
    ];
```
- 根目录执行composer install
- 根目录执行./yii migrate
- 根目录执行./yii migrate --migrationPath=@conquer/oauth2/migrations
- 数据库中执行脚本，登录账号/密码：admin/admin
```
	insert into `oauth2_client` ( `client_id`, `client_secret`, `redirect_uri`, `grant_type`, `scope`, `created_at`, `updated_at`, `created_by`, `updated_by`) values ( 'blog', '1', '1', '1', '1', '1', '1', '1', '1');
	insert into `oauth2_user` ( `username`, `cellphone`, `password`, `salt`, `roles`, `scope`, `client_id`) values ( 'admin', null, 'a66abb5684c45962d887564f08346e8d', '123456', null, null, 'blog');
```
