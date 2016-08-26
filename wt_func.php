<?php
function curlGet($url){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		//curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
		curl_close($ch);
	}
function get_token(){
	    /*function curlGet($url){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		//curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
		curl_close($ch);
	    }*/
		$sqlcon=mysql_connect("localhost","root","ChangLam1314");
		mysql_select_db("wechat",$sqlcon);
		$time=time();
		$sqltext="select access,time from token";
		$result=mysql_query($sqltext,$sqlcon);
		$temparray=mysql_fetch_array($result);
		$old_time=$temparray['time'];
		$old_access=$temparray['access'];
		if(((int)$time-(int)$old_time)>=7500)
		{
			$url_get="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxa3c376cdbddb269d&secret=76970de6a2f6d11e57f505a800b35d92";
		$json=json_decode(curlGet($url_get),true);
		$new_access=$json['access_token'];
		$new_time=time();
		$sqltext="update token set access='$new_access',time='$new_time' where pid='access'";
		mysql_query($sqltext,$sqlcon);
		return $new_access;
		}
		else
		{
			return $old_access;
		}
}

?>