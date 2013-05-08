<?php 
function transmitinfo()
    {
       echo $textTpl = "<xml>
										<ToUserName><![CDATA[%s]]></ToUserName>
										<FromUserName><![CDATA[%s]]></FromUserName>
										<CreateTime>%s</CreateTime>
										<MsgType><![CDATA[%s]]></MsgType>
										<Content><![CDATA[%s]]></Content>
										<FuncFlag>%d</FuncFlag>
										</xml>";
         $resultStr = sprintf($textTpl, "solo", "you", time(), "text", "test", 0);
        return $resultStr;
    }

    echo transmitinfo();
    ?>