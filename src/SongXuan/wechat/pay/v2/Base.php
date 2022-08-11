<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2022/8/11
 * Time: 15:02
 */

namespace SongXuan\wechat\pay\v2;


class Base{
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
            curl_setopt($ch,CURLOPT_SSLCERT, $this->cert_path);
            curl_setopt($ch,CURLOPT_SSLKEY, $this->key_path);
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
    public function getSign($data,$type = 'sha256'){

        ksort($data);
        $str = $this->ToUrlParams($data);
        $key = $this->wx_key;
        $string = $str."&key=".$key;

        //加密方式
        if($type == 'sha256'){
            $sign = hash_hmac('sha256', $string, $key);
        }else{
            $sign = md5($string);
        }
        return strtoupper($sign);
    }


    /***
     * 随机32位字符串
     */
    public function nonceStr(){
        $result = '';
        $str = 'QWERTYUIOPASDFGHJKLZXVBNMqwertyuioplkjhgfdsamnbvcxz';
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