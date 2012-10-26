<?php
/**
 * MokujiCache
 *
 * @author Martin
 * updated to clean namespaces
 */
class MokujiCache extends FileStorage{
    //put your code here

    /** @var string */
    private $dir;

    /** @var bool */
    private $useDirs;

    /** @var resource */
    private $db;

    public function __construct($dir)
    {
            if (self::$useDirectories === NULL) {
                    self::$useDirectories = !ini_get('safe_mode');

                    // checks whether directory is writable
                    $uniq = uniqid('_', TRUE);
                    umask(0000);
                    if (!@mkdir("$dir/$uniq", 0777)) { // intentionally @
                            throw new InvalidStateException("Unable to write to directory '$dir'. Make this directory writable.");
                    }

                    // tests subdirectory mode
                    if (!self::$useDirectories && @file_put_contents("$dir/$uniq/_", '') !== FALSE) { // intentionally @
                            self::$useDirectories = TRUE;
                            unlink("$dir/$uniq/_");
                    }
                    rmdir("$dir/$uniq");
            }

            $this->dir = $dir;
            $this->useDirs = (bool) self::$useDirectories;

            if (mt_rand() / mt_getrandmax() < self::$gcProbability) {
                    $this->clean(array());
            }
    }

    public function clean(array $conds, $namespace = null)
    {
        $all = !empty($conds[Cache::ALL]);
        $collector = empty($conds);

        if($namespace != null)
        {
            $base = $this->dir . DIRECTORY_SEPARATOR . 'c'.$namespace;
            fd($base);
        }

        // cleaning using file iterator
        if ($all || $collector) {
                $now = time();
                $base = $this->dir . DIRECTORY_SEPARATOR . 'c';
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->dir), RecursiveIteratorIterator::CHILD_FIRST);
                foreach ($iterator as $entry) {
                        $path = (string) $entry;
                        if (strncmp($path, $base, strlen($base))) { // skip files out of cache
                                continue;
                        }
                        if ($entry->isDir()) { // collector: remove empty dirs
                                @rmdir($path); // intentionally @
                                continue;
                        }
                        if ($all) {
                                $this->delete($path);

                        } else { // collector
                                $meta = $this->readMeta($path, LOCK_SH);
                                if (!$meta) continue;

                                if (!empty($meta[self::META_EXPIRE]) && $meta[self::META_EXPIRE] < $now) {
                                        $this->delete($path, $meta[self::HANDLE]);
                                        continue;
                                }

                                fclose($meta[self::HANDLE]);
                        }
                }

                if ($all && extension_loaded('sqlite')) {
                        sqlite_exec("DELETE FROM cache", $this->getDb());
                }
                return;
        }

        // cleaning using journal
        if (!empty($conds[Cache::TAGS])) {
                $db = $this->getDb();
                foreach ((array) $conds[Cache::TAGS] as $tag) {
                        $tmp[] = "'" . sqlite_escape_string($tag) . "'";
                }
                $query[] = "tag IN (" . implode(',', $tmp) . ")";
        }

        if (isset($conds[Cache::PRIORITY])) {
                $query[] = "priority <= " . (int) $conds[Cache::PRIORITY];
        }

        if (isset($query)) {
                $db = $this->getDb();
                $query = implode(' OR ', $query);
                $files = sqlite_single_query("SELECT file FROM cache WHERE $query", $db, FALSE);
                foreach ($files as $file) {
                        $this->delete($file);
                }
                sqlite_exec("DELETE FROM cache WHERE $query", $db);
        }
    }

    /**
     * Verifies dependencies.
     * @param  array
     * @return bool
     */
    private function verify($meta)
    {
            do {
                    if (!empty($meta[self::META_DELTA])) {
                            // meta[file] was added by readMeta()
                            if (filemtime($meta[self::FILE]) + $meta[self::META_DELTA] < time()) break;
                            touch($meta[self::FILE]);

                    } elseif (!empty($meta[self::META_EXPIRE]) && $meta[self::META_EXPIRE] < time()) {
                            break;
                    }

                    if (!empty($meta[self::META_CALLBACKS]) && !Cache::checkCallbacks($meta[self::META_CALLBACKS])) {
                            break;
                    }

                    if (!empty($meta[self::META_ITEMS])) {
                            foreach ($meta[self::META_ITEMS] as $depFile => $time) {
                                    $m = $this->readMeta($depFile, LOCK_SH);
                                    if ($m[self::META_TIME] !== $time) break 2;
                                    if ($m && !$this->verify($m)) break 2;
                            }
                    }

                    return TRUE;
            } while (FALSE);

            $this->delete($meta[self::FILE], $meta[self::HANDLE]); // meta[handle] & meta[file] was added by readMeta()
            return FALSE;
    }

    /**
     * Deletes and closes file.
     * @param  string
     * @param  resource
     * @return void
     */
    private static function delete($file, $handle = NULL)
    {
            if (@unlink($file)) { // intentionally @
                    if ($handle) fclose($handle);
                    return;
            }

            if (!$handle) {
                    $handle = @fopen($file, 'r+'); // intentionally @
            }
            if ($handle) {
                    flock($handle, LOCK_EX);
                    ftruncate($handle, 0);
                    fclose($handle);
                    @unlink($file); // intentionally @; not atomic
            }
    }
}
?>
