<?php 

	    function get_utf8_content($content, $origin_codec)
	    {
	        return mb_convert_encoding($content, "UTF-8", $origin_codec);
	    }

?>