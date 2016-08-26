<?php
require("cgi-bin/wt_func.php");
define("TOKEN","Gzszwt_0822");
define("APPID","wx45fd47532deb5295");
define("SECRET","be04be8866089228e82913066e04fae4");
$access=get_token();
$wtcheck=new wechatCallBack;
$wtcallback=new wechatCallBack;
$wtcallback->response();
//$wtcheck->check();
class wechatCallBack
{
	public function check()
	{
		$echostr=$_GET["echostr"];
		if ($this->checktoken())
		{echo $echostr;
		 exit;
	    }
	}
	public function response()
	{
		$xmltext=$GLOBALS['HTTP_RAW_POST_DATA'];
		if(!empty($xmltext))
		{
		$xmlobj=simplexml_load_string($xmltext);
		$type=$xmlobj->MsgType;
		$text=trim($xmlobj->Content);
		$toname=$xmlobj->ToUserName;
		$fromname=$xmlobj->FromUserName;
		$time=time();
		$textStr = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";
		if($type=="text")
		{
				if(stripos($text,"点歌+")!==false)
				{
					$sqlcon=mysql_connect("localhost","root","ChangLam1314");
					mysql_query("set names 'utf8' ");
					mysql_query("set character_set_client=utf8");
					mysql_query("set character_set_results=utf8");
					mysql_select_db("wechat",$sqlcon);
					$textarray=explode("+",$text);
					$song=$textarray[2];
					$singer=$textarray[3];
					$name=$textarray[1];
					$date=date("Y-m-d H:i:s",$time+60*60*8);
					if($this->checksame($song,$singer))
					{
						$restext="您点的歌已经被别人点过，请勿重复点歌";
						$totext=sprintf($textStr,$fromname,$toname,$time,"text",$restext);
					}
					else
					{
						$sqltext="insert into song (openid,name,song,singer,time) values	 ('$fromname','$name','$song','$singer','$date')";
						$result=mysql_query($sqltext,$sqlcon);
						$restext=$name."~\n你点的歌是：".$song."\n歌手：".$singer;
						$totext=sprintf($textStr,$fromname,$toname,$time,"text",$restext);
					}
					echo $totext;
					}
				if(stripos($text,"我的点歌")!==false)
				{
					$sqlcon=mysql_connect("localhost","root","ChangLam1314");
					mysql_query("set names 'utf8' ");
					mysql_query("set character_set_client=utf8");
					mysql_query("set character_set_results=utf8");
					mysql_select_db("wechat",$sqlcon);
					$sqltext="select name,song,singer from song where openid='$fromname'";
					$result=mysql_query($sqltext,$sqlcon);
					if(!empty($result))
					{
						while($res=mysql_fetch_array($result))
						{
							$textr=$textr."歌名：".$res['song']."\n歌手：".$res['singer']."\n\n";
						}
						if(empty($textr))
						{
							$textr="你还没有点过歌哦~";
						}
						else
						{
							$textr="我的点歌:\n".$textr;
						}
						$totext=sprintf($textStr,$fromname,$toname,$time,"text",$textr);
						echo $totext;
					}
				}		
		}
		else if($type=="event")
		{
			$event=$xmlobj->Event;
			if($event=="subscribe")
			{
				$restext="这里是广州三中学生会第一帅的部门（没有第二），如果你想加入我们，请悄悄地给我们留言~
本公众号同时具有广州三中广播站的点歌功能，点歌请按以下格式发送消息给公众号：点歌+昵称+歌名+歌手（+号是输入法英文状态下的+号）\n发送”我的点歌“可以查看自己点过的歌曲噢";
				$totext=sprintf($textStr,$fromname,$toname,$time,"text",$restext);
				echo $totext;
			}
		}
		}
	}
	private function checktoken()
	{
			$sign=$_GET["signature"];
			$timestamp=$_GET["timestamp"];
			$nonce=$_GET["nonce"];
			$token=TOKEN;
			$temparray=array($timestamp,$nonce,$token);
			sort($temparray,SORT_STRING);
			$tempstr=implode($temparray);
			$tempstr=sha1($tempstr);
			if($tempstr==$sign)
			{
				return true;
			}
			else
			{
				return false;
			}
	}
	private function checksame($songname,$singer)
	{
		$sqlcon=mysql_connect("localhost","root","ChangLam1314");
		mysql_query("set names 'utf8' ");
		mysql_query("set character_set_client=utf8");
		mysql_query("set character_set_results=utf8");
		mysql_select_db("wechat",$sqlcon);
		$query="select * from song where song='$songname' and singer='$singer'";
		$c_result=mysql_query($query,$sqlcon);
		while($resq=mysql_fetch_array($c_result))
		{
			if($resq['song']==$songname && $resq['singer']==$singer)
			{
				return true;
			}
			else
			{
				return false;
			}
		}	
	}	
}
?>