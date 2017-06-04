<?php
require_once("../Snoopy-2.0.0/Snoopy.class.php");
header("Content-type:text/html;charset=utf-8");

class Response {

    public static function json($code,$message='',$data = array()){
        $result = array(
            'code' => $code,
            'message' => $message,
            'data' =>$data
        );

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function catched(){
    global $Snoopy,$username,$psw,$fromdate,$todate;//定义为全局变量在函数内部使用
    $array = Array(
        'barcode'=>$username,
        'password'=>$psw,
        'formdate'=>$fromdate,
        'todate'=>$todate
    );
    $url = 'http://202.197.232.4:8081/opac_two/include/login_app.jsp';
    $Snoopy -> submit($url,$array);
    if ($Snoopy->results != 'ok'){
         Response::json(100,"用户名或密码错误",NULL);
         exit;
    }
    $Snoopy ->setcookies();
    $url2 = 'http://202.197.232.4:8081/opac_two/reader/jieshulishi.jsp';
    $Snoopy ->fetch($url2);
    // echo $Snoopy->results;
     $str =  $Snoopy->results;//清除字符串两边的空格
     $str = iconv("GB2312","utf-8",$str);//编码转换，否则不能转为json格式
    $str=preg_replace('/\t/is',"",$str);
    $str=preg_replace('/\r\n/is',"",$str);
    $str=preg_replace('/\r/is',"",$str);
    $str=preg_replace('/\n/is',"",$str);
    $str=preg_replace('/ /is',"",$str);
    $str=preg_replace('/&nbsp/is',"",$str);
    //注意是(.*?)而不是(.*)?前者判断这个里面，后者是有无整个整体
    $pattern='/<tdalign=left>(\d*);<\/td><tdalign=left>(.*?);<\/td><tdalign=left>(.*?);<\/td><tdalign=left>(.*?);<\/td><tdalign=left>(\d{4}-\d{2}-\d{2});<\/td>/';//正则判断
    preg_match_all($pattern,$str,$res);
    if($res == null){
        Response::json(199,"未知错误",NULL);
    }
    for($i=0;$i<count($res[0]);$i++){
                $results[$i] =array(
                    'order'=>$res[1][$i],
                    'book'=>$res[2][$i],
                    'action'=>$res[4][$i],
                    'date'=>$res[5][$i]
                );
    }
    Response::json(200,"succeed",$results);
}

$Snoopy = new Snoopy();
$username = isset($_POST['username'])?$_POST['username']:"";
$psw = isset($_POST['psw'])?$_POST['psw']:"";
$fromdate = isset($_POST['fromdate'])?$_POST['fromdate']:"";
$todate = isset($_POST['todate'])?$_POST['todate']:"";

if($username != "" && $psw != ""){
    catched();
}else{
    Response::json(101,"参数缺失",NULL);
}

?>