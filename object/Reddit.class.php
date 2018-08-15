<?php

class RedditDl {
	private $curl = null;
	private $jar = null;
	private $page = '';
	private $url = [
		'video' => [],
		'picture' => [],
		'album' => []
	];
	private $limit = null;

	public function __construct(GuzzleHttp\Client $curl, \GuzzleHttp\Cookie\CookieJar $jar) {
		$this->curl = $curl;
		$this->jar = $jar;
	}

	public function getImage(string $url, string $dest, int $limit = null)
	{
		$this->page = $this->get($url);
		$this->limit = $limit;
		$this->dest = $dest;

		$dom = new DOMDocument();
		@$dom->loadHTML($this->page);
		$xpath = new DOMXpath($dom);
		$elements = $xpath->query('//div[@id="siteTable"]/div');
	
		foreach ($elements as $element) {
			foreach ($element->attributes as $attr) {
				if ($attr->nodeName == 'data-url') {
					if (!$this->detect($attr->nodeValue)) {
						echo "Incompatible link:" . $attr->nodeValue . "\n";

						continue;
					}
				}
			}
		}
	}

	private function detect($url) {
		foreach(Regex::$extractor as $regex => $infos) {
			if (preg_match($regex, $url, $matches)) {
				$this->url[$infos['type']][] = sprintf($infos['replace'], $matches[1]);

				// $this->downloadImgurAlbum();

				if (count($this->url['video']) + count($this->url['picture']) >= $this->limit) {
					$this->process();

					exit();
				}

				return true;
			}
		}

		return null;
	}

	private function process() {
		if (!file_exists($this->dest)) {
			mkdir($this->dest, 0755, true);
		}

		$urls = array_slice(array_merge($this->url['video'], $this->url['picture']), 0, $this->limit);

		foreach ($urls as $url) {
			$this->download($url);
		}
	}


	private function downloadImgurAlbum() {
		foreach($this->url['album'] as $album_links) {
			preg_match("/^(?:https?:\/\/)?imgur\.com\/(?:a|gallery)\/(\w+)$/i", $album_links, $matches);

			$json_imgur_api = json_decode($this->get("https://imgur.com/gallery/" . $matches[1] . "/comment/best/hit.json"), true);

			foreach ($json_imgur_api['data']['image']['album_images']['images'] as $an_image) {
				if ($an_image['ext'] == '.mp4') 
					$this->url['video'] = "https://i.imgur.com/".$an_image['hash'].".mp4";
				else
					$this->url['picture'] = "https://i.imgur.com/".$an_image['hash'].".jpg";
			}
		}

		$this->url['album'] = [];
	}

	private function download($img_url) {
		$this->curl->request('GET', $img_url, [
			'headers' => [
				'User-Agent' => UserAgent::get(),
			],
			'sink' => $this->dest . '/' . end(explode('/', parse_url($img_url, PHP_URL_PATH))),
		]);
	}

	private function get($url, $cookie = true) {
		$res = $this->curl->request('GET', $url, [
			'headers' => [
				'User-Agent' => UserAgent::get(),
			],
			'cookies' => $cookie ? $this->jar : null,
		]);

		if ($res->getStatusCode() != 200) {
			echo $res->getReasonPhrase() . "\n";
			exit();
		}

		return $res->getBody()->getContents();
	}
}