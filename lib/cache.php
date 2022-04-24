<?php

class Cache {
	public $enabled = false;
	public $memcached;

	function __construct($memcachedServers) {
		if (!empty($memcachedServers)) {
			$this->enabled = true;
			$this->memcached = new Memcached();
			foreach ($memcachedServers as $memcachedServer) {
				$this->memcached->addServer($memcachedServer, 11211);
			}
		}
	}

	public function hit($fingerprint, $uncachedContent, $expire = 0) {
		if ($this->enabled) {
			return $this->hitMem($fingerprint, $uncachedContent, $expire);
		} else {
			return $uncachedContent();
		}
	}

	private function hitMem($fingerprint, $uncachedContent, $expire = 0) {
		$hash = hash("xxh128", var_export($fingerprint, true));
		$cached = $this->memcached->get($hash);
		if ($cached !== false) {
			return $cached;
		} else {
			$content = $uncachedContent();
			$this->memcached->set($hash, $content, $expire);
			return $content;
		}
	}

	/**
	 * Delete a key that is a hashed fingerprint created with Cache::hitMem.
	 */
	public function deleteHash($fingerprint) {
		if ($this->enabled) {
			$hash = hash("xxh128", var_export($fingerprint, true));
			$this->memcached->delete($hash);
		}
	}
}

$cache = new Cache($memcachedServers);
