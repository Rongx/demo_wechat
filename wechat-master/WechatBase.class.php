<?php
    namespace Wechat;

    class WechatBase
    {

        //在构造函数中进行与微信服务器进行交互
        public function __construct(array $config)
        {
            //            $this->traceHttp();
            if (!isset($config['echostr'])) {
                //进行响应
                $this->responseMsg();
            } else {
                //进行验证 并返回微信服务器发送的随机值
                if ($this->checkSignature($config)) {
                    echo $config['echostr'];
                }
            }
        }

        // 进行代码追踪
        private function traceHttp()
        {
            $content = date('Y-m-d H:i:s') . "\nREMOTE_ADDR:" . $_SERVER["REMOTE_ADDR"] . "\nQUERY_STRING:" . $_SERVER["QUERY_STRING"] . "\n\n";
            if (isset($_SERVER['HTTP_APPNAME'])) {
                sae_set_display_errors(FALSE);
                sae_debug(trim($content));
                sae_set_display_errors(TRUE);
            } else {
                $max_size = 100000;
                $log_filename = "log.xml";
                if (file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)) {
                    unlink($log_filename);
                }
                file_put_contents($log_filename, $content, FILE_APPEND);
            }
        }

        // 获得微信access_tokenss
        protected function get_access_token($config = array('appid' => "wxd0b854dac97b1edf", 'appsecret' => "e029fcbb83cd847dabdc38a1bd84b85d"))
        {
            //            $appid = "wxfa12bc19b4ac3d1d";
            //            $appsecret = "d0a07fefb8d531eb8fa74dae9f6dafdf";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$config['appid']}&secret={$config['appsecret']}";
            $out_put = $this->https_request($url);
            $json = json_decode($out_put, TRUE);

            //             echo $json['access_token'];
            return $json['access_token'];
        }

        // 进行curl 请求
        protected function https_request($url, $data = NULL)
        {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            if (!empty($data)) {
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            curl_close($curl);

            return $output;
        }

        //进行信息微信验证
        private function checkSignature($config)
        {

            $signature = $config["signature"];
            $timestamp = $config["timestamp"];
            $nonce = $config["nonce"];
            $token = $config['token'];
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode($tmpArr);
            $tmpStr = sha1($tmpStr);
            if ($tmpStr == $signature) {
                return TRUE;
            } else {
                return FALSE;
            }
        }


        //返回信息给微信用户
        private function responseMsg()
        {
            $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
            if (!empty($postStr)) {
                //主要是解析接收用户消息
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $RX_TYPE = trim($postObj->MsgType);
                switch ($RX_TYPE) //接收用户消息
                {
                    case "event": //关注事件
                        $result = $this->receiveEvent($postObj);
                        break;
                    case "text":
                        $result = $this->receiveText($postObj);
                        break;
                    case "image":
                        $result = $this->receiveImage($postObj);
                        break;
                    case "voice": //语音消息
                        $result = $this->receiveVoice($postObj);
                        break;
                    case "video"://视频
                        $result = $this->receiveVideo($postObj);
                        break;
                    case "shortvideo":
                        $result = $this->receiveShortvideo($postObj);
                        break;
                    case "location":
                        $result = $this->receiveLocation($postObj);
                        break;
                    case "link":
                        $result = $this->receiveLink($postObj);
                        break;
                    default :
                        $result = "unknow msg type: " . $RX_TYPE;
                        break;

                }
                echo $result;
            } else {
                echo "";
                exit;
            }
        }

        //接收到客服text 文本
        private function receiveText($object)
        {
            //        $content = "你发送的是文本，内容为：".$object->Content;
            $content = '<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxfa12bc19b4ac3d1d&redirect_uri=http://byfweixin.sinaapp.com/oauth.php&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect">点击这里体验OAuth授权</a>';
            $result = $this->transmitText($object, $content);

            return $result;
        }

        //接收客服image 信息
        private function receiveImage($object)
        {
            $content = "你发送的是图片，地址为：" . $object->MediaId;
            //        $result = $this->transmitText($object, $content);
            $result = $this->transmitImage($object, $content);

            return $result;
        }

        //接收客户 voice
        private function receiveVoice($object)
        {
            //        $content = "你发送的是语音，媒体ID为：".$object->MediaId;
            //        $result = $this->transmitText($object, $content);
            // 当开启语音识别为语音识别结果
            if (isset($object->Recognition)) {
                $content = $object->Recognition;
            }
            $result = $this->transmitVoice($object);

            return $result;
        }

        // 接收视频信息
        private function receiveVideo($object)
        {
            //        $content = "你发送的是视频，媒体ID为：".$object->MediaId;
            //        $result = $this->transmitText($object, $content);
            $result = $this->transmitvideo($object);

            return $result;
        }

        //位置
        private function receiveLocation($object)
        {
            //        $content = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
            //        $result = $this->transmitText($object, $content);
            //        $result = $this->transmitNews($object);
            $result = $this->transmitMusic($object);

            return $result;
        }

        //链接
        private function receiveLink($object)
        {
            $content = "你发送的是链接，标题为：" . $object->Title . "；内容为：" . $object->Description . "；链接地址为：" . $object->Url;
            $result = $this->transmitText($object, $content);

            return $result;
        }

        //小视频
        private function receiveShortvideo($object)
        {
            //        $content = "你发送的是视频，媒体ID为：".$object->MediaId;
            //        $result = $this->transmitText($object, $content);
            $result = $this->transmitvideo($object);

            return $result;
        }

        //回复文本消息
        private function transmitText($object, $content)
        {
            $textTpl = "<xml>
    	  <ToUserName><![CDATA[%s]]></ToUserName>
		  <FromUserName><![CDATA[%s]]></FromUserName>
		  <CreateTime>%s</CreateTime>
		  <MsgType><![CDATA[text]]></MsgType>
		  <Content><![CDATA[%s]]></Content>
		  </xml>";
            $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);

            return $result;
        }

        //回复图片信息
        private function transmitImage($object, $content)
        {
            $imageTmp = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[image]]></MsgType>
                    <Image>
                    <MediaId><![CDATA[%s]]></MediaId>
                    </Image>
                    </xml>";
            $result = sprintf($imageTmp, $object->FromUserName, $object->ToUserName, time(), $content);

            return $result;
        }

        //回复语音信息
        private function transmitVoice($object)
        {
            $videoTmp = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[voice]]></MsgType>
                    <Voice>
                    <MediaId><![CDATA[%s]]></MediaId>
                    </Voice>
                    </xml>";
            $result = sprintf($videoTmp, $object->FromUserName, $object->ToUserName, time(), $object->MediaId);

            return $result;
        }

        //回复视频信息
        private function transmitvideo($object)
        {
            $imageTmp = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[video]]></MsgType>
                    <Video>
                    <MediaId><![CDATA[%s]]></MediaId>
                    <Title><![CDATA['刚才发送的视频']]></Title>
                    <Description><![CDATA['对刚才发送视频描述']]></Description>
                    </Video> 
                    </xml>";
            $result = sprintf($imageTmp, $object->FromUserName, $object->ToUserName, time(), $object->MediaId);

            return $result;
        }

        //回复音乐信息
        private function transmitMusic($object)
        {
            $imageTmp = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[music]]></MsgType>
                    <Music>
                    <Title><![CDATA['测试音乐标题']]></Title>
                    <Description><![CDATA['测试音乐描述']]></Description>
                    <MusicUrl><![CDATA[%s]]></MusicUrl>
                    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                    </Music>
                    </xml>";
            $result = sprintf($imageTmp, $object->FromUserName, $object->ToUserName, time(), 'http://yinyueshiting.baidu.com/data2/music/123319204/8513530122400128.mp3?xcode=b0592559bae585249d3ef966b25050e03a9a633934f8cad4', 'http://yinyueshiting.baidu.com/data2/music/123319204/8513530122400128.mp3?xcode=b0592559bae585249d3ef966b25050e03a9a633934f8cad4');

            return $result;
        }

        //回复图片信息
        private function transmitNews($object)
        {
            $imageTmp = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>1</ArticleCount>
                    <Articles>
                    <item>
                    <Title><![CDATA['图文信息标题一']]></Title> 
                    <Description><![CDATA['图文信息描述一']]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                    </item>
                    </Articles>
                    </xml> ";
            $result = sprintf($imageTmp, $object->FromUserName, $object->ToUserName, time(), 'http://ww2.sinaimg.cn/thumbnail/b0fbe1b7jw1es1njx15hyj20c807n74k.jpg', 'http://weibo.com/mygroups?gid=3524646135341115&wvr=6&leftnav=1&isspecialgroup=1');

            return $result;
        }

        // 原样输出数据，进行代码调试
        public function print_ori($data)
        {
            echo "<pre>";
            var_dump($data);
            echo "</pre>";
        }

        //进行事件处理
        private function receiveEvent($object)
        {
            $content = "";
            switch ($object->Event) {
                case "subscribe":   //关注事件
                    $content = "亲爱的玩家，您好。我是【有乐游戏】的微信助手乐乐。
我给您准备了一份大大的见面礼，绑定游戏后即可得到，赶快来绑定吧！
<a href='http://byfweixin.sinaapp.com/payment/example/jsapi.php'>这是支付接口数据！</a>
<a href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxb611ddaac13b231e&redirect_uri=http%3A%2F%2Fbyfweixin.sinaapp.com%2Findex.php%2FHome%2FIndex%2Foauth&response_type=code&scope=snsapi_userinfo&state=1234646#wechat_redirect'>内容正在维护，给您带来的不便还请见谅...</a>";
                    //                if (isset($object->EventKey)){
                    //                   $content .= "\n来自二维码场景 ".$object->EventKey;
                    //                 }
                    break;
                case "unsubscribe": //取消关注事件
                    $content = "努力挖掘更有价值信息，希望有机会再次为您提供服务！";
                    break;
                case "SCAN":
                    $content = "扫描二维码场景 " . $object->EventKey;
                    break;
                case "CLICK": // 点击菜单拉取消息时的事件推送
                    switch ($object->EventKey)  //事件key 值
                    {
                        case "公司简介":
                            $content = "buyingfei888 ceshi ";
                            break;
                        default:
                            $content = "内容正在维护，给您带来的不便还请见谅...";
                            break;
                    }
                    break;
                case "VIEW":  // 点击菜单跳转链接时的事件推送
                    $content = "this is a view";
                    break;
                case "scancode_push"://"扫码推事件的事件推送"
                    $content = "scancode_push";
                    break;
                case "scancode_waitmsg"://扫码推事件且弹出“消息接收中”提示框的事件推送
                    $content = "scancode_waitmsg";
                    break;
                case "pic_sysphoto"://弹出系统拍照发图的事件推送
                    break;
                case "pic_photo_or_album": //弹出拍照或者相册发图的事件推送

                    break;
                case "pic_weixin": //弹出微信相册发图器的事件推送
                    break;
                case "location_select"://弹出地理位置选择器的事件推送
                    break;
                default :
                    $content = "未设置，可以根据微信平台自行拓展！";

            }
            $result = $this->transmitText($object, $content);

            return $result;
        }

    }

?>
