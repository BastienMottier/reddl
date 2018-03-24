<?php

class Regex {
	private static $subreddit = '/^(?!(?:blog|about|code|advertising|jobs|rules|wiki|contact|buttons|gold|page|help|prefs|user|message|widget))(?:[\w]+)$/i';

	public static function __callStatic($name, $arguments)
	{
		return preg_match(self::$$name, array_shift($arguments));
	}
}

?>