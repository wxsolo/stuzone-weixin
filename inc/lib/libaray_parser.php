<?php
// error_reporting(0);
// 引入公共文件
include_once('../common/Http_client.php');
include_once('../common/simple_html_dom.php');
include_once('../common/debug_helper.php');
	/**
	* 中南民大图书信息抓取类
	*/
	class Libaray_parser
	{
		
		function __construct()
		{
		}

		/**
		 * 抓取关键字搜索结果函数
		 *
		 * @return void
		 * @author solo
		 **/
		function get()
		{
			$host = $this->host;

			$client = new HttpClient($host);

			// 禁止自动跳转
			$client->setHandleRedirects(false);

			// 伪造浏览器
			$client->setUserAgent('Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.3a) Gecko/20021207');
			$client->referer = "http://".$host."/";

			// 得到网站根目录
			if (!$client->get('/opac/openlink.php?strSearchType=title&match_flag=forward&historyCount=1&strText=php&doctype=ALL&displaypg=20&showmode=list&sort=CATA_DATE&orderby=desc&location=ALL'))
			{
				die('404');				// 返回错误代码,获取失败
			}
			$pageContents = $client->getContent();

			return $pageContents;
		}

		/*********************** 私有区 ****************************/
		private $host = "coin.lib.scuec.edu.cn";				//	抓取地址

	}// End Class

	$lib = new Libaray_parser();
	$res = $lib->get();
	e($res);
?>