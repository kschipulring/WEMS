<?php
class utilities{
	//converting prefix type to DB type
	public static function getDBtype($pre){
		$type = "S";
		
		switch($pre){
			case "l":
				$type = "S";
				break;
			case "i":
				$type = "I";
				break;
			case "pl":
				$type = "P";
				break;
			case "si":
				$type = "T";
				break;
		}
		
		return $type;
	}
	
	//useful for converting strings like 'my_dashed_string' to 'myDashedString'
	public static function dashesToCamelCase($string, $capitalizeFirstCharacter = false){
		$str = str_replace('_', '', ucwords($string, '_'));
	
		if (!$capitalizeFirstCharacter) {
			$str = lcfirst($str);
		}
	
		return $str;
	}
	
	//What browser is this webpage being presented on?
	public static function getBrowser( $cssFriendly=false ){
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') ) {
			if (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')) {
				$browser = 'Opera (MSIE/Opera/Compatible)';
			} else {
				$browser = 'Internet Explorer (MSIE/Compatible)';
			}
		} else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko')) {
			if (strpos($_SERVER['HTTP_USER_AGENT'], 'Netscape')) {
				$browser = 'Netscape (Gecko/Netscape)';
			} else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
				$browser = 'Mozilla Firefox (Gecko/Firefox)';
			} else {
				$browser = 'Mozilla (Gecko/Mozilla)';
			}
		} else {
			$browser = 'Others browsers';
		}
		
		if( $cssFriendly ){
			$browser = trim( preg_replace("/\([\w\s\/]+\)/", "", $browser) );
			
			$browser = str_replace(" ", "_", $browser);
		}
		
		return $browser;
	}
}