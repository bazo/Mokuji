<?php
/**
 * Uploader
 *
 * @author Martin
 */
class Uploader extends Control{

    private $runtimes = 'html5, gears, flash, silverlight';
    private $maxFileSize = '1MB';
    private $chunkSize = '1MB';
    private $uniqueNames = false;
    private $resize = false;
    private $resizeOptions = array('width' => 320, 'height' => 240, 'quality' => 90);
    private $filters = array();
    private $flashSwfUrl;
    private $silverlightXapUrl;
    /*
     * @var CssLoader
     */
    public $templateFile = 'uploader.phtml';

    public function  __construct($parent, $name)
    {
        parent::__construct($parent, $name);
        Debug::barDump(ini_get_all());
        $this->chunkSize = ini_get('upload_max_filesize');
    }

    protected function createTemplate()
    {
        $template = parent::createTemplate();
        $template->setFile(dirname(__FILE__) . '/'.$this->templateFile);
        return $template;
    }

    public function render()
    {
        $this->template->max_file_size = $this->maxFileSize;
        $this->template->chunk_size = $this->chunkSize;
        $this->template->unique_names = $this->uniqueNames;
        $this->template->render();
    }

    public function handleUpload($name)
    {
        fd($_REQUEST);
        fd($_FILES);
        //header('Content-type: text/plain; charset=UTF-8');
	//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	//header("Cache-Control: no-store, no-cache, must-revalidate");
	//header("Cache-Control: post-check=0, pre-check=0", false);
	//header("Pragma: no-cache");

	// Settings
	$targetDir = ini_get("upload_tmp_dir");
	$cleanupTargetDir = false; // Remove old files
	$maxFileAge = 60 * 60; // Temp file age in seconds

	// 5 minutes execution time
	//@set_time_limit(5 * 60);
	// usleep(5000);

	// Get parameters
	$chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
	$chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
	$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

	// Clean the fileName for security reasons
	$fileName = preg_replace('/[^\w\._]+/', '', $fileName);

	// Create target dir
	if (!file_exists($targetDir))
		@mkdir($targetDir);


	// Remove old temp files
        /*
	if (is_dir($targetDir) && ($dir = opendir($targetDir)))
        {
		while (($file = readdir($dir)) !== false) {
			$filePath = $targetDir . DIRECTORY_SEPARATOR . $file;

			// Remove temp files if they are older than the max age
			if (preg_match('/\\.tmp$/', $file) && (filemtime($filePath) < time() - $maxFileAge))
				@unlink($filePath);
		}

		closedir($dir);
	} else
		die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
                */
	// Look for the content type header
	if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
		$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

	if (isset($_SERVER["CONTENT_TYPE"]))
		$contentType = $_SERVER["CONTENT_TYPE"];

	if (strpos($contentType, "multipart") !== false) {
		if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
			// Open temp file
			$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen($_FILES['file']['tmp_name'], "rb");

				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

				fclose($out);
				unlink($_FILES['file']['tmp_name']);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	} else {
		// Open temp file
		$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
		if ($out) {
			// Read binary input stream and append it to temp file
			$in = fopen("php://input", "rb");

			if ($in) {
				while ($buff = fread($in, 4096))
					fwrite($out, $buff);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

			fclose($out);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
	}

	// Return JSON-RPC response
	die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
        $this->presenter->terminate();
    }

    /*GETTERS AND SETTERS*/

    /**
     * comma separated list of runtimes
     * @param string $runtimes
     */
    public function setRuntimes($runtimes)
    {
        $this->runtimes = $runtimes;
    }

    /**
     * @return string
     */
    public function getRuntimes()
    {
        return $this->runtimes;
    }

    public function getMaxFileSize() {
        return $this->maxFileSize;
    }

    public function setMaxFileSize($maxFileSize) {
        $this->maxFileSize = $maxFileSize;
    }

    public function setTemplateFile($templateFile) {
        $this->templateFile = $templateFile;
    }



    public function getChunkSize() {
        return $this->chunkSize;
    }


   public function setChunkSize($chunkSize) {
        $this->chunkSize = $chunkSize;
    }


   public function getUniqueNames() {
        return $this->uniqueNames;
    }


   public function setUniqueNames($uniqueNames)
   {
       $this->uniqueNames = $uniqueNames;
   }


   public function getResize()
   {
       return $this->resize;
   }


   public function setResize($resize)
   {
       $this->resize = $resize;
   }

   public function getResizeOptions()
   {
       return $this->resizeOptions;
   }


   public function setResizeOptions($resizeOptions)
   {
       $this->resizeOptions = $resizeOptions;
   }


   public function getFilters()
   {
       return $this->filters;
   }


   public function setFilters($filters)
   {
       $this->filters = $filters;
   }


   public function getFlashSwfUrl()
    {
       return $this->flashSwfUrl;
    }


   public function setFlashSwfUrl($flashSwfUrl)
   {
       $this->flashSwfUrl = $flashSwfUrl;
   }


   public function getSilverlightXapUrl()
   {
       return $this->silverlightXapUrl;
   }


   public function setSilverlightXapUrl($silverlightXapUrl)
   {
       $this->silverlightXapUrl = $silverlightXapUrl;
   }
}
?>
