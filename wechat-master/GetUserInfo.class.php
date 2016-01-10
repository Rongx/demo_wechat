<?php
    /**
     * 管理用户信息
     *
     * Created by PhpStorm.
     * User: buyingfei
     * Date: 2015/9/29
     * Time: 10:01
     *
     */
    namespace Wechat;


    class GetUserInfo extends WechatBase
    {
        public function  __construct(){

        }

        /**
         *获取回调uri
         *
         * @param string $appid
         * @param string $redirect_uri 获得openid 后需要进行重定向的url
         * @param bool|TRUE $simple
         */
        public function getRedirUri(string $appid,string $redirect_uri,$simple = true){
            $redirect_url = urlencode($redirect_uri);
            if($simple){
                // 直接获取转跳地址,把转跳地址放到需要的a 标签即可
                $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=".
                    "$redirect_url&response_type=code&scope=snsapi_base&state=123456#wechat_redirect";
                echo $url;
            }else{ //通过另外一种方式获取用户比较全面信息，此处需要经过用户确认
                $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=".
                    $redirect_url."&response_type=code&scope=snsapi_userinfo&state=1234646#wechat_redirect";
                echo $url;
            }
        }

        /**
         *  进行用户授权 获得唯一openId
         *
         * @param string $appid 微信配置信息appid
         * @param string $appsecret 微信配置信息
         */
        public function getOpenId(string $appid,string $appsecret){
            $code = $_GET["code"];
            //oauth2的方式获得openid
            $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code";
            $access_token_json = $this->https_request($access_token_url);
            $access_token_array = json_decode($access_token_json, true);
            $this->print_ori($access_token_array);
            $openid = $access_token_array['openid'];
            return $openid;
        }

    }