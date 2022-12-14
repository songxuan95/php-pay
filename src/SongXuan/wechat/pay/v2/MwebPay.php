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
    
    /**
     * @param  array [out_trade_no]      订单号
     * @param  array [spbill_create_ip]  客户端ip
     * @param  array [total_fee]         支付金额 单位 分
     * @param  array [notify_url]        回调地址
     * @param  array [body]              描述
     * @param  array [attach]            附加参数，回调处理
     * @return array
     */
    public function pay($params=[]){

        $data = [
            'appid'            =>$this->appid,
            'mch_id'           =>$this->mch_id,
            'nonce_str'        =>$this->nonceStr(),
            'out_trade_no'     =>$params['out_trade_no'],
            'spbill_create_ip' =>$params['spbill_create_ip'],
            'total_fee'        =>intval($params['total_fee']),
            'notify_url'       =>$params['notify_url'],
            'trade_type'       =>'MWEB',
            'sign_type'        =>'HMAC-SHA256',
            'body'             =>$params['body'],
        ];

        if(isset($params['attach'])){
            $data['attach'] = $params['attach'];
        }

        $url='https://api.mch.weixin.qq.com/pay/unifiedorder';
        return $this->buildData($data,$url,false);
    }
}