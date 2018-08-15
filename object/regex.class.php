<?php

class Regex {
	private static $subreddit = '/^(?!(?:blog|about|code|advertising|jobs|rules|wiki|contact|buttons|gold|page|help|prefs|user|message|widget))(?:[\w]+)$/i';
	public static $extractor = [
		'/^(?:https?:\/\/)?i\.redd\.it\/(\w+\.(?:jpg|png|gif))$/i' => ['replace' => 'https://i.redd.it/%s', 'type' => 'picture'],
		'/^(?:https?:\/\/)?(?:i\.)?imgur\.com\/(\w+)\.(?:jpg|png)?$/i' => ['replace' => 'https://i.imgur.com/%s.jpg', 'type' => 'picture'],
		'/^(?:https?:\/\/)?(?:i\.)?imgur\.com\/(\w+)\.(?:gifv|gif)?$/i' => ['replace' => 'https://i.imgur.com/%s.mp4', 'type' => 'video'],
		'/^(?:https?:\/\/)?gfycat\.com(?:\/gifs\/detail)?\/(\w+)$/i' => ['replace' => 'https://giant.gfycat.com/%s.mp4', 'type' => 'video'],
		'/^(?:https?:\/\/)?imgur\.com\/(?:a|gallery)\/(\w+)$/i' => ['replace' => 'https://imgur.com/a/%s', 'type' => 'album'],
		'/^((?:https?:\/\/)?.*\/(?:\w+)\.(?:png|jpg|jpeg))$/i' => ['replace' => '%s', 'type' => 'picture'],
		'/^((?:https?:\/\/)?.*\/(?:\w+)\.(?:mp4|gifv|mkv|gif))$/i' => ['replace' => '%s', 'type' => 'video'],
	];

	public static function __callStatic($name, $arguments)
	{
		return preg_match(self::$$name, array_shift($arguments));
	}
}

?>