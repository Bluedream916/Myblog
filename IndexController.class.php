<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller 
{
    

    public function valid()
    {
        $echoStr = $_GET["echostr"];

        
        //验证
        if( $this->checkSignature() && $echostr ){
            echo $echoStr;
            exit;

        //接收微信的消息
        //$echoStr = ''  
        }else{

            $this->responseMsg();
        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        
        if (!empty($postStr)){
                
                libxml_disable_entity_loader(true);
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);


                //获取到用户ID
                $fromUsername = $postObj->FromUserName;//

                //获取公众号的ID
                $toUsername = $postObj->ToUserName;


                //获取到用户发送内容
                $keyword = trim($postObj->Content);
                

                $time = time();


                if( $keyword == '?' ){
                                    //发送给微信服务器的消息模板
                                        $textTpl = "<xml>
                        <ToUserName><![CDATA[".$fromUsername."]]></ToUserName>
                        <FromUserName><![CDATA[".$toUsername."]]></FromUserName>
                        <CreateTime>".time()."</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[".time()."]]></Content>
                        </xml>";  

                }elseif( $keyword == '图文' ){


                    $data = array(

                        array(
                            'Title'=>'哈11112',
                            'Description'=>'ddd',
                            'PicUrl'=>'http://mmbiz.qpic.cn/mmbiz/8I4Kl1TrFCnsQUuZ3GbHlgWVHB6rRR5OT4VomHfeubiajDqAbdNLg1TclAYws2K0MtwINBkiaeMh6rQgD5ZXENHw/0',
                            'Url'=>'http://xuanhaoguo.com'
                        ),
                        array(
                            'Title'=>'哈哈3',
                            'Description'=>'ddd',
                            'PicUrl'=>'http://mmbiz.qpic.cn/mmbiz/8I4Kl1TrFCnsQUuZ3GbHlgWVHB6rRR5OT4VomHfeubiajDqAbdNLg1TclAYws2K0MtwINBkiaeMh6rQgD5ZXENHw/0',
                            'Url'=>'http://xuanhaoguo.com'
                        ),

                        );


                    $this->responsePicNews( $postObj ,$data);
                }else{

                                    //发送给微信服务器的消息模板
                $textTpl = "<xml>
<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>
<FromUserName><![CDATA[".$toUsername."]]></FromUserName>
<CreateTime>".time()."</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[".$keyword."]]></Content>
</xml>";   
                }  
    
                echo $textTpl;


        }else {
            echo "";
            exit;
        }
    }



    private function checkSignature()
    {
        // you must define TOKEN by yourself
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
                
        $token = 'weixin';
        // $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }




        //回复图文消息
    private function responsePicNews( $postObj, $data )
    {

        //获取到用户微信ID
        $FromUserName = $postObj->FromUserName;

        //获取到公号ID
        $ToUserName = $postObj->ToUserName;

        $newTpl = '<xml>
<ToUserName><![CDATA['.$FromUserName.']]></ToUserName>
<FromUserName><![CDATA['.$ToUserName.']]></FromUserName>
<CreateTime>'.time().'</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>'.count($data).'</ArticleCount>
<Articles>';

        foreach($data as $k => $v )
        {

            $newTpl .='<item>
<Title><![CDATA['.$v['Title'].']]></Title> 
<Description><![CDATA['.$v['Description'].']]></Description>
<PicUrl><![CDATA['.$v['PicUrl'].']]></PicUrl>
<Url><![CDATA['.$v['Url'].']]></Url>
</item>';

        }

        $newTpl .='</Articles>
</xml>';

        echo $newTpl;

        exit;



    }
}