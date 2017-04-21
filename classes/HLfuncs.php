<?php
class HLfuncs {
	private static $initialized = false;
	
	public static function svg($class="dirty", $type="circle", $alt="", $use=""){
		if($alt === "" || (strlen(trim($alt)) < 1) ){
			$alt = $class;
		}
	
		if($use === ""){
			$use = $type . "1";
		}
	
		return "<svg class='icon $class $type' id='icon_{$class}_{$type}'><use xlink:href='#{$use}' alt='$alt' /></svg>";
	}
	public static function label($text="", $class="dirty", $type="circle"){
		return "<span class='label $class' id='label_{$class}_{$type}'>$text</span> \n";
	}
	public static function item($text="", $class="dirty", $type="circle", $alt="", $use=""){
		if(self::$initialized === false){
			self::init();
			
			self::$initialized = true;
		}
		
		return self::svg($class, $type, $alt, $use) . self::label($text, $class, $type);
	}
	
	public static function init() {
		include_once("../images/header-sprite.svg");
	}
}