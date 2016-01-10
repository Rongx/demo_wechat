<?php
/*
本文件位置
$redirect_url= "http://byfweixin.sinaapp.com/oauth.php";
*/
//获得code
$code = $_GET["code"];
$userinfo = getUserInfo($code);

print_r($userinfo);
function getUserInfo($code)
{
	$appid = "wxfa12bc19b4ac3d1d";
	$appsecret = "d0a07fefb8d531eb8fa74dae9f6dafdf";

    //oauth2的方式获得openid
	$access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code";
	$access_token_json = https_request($access_token_url);
	$access_token_array = json_decode($access_token_json, true);
	$openid = $access_token_array['openid'];

        //通过接口获得access_token
        $new_access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
        $new_access_token_json = https_request($new_access_token_url);
        $new_access_token_array = json_decode($new_access_token_json, true);
        $new_access_token = $new_access_token_array['access_token'];
    
        //全局access token获得用户基本信息
        $userinfo_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$new_access_token&openid=$openid";
	$userinfo_json = https_request($userinfo_url);
	$userinfo_array = json_decode($userinfo_json, true);
	return $userinfo_array;
}

function https_request($url)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($curl);
	if (curl_errno($curl)) {return 'ERROR '.curl_error($curl);}
	curl_close($curl);
	return $data;
}
?>
