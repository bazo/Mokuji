<?php

require_once 'MokujiCache.php';

class MokujiLoader extends RobotLoader
{
	/** @var array */
	public $list = array();

	/** @var array */
	private $timestamps;

	/** @var bool */
	private $rebuilded = FALSE;

	/** @var string */
	private $acceptMask;

	/** @var string */
	private $ignoreMask;
   
	/**
	 * Register autoloader.
	 * @return void
	 */
	public function register()
	{
		$cache = $this->getCache();
		$key = $this->getKey();
		if (isset($cache[$key])) {
			$this->list = $cache[$key];
		} else {
			$this->rebuild();
		}

		if (isset($this->list[strtolower(__CLASS__)]) && class_exists('NetteLoader', FALSE)) {
			NetteLoader::getInstance()->unregister();
		}

		parent::register();
	}

	/**
	 * Scan a directory for PHP files, subdirectories and 'netterobots.txt' file.
	 * @param  string
	 * @return void
	 */
	private function scanDirectory($dir)
	{
		$iterator = dir($dir);
		if (!$iterator) return;

		$disallow = array();
		if (is_file($dir . '/netterobots.txt')) {
			foreach (file($dir . '/netterobots.txt') as $s) {
				if (preg_match('#^disallow\\s*:\\s*(\\S+)#i', $s, $m)) {
					$disallow[trim($m[1], '/')] = TRUE;
				}
			}
			if (isset($disallow[''])) return;
		}

		while (FALSE !== ($entry = $iterator->read())) {
			if ($entry == '.' || $entry == '..' || isset($disallow[$entry])) continue;

			$path = $dir . DIRECTORY_SEPARATOR . $entry;

			// process subdirectories
			if (is_dir($path)) {
				// check ignore mask
				if (!preg_match($this->ignoreMask, $entry)) {
					$this->scanDirectory($path);
				}
				continue;
			}

			if (is_file($path) && preg_match($this->acceptMask, $entry)) {
				$time = filemtime($path);
				if (!isset($this->timestamps[$path]) || $this->timestamps[$path] !== $time) {
					$this->timestamps[$path] = $time;
					$this->scanScript($path);
				}
			}
		}

		$iterator->close();
	}

	/**
	 * Analyse PHP file.
	 * @param  string
	 * @return void
	 */
	private function scanScript($file)
	{
		if (!defined('T_NAMESPACE')) {
			define('T_NAMESPACE', -1);
			define('T_NS_SEPARATOR', -1);
		}

		$expected = FALSE;
		$namespace = '';
		$level = 0;
		$s = file_get_contents($file);

		if (preg_match('#//nette'.'loader=(\S*)#', $s, $matches)) {
			foreach (explode(',', $matches[1]) as $name) {
				$this->addClass($name, $file);
                                $this->class_list[$name] = $file;
			}
			return;
		}

		foreach (token_get_all($s) as $token)
		{
			if (is_array($token)) {
				switch ($token[0]) {
				case T_NAMESPACE:
				case T_CLASS:
				case T_INTERFACE:
					$expected = $token[0];
					$name = '';
					continue 2;

				case T_COMMENT:
				case T_DOC_COMMENT:
				case T_WHITESPACE:
					continue 2;

				case T_NS_SEPARATOR:
				case T_STRING:
					if ($expected) {
						$name .= $token[1];
					}
					continue 2;
				}
			}

			if ($expected) {
				if ($expected === T_NAMESPACE) {
					$namespace = $name . '\\';
				} elseif ($level === 0) {
					$this->addClass($namespace . $name, $file);
				}
				$expected = FALSE;
			}

			if (is_array($token)) {
				if ($token[0] === T_CURLY_OPEN || $token[0] === T_DOLLAR_OPEN_CURLY_BRACES) {
					$level++;
				}
			} elseif ($token === '{') {
				$level++;
			} elseif ($token === '}') {
				$level--;
			}
		}
	}

	/**
	 * Converts comma separated wildcards to regular expression.
	 * @param  string
	 * @return string
	 */
	private static function wildcards2re($wildcards)
	{
		$mask = array();
		foreach (explode(',', $wildcards) as $wildcard) {
			$wildcard = trim($wildcard);
			$wildcard = addcslashes($wildcard, '.\\+[^]$(){}=!><|:#');
			$wildcard = strtr($wildcard, array('*' => '.*', '?' => '.'));
			$mask[] = $wildcard;
		}
		return '#^(' . implode('|', $mask) . ')$#i';
	}

	/**
	 * @return Cache
	 */
	protected function getCache()
	{
		return Environment::getCache('Mokuji.Loader');
	}
}
