<?php
/**
	* wechat php test
	*/
header("Content-type: text/html; charset=utf-8"); 
require_once("./inc/haitou/Haitou_parser.php");
require_once("./inc/haitou/Helper.php");

//define your token
define("TOKEN", "******");
$wechatObj = new wechatCallbackapiTest();

//$wechatObj->valid();

$act = new Haitou_parser();
					$result = $act->get_xjh();
					echo $contentStr = get_utf8_content($result[1]->title, "ASCII");
/*					 iconv("ASCII","UTF-8", $contentStr);
					echo mb_detect_encoding("a");*/

/*$wechatObj->transmitinfo("a", "text", $contentStr, 0);*/

class wechatCallbackapiTest
{
	public function valid()
		{
				$echoStr = $_GET["echostr"];

				//valid signature , option
				if($this->checkSignature()){
					echo $echoStr;
						$this->responseMsg();
					exit;
				}
		}

		public function responseMsg()
		{
			//get post data, May be due to the different environments
			$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

					//extract post data
			if (!empty($postStr))
			{
									
				$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
				$RX_TYPE = trim($postObj->MsgType);   // 信息类型

				switch ($RX_TYPE) 
				{
					case 'text':
						$resultStr = $this->receiveText($postObj);
						break;
					case 'event':
						$resultStr = $this->receiveEvent($postObj);
						break;
					default :
						$resultStr = "unknow msg type: ".$RX_TYPE;
						break;
				}

				echo $resultStr;
			}
					
		}
	
	private function checkSignature()
	{
				$signature = $_GET["signature"];
				$timestamp = $_GET["timestamp"];
				$nonce = $_GET["nonce"];  
						
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 接受事件信息
	 *
	 * @return xml
	 * @author solo
	 **/
	private function receiveEvent($object)
	{
			$fromUsername = $object->FromUserName;
			$toUsername = $object->ToUserName;
			$keyword = trim($object->Content);
			$time = time();
			$msgType = "text";

			$textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";    

			 $contentStr = "";
				switch ($object->Event)
				{
						case "subscribe":
								// 用户关注时回复
								$contentStr = "	#欢迎关注学生地带,
																回复以下关键字会得到结果哦,
																天气(tq),宣讲(xj),招聘(zp)
																未完待续
																->.->
															";
								
								break;
						default :
								$contentStr = "亲,你按错地方啦!";
								break;
				}
				$resultStr = $this->transmitinfo($object, "text", $contentStr);
				return  $resultStr;
	}

	/**
	 * 接受文本信息
	 *
	 * @return xml
	 * @author solo
	 **/
	private function receiveText($object)
	{
			$fromUsername = $object->FromUserName;
			$toUsername = $object->ToUserName;
			$keyword = trim($object->Content);
			$time = time();

			$contentStr = "";
			$textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";

				if(!empty( $keyword ))
				{
					
					if($keyword == "天气" || $keyword == "tq")
					{

								$msgType = "text";
								$post_data = array();
								$post_data['city'] = "武汉";
								$post_data['submit'] = "submit";
								$url='http://search.weather.com.cn/wap/search.php';
								$o="";
								foreach ($post_data as $k=>$v){
									$o.= "$k=".urlencode($v)."&";
								}
								$post_data=substr($o,0,-1);
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_POST, 1);
								curl_setopt($ch, CURLOPT_HEADER, 0);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
								curl_setopt($ch, CURLOPT_URL,$url);
								curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
								$result = curl_exec($ch);
								curl_close($ch);
								$result=explode('/',$result);
								$result=explode('.',$result['5']);
								$citynum = $result['0'];
								$weatherurl = "http://m.weather.com.cn/data/".$citynum.".html";
								$weatherjson = file_get_contents($weatherurl);
								$weatherarray = json_decode($weatherjson,true);
								$weatherinfo = $weatherarray['weatherinfo'];
								$contentTpl = "#%s%s
															 我校天气状况：
															 今天天气：%s %s，%s
															 明天天气：%s%s，%s
															 后天天气：%s%s，%s
															 ";
								$contentStr = sprintf(
															$contentTpl,
															$weatherinfo['date_y'],
															$weatherinfo['week'],
															$weatherinfo['temp1'],
															$weatherinfo['weather1'],
															$weatherinfo['wind1'],
															$weatherinfo['temp2'],
															$weatherinfo['weather2'],
															$weatherinfo['wind2'],
															$weatherinfo['temp3'],
															$weatherinfo['weather3'],
															$weatherinfo['wind3']
														);
			}else if($keyword == "宣讲" || $keyword == "xj")
			{
					$act = new Haitou_parser();
					$result = $act->get_xjh();
					$contentStr = $this->CP1251toUTF8($result[1]->title);


/*					foreach ($act->get_xjh() as $key => $value) {
						$contentStr .= $value->title;
					}*/

//					$contentStr = "后续应用正在努力开发中哦.~";
			}
			else
			{
						$contentStr = "后续应用正在努力开发中哦~.~";
					
				}

		}else 
		{
			echo "";
			exit;
		}
				$resultStr = $this->transmitinfo($object, "text", $contentStr);
				//$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				return $resultStr;
	}


/**
  * 构造返回信息
  *
  * @return void
  * @author solo
  **/

function transmitinfo($object, $msgtype, $content, $flag = 0)
    {
        $textTpl = "<xml version='1.0' encoding='UTF-8'>
										<ToUserName><![CDATA[%s]]></ToUserName>
										<FromUserName><![CDATA[%s]]></FromUserName>
										<CreateTime>%s</CreateTime>
										<MsgType><![CDATA[%s]]></MsgType>
										<Content><![CDATA[%s]]></Content>
										<FuncFlag>%d</FuncFlag>
										</xml>";
        echo $resultStr = sprintf($textTpl, "t", "q", time(), $msgtype, $content, $flag);
        return $resultStr;
    }

}
?>