<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2022/8/11
 * Time: 15:14
 */

namespace SongXuan\wechat\pay\v2;

/**
 * h5支付
 */
class MwebPay extends Base {
    public  $appid;
    //商户号
    public $mch_id;
    //商户秘钥 api秘钥
    public $mch_secret_cert;
    //商户私钥的绝对路径
    public $mch_secret_cert_path;
    //商户公钥的绝对路径
    public $mch_public_cert_path;

    public  function __construct($config=[]){

        $this->appid                     = $config['appid'];
        $this->mch_id                    = $config['mch_id'];
        $this->mch_secret_cert           = $config['mch_secret_cert'];
        $this->mch_secret_cert_path      = $config['mch_secret_cert_path'];
        $this->mch_public_cert_path      = $config['mch_public_cert_path'];

    }

    /**
     * @param  array [out_trade_no]   订单号
     * @param  array [ip]             客户端ip
     * @param  array [money]          支付金额 单位 分
     * @param  array [notify_url]     回调地址
     * @param  array [body]           描述
     * @param  array [attach]         附加参数，回调处理
     * @return array
     */
    public function pay($params=[]){

        $data = [
            'appid'            =>$this->appid,
            'body'             =>$params['body'],
            'mch_id'           =>$this->mch_id,
            'nonce_str'        =>$this->nonceStr(),
            'notify_url'       =>$params['notify_url'],
            'out_trade_no'     =>$params['out_trade_no'],
            'spbill_create_ip' =>$params['ip'],
            'total_fee'        =>intval($params['money']),
            'trade_type'       =>'MWEB',
            'sign_type'        =>'HMAC-SHA256',
            //额外参数
            'attach'           =>$params['attach'],
        ];

        //获取签名
        $data['sign'] = $this->getSign($data);
        //转 xml 格式
        $xml = $this->arrayToXml($data);
        $url='https://api.mch.weixin.qq.com/pay/unifiedorder';
        $result = $this->sendRequest($url,$xml,true);
        // xml 转 数组
        $arr = $this->xmlToArray($result);
        return $arr;
    }
}