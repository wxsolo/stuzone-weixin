<?php
// error_reporting(0);
// 引入公共文件
include_once('./inc/common/Http_client.php');
include_once('./inc/common/simple_html_dom.php');
include_once('Activity.php');
//include_once('debug_helper.php');

/**
 * 海投网信息
 *
 * @package parser
 * @author solo
 **/
class  Haitou_parser 
{
	/**
	 * 构造函数
	 *
	 * @return void
	 * @author solo
	 **/
	function __construct(){}


	/**
	 * 得到宣讲会列表第一页
	 *
	 * @return 返回一个Activity对象 
	 * @author solo
	 **/

	function get_xjh()
	{
		$host = $this->host_xjh;

		$client = new HttpClient($host);

		// 禁止自动跳转
		$client->setHandleRedirects(false);

		// 伪造浏览器
		$client->setUserAgent('Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.3a) Gecko/20021207');
		$client->referer = "http://".$host."/";

		// 得到网站根目录
		if (!$client->get('/'))
		{
			die('404');				// 返回错误代码,获取失败
		}
		$pageContents = $client->getContent();
//		var_dump($pageContents);

//		log_time();

		// 初始化html_dom类
		$dom = new simple_html_dom();
//		log_time();
		// 创建html dom树
		$dom->load($pageContents, true, true);
//		log_time();
		// 初始化活动列表
		$act_list = array();

		foreach ($dom->find('table[id=infoTable] tr') as $e) 
		{
			$act = new Activity();
			if ($e->children(1)->children(0)) 
			{
				$act->title = $e->children(1)->children(0)->plaintext;
				$act->school = $e->children(1)->children(1)->plaintext;
				$act->place = $e->children(3)->plaintext;
				$act->stime = $e->children(4)->plaintext;

				$act->title = $act->title;
			}


			$act_list[] = $act;
			
		}
		array_splice($act_list,0,1);
//		var_dump($act_list);
//		echo $act_list[2]->title;
//		e($act_list);
//		log_time();
		return $act_list;
	}

	    // 编码转换函数
    function get_utf8_content($origin_codec)
    {
        return mb_convert_encoding($this->content, "UTF-8", $origin_codec);
    }

	/*********************** 私有区 ****************************/
	private $host_xjh = "xjh.haitou.cc";				//海投宣讲会地址
	private $host_zph = "xyzp.haitou.cc";				//海投招聘地址
} // END class 

/*********************** 测试区 ****************************/
// test
$a = new Haitou_parser();
$a->get_xjh();
?>