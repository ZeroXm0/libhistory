<?php

$WX = new WeiXin();
if(isset($_GET['echostr'])){
    $WX->checkSignature();
}else{
    $WX->responseMsg();
}

class WeiXin{
    public function checkSignature(){
       $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $token = 'zero';
        $signature = $_GET['signature'];
        $array = array($timestamp,$nonce,$token);
        sort($array);
        $tmpstr = implode($array);
        $tmpstr = sha1($tmpstr);

        if($tmpstr == $signature){
            echo $_GET['echostr'];
            exit;
        }

        
    }



    public function responseMsg(){
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        $postObj = simplexml_load_string($postArr,'SimpleXMLElement',LIBXML_NOCDATA);
        if($postObj->MsgType=='event' && $postObj->Event =='subscribe'){
            $Content = '如需查询借书记录，请回复“学号+&+图书馆密码”';
            $this->responseText($postObj,$Content);
        }else if($postObj->MsgType=='text'){
            $str = $postObj->Content;
            $str=preg_replace('/\t/is',"",$str);
            $str=preg_replace('/\r\n/is',"",$str);
            $str=preg_replace('/\r/is',"",$str);
            $str=preg_replace('/\n/is',"",$str);
            $str=preg_replace('/ /is',"",$str);
            $pattern = '/(\d{6,10})/';;
            preg_match_all($pattern,$str,$res);
            if($res != null){
                $res = json_encode($res[1]);
                $res = json_decode($res);
                $username = $res[0];
                $psw = $res[1];
                $postUrl = "http://123.206.190.212/lib/index.php";
                $curlPost = array(
                    "username" => $username,
                    "psw" => $psw
                    );
                $ch = curl_init();//初始化curl
                curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
                curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
                curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
                $data = curl_exec($ch);//运行curl
                $data = json_decode($data,true);
                curl_close($ch); 
                $res = $data['data'];
                $Content = "";
                
                $num = count($res);
                if($num > 1){
                    if($num >20){
                        $list = 20;
                    }else{
                        $list = $num;
                    }
                  for($i=0;$i<$list;$i++){
                        $Content .= "序列:".$res[$i]['order']."\n书名:".$res[$i]['book']."\n活动:".$res[$i]['action']."\n日期:".$res[$i]['date']."\n\n";
                    }
                    if($num >20){
                        $Content .= '<a href="http://202.197.232.4:8081/opac_two/reader/infoList.jsp">查询更多</a>';
                    }
                /*
                if(count($res) > 1){
                  for($i=0;$i<count($res)-1;$i++){
                        $Content .= "序列:".$res[$i]['order']."\n书名:".$res[$i]['book']."\n活动:".$res[$i]['action']."\n日期:".$res[$i]['date']."\n\n";
                    }
                    $Content .= "序列:".$res[count($res)-1]['order']."\n书名:".$res[count($res)-1]['book']."\n活动:".$res[count($res)-1]['action']."\n日期:".$res[count($res)-1]['date'];
                */
                }else{
                    $Content = '如需查询借书记录，请回复“学号+&+图书馆密码”'; 
                }
                }else{
                    $Content = '如需查询借书记录，请回复“学号+&+图书馆密码”';                
                }
            }else{
                $Content = '如需查询借书记录，请回复“学号+&+图书馆密码”';
            }
            $this->responseText($postObj,$Content);;
    }

    public  function responseText($postObj,$Content){
        $ToUser = $postObj->FromUserName;
        $FromUser = $postObj->ToUserName;
        $Time= time();
        $Template = '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>';
        echo sprintf($Template,$ToUser,$FromUser,$Time,$Content);

    }
}