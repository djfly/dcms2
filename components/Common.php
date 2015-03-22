<?php
/**
 * @link http://www.cmsboom.com/
 * @author ff <gitday@qq.com>
 * @license http://www.cmsboom.com/license/
 */

namespace app\components;
use Yii;
use yii\imagine\Image;

class Common
{
	public static function setLanguage($language='')
	{
		$options = ['name'=>'language','value'=>$language,'expire'=>time()+86400*365];
		$cookie = new \yii\web\Cookie($options);
		Yii::$app->response->cookies->add($cookie);
	}

	public static function getLanguage()
	{
		if (Yii::$app->request->cookies['language']) {
		    return Yii::$app->request->cookies->getValue('language');
		}else{
			return false;
		}
	}

	public static function random($length, $numeric = 0) {
		$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
		$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
		if($numeric) {
			$hash = '';
		} else {
			$hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
			$length--;
		}
		$max = strlen($seed) - 1;
		for($i = 0; $i < $length; $i++) {
			$hash .= $seed{mt_rand(0, $max)};
		}
		return $hash;
	}

	public static function url_to_dir($url)
	{
	    $dir_cur="";
	    $str=$_SERVER["SERVER_SOFTWARE"];
	    if(is_int(strpos($str,"pache"))===true){
	        $dir_cur="/";
	    }
	    elseif(is_int(strpos($str,"IIS"))===true){
	        $dir_cur="\\";
	    }else{
	        $dir_cur="/";
	    }
	    $url=str_replace("/",$dir_cur,$url);
	    $url=$_SERVER['DOCUMENT_ROOT'].$url;
	    return $url;
	}

	public static function unescape($str)
	{ 
		$ret = ''; 
		$len = strlen($str); 
		for ($i = 0; $i < $len; $i++){ 
			if ($str[$i] == '%' && $str[$i+1] == 'u'){ 
				$val = hexdec(substr($str, $i+2, 4)); 
				if ($val < 0x7f){ 
					$ret .= chr($val); 
				} else if($val < 0x800) {
					$ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
				} else{
					$ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
				}  
				$i += 5; 
			} 
			else if ($str[$i] == '%'){ 
				$ret .= urldecode(substr($str, $i, 3)); 
				$i += 2; 
			}else {
				$ret .= $str[$i]; 
			}
		} 
		return $ret; 
	}

	public static function formatTime($time)
	{
	    $t = time() - $time;
	    $f = array(
	        '31536000' => Yii::t("app", 'Years'),
	        '2592000' => Yii::t("app", 'Months'),
	        '604800' => Yii::t("app", 'Weeks'),
	        '86400' => Yii::t("app", 'Days'),
	        '3600' => Yii::t("app", 'Hours'),
	        '60' => Yii::t("app", 'Minutes'),
	        '1' => Yii::t("app", 'Seconds')
	    );
	    foreach ($f as $k => $v) {
	        if (0 != $c = floor($t / (int)$k)) {
	            $m = floor($t % $k);
	            foreach ($f as $x => $y) {
	                if (0 != $r = floor($m / (int)$x)) {
	                    return $c.$v.$r.$y.Yii::t("app", 'ago');
	                }
	            }
	            return $c.$v.Yii::t("app", 'ago');
	        }
	    }
	}

	public static function tranTime($time)
	{     
	    $rtime = date("m-d H:i",$time);     
	    $htime = date("H:i",$time);           
	    $time = time() - $time;       
	    if ($time < 60)
	    {         
	        $str = '刚刚';     
	    }elseif($time < 60 * 60){         
	        $min = floor($time/60);         
	        $str = $min.'分钟前';     
	    }elseif($time < 60 * 60 * 24){         
	        $h = floor($time/(60*60));         
	        $str = $h.'小时前 '.$htime;     
	    }elseif($time < 60 * 60 * 24 * 3){         
	        $d = floor($time/(60*60*24));         
	        if($d==1){
	            $str = '昨天 '.$rtime;
	        }else{
	            $str = '前天 '.$rtime;     
	        }
	    }else{         
	        $str = $rtime;     
	    }     
	    return $str; 
	}
}
