<?php

$jar = new \GuzzleHttp\Cookie\CookieJar();
$jar = \GuzzleHttp\Cookie\CookieJar::fromArray(
    [
        'over18' => '1',
    ],
    '.reddit.com'
);

$curl = new GuzzleHttp\Client();

$RedditDl = new RedditDl($curl, $jar);
