<?php
class HLfuncs {
	public static function svg($class="dirty", $type="circle", $alt="", $use=""){
		if($alt === "" || (strlen(trim($alt)) < 1) ){
			$alt = $class;
		}
	
		if($use === ""){
			$use = $type . "1";
		}
	
		return "<svg class='icon $class $type' id='icon_{$class}_{$type}' title='$alt'><use xlink:href='#{$use}' alt='$alt' /></svg>";
	}
	public static function label($text="", $class="dirty", $type="circle", $alt=""){
		return "<span class='label $class' id='label_{$class}_{$type}' title='$alt'>$text</span> \n";
	}
	public function item($text="", $class="dirty", $type="circle", $alt="", $use=""){
		return self::svg($class, $type, $alt, $use) . $this->label($text, $class, $type, $alt);
	}
	
	public function __construct($file="../images/header-sprite.svg") {
		include_once($file);
	}
}