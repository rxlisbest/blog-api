<?php

namespace app\controllers\v1;

use Yii;
use linslin\yii2\curl;
use app\models\SmsAliyun;
use yii\web\HttpException;
use yii\captcha\CaptchaAction;

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;

class SmsAliyunController extends BaseController
{
    public $modelClass = 'app\models\SmsAliyun';

    public function actions(){
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update']);
        return $actions;
    }

    static $acsClient = null;

    /**
     * 取得AcsClient
     *
     * @return DefaultAcsClient
     */
    public static function getAcsClient() {
        $sms_config = Yii::$app->params['aliyun']['sms'];
        //产品名称:云通信流量服务API产品,开发者无需替换
        $product = "Dysmsapi";

        //产品域名,开发者无需替换
        $domain = "dysmsapi.aliyuncs.com";

        // TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
        $accessKeyId = $sms_config['accessKeyId']; // AccessKeyId

        $accessKeySecret = $sms_config['accessKeySecret']; // AccessKeySecret

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";


        if(static::$acsClient == null) {

            //初始化acsClient,暂不支持region化
            $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

            // 增加服务结点
            DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

            // 初始化AcsClient用于发起请求
            static::$acsClient = new DefaultAcsClient($profile);
        }
        return static::$acsClient;
    }

    public function actionSend(){
        $get = Yii::$app->request->get();
        //		// 图形验证码
        //		if(!isset($get['captcha']) || !trim($get['captcha'])){
        //			throw new HttpException(400, "验证码不能为空");
        //		}
        //		$captcha = new CaptchaAction(1, $this);
        //		$result = $captcha->validate($get['captcha'], true);
        //		if(!$result){
        //			throw new HttpException(400, "验证码不正确");
        //		}

        if(!isset($get['TemplateCode']) || !isset($get['RecNum'])){
            throw new HttpException(400, "参数错误");
        }

        $sms_config = Yii::$app->params['aliyun']['sms'];
        if(!isset($sms_config['TemplateCode'][$get['TemplateCode']])){
            throw new HttpException(400, "参数错误");
        }

        if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $get['RecNum'])){
            throw new HttpException(400, "手机号格式不正确");
        }

        $template_code = $sms_config['TemplateCode'][$get['TemplateCode']];
        $query = SmsAliyun::find()->where(['>', 'create_time', strtotime(date('Y-m-d'))])->andWhere(['<', 'create_time', time()]);
        // 同一IP每天条数限制
        $x = clone $query;
        $where = ['ip' => Yii::$app->request->userIP];
        $send_count = $x->andWhere($where)->count();
        if($send_count >= 3*$template_code['daily_limit']){
            throw new HttpException(500, "同一IP超出每天限制数量");
        }
        // 同一模板每天条数限制
        $y = clone $query;
        $where = ['RecNum' => $get['RecNum']];
        $send_count = $y->andWhere($where)->andWhere(['TemplateCode' => $template_code['code']])->count();
        if($send_count >= $template_code['daily_limit']){
            throw new HttpException(500, "超出每天限制数量");
        }

        // 每天条数总数限制
        $z = clone $query;
        $send_count = $z->count();
        if($send_count >= $sms_config['daily_limit']){
            throw new HttpException(500, "超出每天限制总数量");
        }

        // 上次发短信时间间隔
        $where = ['RecNum' => $get['RecNum']];
        $last_sms = SmsAliyun::find()->where($where)->andWhere(['TemplateCode' => $template_code['code']])->orderBy('create_time DESC')->one();
        if($last_sms && time() - $last_sms->attributes['create_time'] < $sms_config['time_interval']){
            throw new HttpException(500, '发送时间间隔不能短于' .$sms_config['time_interval'].'s');
        }

        $param_string = $this->createParamString($get['TemplateCode']);
        $sms_aliyun = new SmsAliyun();
        $sms_aliyun->ParamString = $param_string;
        $sms_aliyun->RecNum = $get['RecNum'];
        $sms_aliyun->TemplateCode = $template_code['code'];
        $sms_aliyun->ip = Yii::$app->request->userIP;
        $sms_aliyun->create_time = time();
        $sms_aliyun->update_time = time();
        $result = $sms_aliyun->save();
        if($result){
            //			$curl = new curl\Curl();
            //			$response = $curl->setGetParams([
            //				'ParamString' => $param_string,
            //				'RecNum' => $get['RecNum'],
            //				'SignName' => $sms_config['SignName'],
            //				'TemplateCode' => $template_code['code']
            //			])->setHeaders([
            //				'Authorization' => 'APPCODE ' . $sms_config['AppCode'],
            //			])->get('http://sms.market.alicloudapi.com/singleSendSms');
            //			$result = json_decode($response);

            Config::load();
            // 初始化SendSmsRequest实例用于设置发送短信的参数
            $request = new SendSmsRequest();

            // 必填，设置短信接收号码
            $request->setPhoneNumbers($get['RecNum']);

            // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
            $request->setSignName($sms_config['SignName']);

            // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
            $request->setTemplateCode($template_code['code']);

            // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
            $request->setTemplateParam(json_encode(array(  // 短信模板中字段的值
                "code" => sprintf('%06d', rand(0, 999999))
            ), JSON_UNESCAPED_UNICODE));

            // 可选，设置流水号
            $request->setOutId("yourOutId");

            // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
            $request->setSmsUpExtendCode("1234567");

            // 发起访问请求
            $acsResponse = static::getAcsClient()->getAcsResponse($request);
            return $acsResponse;
        }
        else{
            throw new HttpException(500, "操作失败");
        }
    }

    private function createParamString($TemplateCode){
        $param = [];
        switch ($TemplateCode){
            case 'register':
                $param['code'] = sprintf('%06d', rand(0, 999999));
                break;
            default:
                // do nothing
        }
        return json_encode($param);
    }

    public function actionCaptcha($random){
        // 生成验证码
        $captcha = new CaptchaAction(1, $this);
        $captcha->minLength = 4;
        $captcha->maxLength = 4;
        $captcha->width = 80;
        $captcha->height = 45;
        $captcha->getVerifyCode(true);
        @ header("Content-Type:image/png"); // 创建一个图层
        echo $captcha->run();exit;
    }
}
