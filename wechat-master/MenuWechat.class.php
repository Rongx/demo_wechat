<?php
namespace Wechat;
//进行菜单管理 最主要是回的access_token
class MenuWechat extends WechatBase{
    private $access_token = null;
    public function __construct($config) {
        //parent::__construct();
        $this->access_token = $this->get_access_token($config);
    }
        //进行创建菜单
    public function create_menu(){
           $url = " https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$this->access_token";
           $result = $this->https_request($url,$this->menu_list());
           var_dump($result);
   }
   //删除菜单
   public function delete_menu(){
	 $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=$this->access_token";
	 $result = $this->https_request($url);
	 var_dump($result);
   }
   //查询菜单接口
   public function select_menu(){
		$url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=$this->access_token";
		$result = $this->https_request($url);
		print_r($result);
   }
   //菜单列表
   public function menu_list(){
       $list = '
{
    "button": [
        {
            "name": "我的账户",
            "sub_button": [
                {
                    "type": "click",
                    "name": "绑定账户",
                    "key": "V1001_TODAY_MUSIC4"
                },
                {
                    "type": "click",
                    "name": "查看账户",
                    "key": "V1001_TODAY_MUSIC3"
                },
                {
                    "type": "click",
                    "name": "修改昵称",
                    "key": "V1001_TODAY_MUSIC2"
                },
                {
                     "type": "view",
                     "name": "游戏充值",
                     "url": "http://byfweixin.sinaapp.com/payment/"
                }
            ]
        },
        {
            "name": "有奖活动",
            "sub_button": [
                {
                    "type": "click",
                    "name": "签到奖励",
                    "key": "V1001_TODAY_MUSIC1"
                },
                {
                    "type": "view",
                    "name": "活动",
                    "url": "http://byfweixin.sinaapp.com/index.php/Home/Index/rechange"
                }
            ]
        },
        {
            "type": "click",
            "name": "有乐助手",
            "key": "V1001_TODAY_MUSIC"
        }
    ]
}
       ';
	  return $list;
   }
}
