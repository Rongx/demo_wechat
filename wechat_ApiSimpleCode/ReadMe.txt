
*** 什么是EncodingAESKey？ 
微信公众平台采用AES对称加密算法对推送给公众帐号的消息体对行加密，EncodingAESKey则是加密所用的秘钥。公众帐号用此秘钥对收到的密文消息体进行解密，回复消息体也用此秘钥加密。 

注意事项：
1.WXBizMsgCrypt.php文件提供了WXBizMsgCrypt类的实现，是用户接入企业微信的接口类。Sample.php提供了示例以供开发者参考。errorCode.php, pkcs7Encoder.php, sha1.php, xmlparse.php文件是实现这个类的辅助类，开发者无须关心其具体实现。
2.WXBizMsgCrypt类封装了 DecryptMsg, EncryptMsg两个接口，分别用于开发者解密以及开发者回复消息的加密。使用方法可以参考Sample.php文件。
3.加解密协议请参考微信公众平台官方文档。