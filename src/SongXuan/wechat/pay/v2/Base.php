<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2022/8/11
 * Time: 15:02
 */

namespace SongXuan\wechat\pay\v2;


class Base{

    /**
     * 订单查询
     * @param  array [out_trade_no]   订单号
     * @return array
     */
    public function order_query($params=[]){

        $data = [
            'appid'            =>$this->appid,
            'mch_id'           =>$this->mch_id,
            'out_trade_no'     =>$params['out_trade_no'],
            'nonce_str'        =>$this->nonceStr(),
            'sign_type'        =>'HMAC-SHA256',
        ];

        $url='https://api.mch.weixin.qq.com/pay/orderquery';
        return $this->buildData($data,$url,false);

    }

    /**
     * 退款
     * @param  array [out_trade_no]    支付订单号
     * @param  array [out_refund_no]   退款单号
     * @param  array [total_fee]       支付金额 分
     * @param  array [notify_url]      退款回调通知
     * @param  array [refund_desc]     退款原因
     * @return array
     */
    public function refund($params=[]){

        $data = [
            'appid'            =>$this->appid,
            'mch_id'           =>$this->mch_id,
            'out_trade_no'     =>$params['out_trade_no'],
            'out_refund_no'    =>$params['out_refund_no'],
            'nonce_str'        =>$this->nonceStr(),
            'sign_type'        =>'HMAC-SHA256',
            'total_fee'        =>intval($params['total_fee']),
            'refund_fee'       =>intval($params['total_fee']),
        ];

        if(isset($params['refund_desc'])){
            $data['refund_desc'] = $params['refund_desc'];
        }
        if(isset($params['notify_url'])){
            $data['notify_url'] = $params['notify_url'];
        }
        $url='https://api.mch.weixin.qq.com/secapi/pay/refund';
        return $this->buildData($data,$url,true);
    }

    /**
     * 退款查询
     * @param  array [out_trade_no]   订单号
     * @return array
     */
    public function refund_query($params=[]){

        $data = [
            'appid'            =>$this->appid,
            'mch_id'           =>$this->mch_id,
            'out_trade_no'     =>$params['out_trade_no'],
            'nonce_str'        =>$this->nonceStr(),
            'sign_type'        =>'HMAC-SHA256',
        ];

        $url='https://api.mch.weixin.qq.com/pay/refundquery';
        return $this->buildData($data,$url,false);

    }

    /**
     * 数据构建，请求接口
     * $data  接口参数
     * $url   接口地址
     * $pem   是否需要证书
     */
    public function buildData($data,$url,$pem){
        //获取签名
        $data['sign'] = $this->getSign($data);
        //转 xml 格式
        $xml = $this->arrayToXml($data);
        $result = $this->sendRequest($url,$xml,$pem);
        // xml 转 数组
        $arr = $this->xmlToArray($result);
        return $arr;
    }

    /***
     * $url  微信接口地址
     * $xml  xml 格式数据
     * $cert 是否需要证书 默认不需要
     */

    public function sendRequest($url,$xml,$cert = false){
        //定义content-type为xml
        $header[] = "Content-type: text/xml";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //设置是否返回信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //设置HTTP头
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //设置证书
        if($cert == true){
            curl_setopt($ch,CURLOPT_SSLCERT, $this->mch_apiclient_cert);
            curl_setopt($ch,CURLOPT_SSLKEY, $this->mch_apiclient_key);
        }

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    /**
     * 获取签名
     * 加密方式默认使用  sha256
     */
    public function getSign($data){

        ksort($data);
        $str    = $this->ToUrlParams($data);
        $key    = $this->mch_secret_cert;
        $string = $str."&key=".$key;
        $sign   = hash_hmac('sha256', $string, $key);
        return strtoupper($sign);
    }


    /***
     * 随机32位字符串
     */
    public function nonceStr(){
        $result = '';
        $str    = 'QWERTYUIOPASDFGHJKLZXVBNMqwertyuioplkjhgfdsamnbvcxz';
        for ($i = 0;$i < 32;$i++){
            $result .= $str[rand(0,48)];
        }
        return $result;
    }

    /**
     * xml转数组
     */
    public function xmlToArray($xml){
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr;
    }

    /**
     * 格式化参数格式化成url参数
     */
    public function ToUrlParams($build){
        $buff = "";
        foreach ($build as $k => $v) {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 数组转xml
     */
    public function arrayToXml($arr){
        $xml = "<xml>";
        foreach ($arr as $key=>$val) {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
}