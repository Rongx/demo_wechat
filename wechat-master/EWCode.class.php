<?php
namespace Wechat;
class EWCode  extends WechatBase{
    private $data = array();
    private $access_token = "";

    public function __construct($config,$data) {
       $this->data = $data;
       $this->access_token = $this->get_access_token($config);
   }
   private function get_ticket($data){
       $json_info = json_decode($data,true);
       $data = urlencode($json_info['ticket']) ;
       echo $data;
       return urlencode($json_info['ticket']) ;
   }
    private function downloadWeixinFile($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);    
        curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpinfo = curl_getinfo($ch);
        curl_close($ch);
        return array_merge(array('body' => $package), array('header' => $httpinfo)); 
    }
   //创建临时ticket
   public function create_tmp_ticket(){
       $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->access_token;
       $data =  $this->https_request($url, $this->data);
        //       print_r($data);
       //echo $this->get_ticket($data);
       return $this->get_ticket($data);
   }
   //创建永久ticket
   public function create_long_ticket(){
       $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->access_token;
       $data =  $this->https_request($url, $this->data);
      //       print_r($data);
       //echo $this->get_ticket($data);
       $this->get_ticket($data);
   }
   //创建二维码  创建简单二维码，只需要通过浏览器get 请求即可，不需要通过此生成图片
   public function create_ERCode($ticket){
       $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
       file_get_contents($url);
       $imageInfo = $this->downloadWeixinFile($url);
        $filename = "D:\lover.jpg"; //图片路径
        $local_file = fopen($filename, 'w');
        if (false !== $local_file){
            if (false !== fwrite($local_file, $imageInfo["body"])) {
                fclose($local_file);
            }
        }
   }

   
}
