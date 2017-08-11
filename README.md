##### 相关
- yii
- 七牛云储存，文档https://developer.qiniu.com

##### 安装步骤
- git clone https://github.com/rxlisbest/blog-api.git
- 配置config/db.php文件
		<?php
        return [  
            'class' => 'yii\db\Connection',  
            'dsn' => 'mysql:host=[数据库地址];dbname=[数据库名]',  
            'username' => '[用户名]',  
            'password' => '[密码]',  
            'charset' => 'utf8',  
        ];  
- 在目录config/中新建params.php文件
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
	    	]  
		];  
- 根目录执行composer install
- 根目录执行./yii migrate
- 根目录执行./yii migrate --migrationPath=@conquer/oauth2/migrations
