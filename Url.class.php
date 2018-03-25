<?php

class Url {
	public static function detect($url) {
		foreach(Regex::$extractor as $regex => $replace) {
			if (preg_match($regex, $url, $matches)) {
				return sprintf($replace, $matches[1]);
			}

		}

		return null;
	}
}

?>