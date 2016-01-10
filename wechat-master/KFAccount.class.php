<?php
namespace Wechat;
//客服接口
// 对客服账号有关修改需要开通多客服权限，在测试账号下无法执行，注意
class KFAccount extends WechatBase {
    private $access_token = "";
    public function __construct($config) {
        //parent::__construct($weixin_token);
        $this->access_token = $this->get_access_token($config);
    }
    
    //添加账号
    public function add_account($data){
        echo $this->access_token;
        $url = "https://api.weixin.qq.com/customservice/kfaccount/add?access_token=".$this->access_token;
        $out_data = $this->https_request($url, $data);
        print_r($out_data);
    }
    // 修改账号
    public function update_account($data){
        $url = "https://api.weixin.qq.com/customservice/kfaccount/update?access_token=".$this->access_token;
        $out_put = $this->https_request($url, $data);
        print_r($out_put);
    }
    //删除账号
    public function delete_account($data){
        $url = "https://api.weixin.qq.com/customservice/kfaccount/del?access_token=".$this->access_token;
        $out_put = $this->https_request($url, $data);
        print_r($out_put);
    }
    
    //  修改客服头像
    public function update_heading($kfaccount){
        $url ="http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token=".$this->access_token."&kf_account=".$kfaccount;
        $out_put = $this->https_request($url);
        print_r($out_put);
        
    }
    
    //获得客服列表
    public function get_account_list(){
        $url = "https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token=".$this->access_token;
        $out_put = $this->https_request($url);
        print_r($out_put);
    }
    
    
    
    
    
    
    
}
