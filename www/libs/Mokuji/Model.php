<?php
class Model extends Object
{
	protected $prefix, $table_aliases = array();
	protected $table;
	
	public function __construct()
	{
		//db::connect();
		$config = Environment::getConfig();
		$this->prefix = '';
		$this->startup();
		$this->__after_startup();
	}
	
	protected function startup()
	{
		
	}
	
	protected function __after_startup()
	{
		foreach ($this->table_aliases as $alias => $table) {
			db::addSubst($alias, $this->prefix.$table);
		}
		db::addSubst('table', $this->prefix.$this->table);
	}
	
	public function getDs()
	{
		return db::select('*')->from(':table:')->toDataSource();
	}
    
    public function getCache()
    {
        return Environment::getCache('Models'.$this->getReflection()->getName());
    }

    public function __call($method_name, $args)
    {
        $method_name = '_'.$method_name;
        $tags = array('tags' => array('Models'.$this->getReflection()->getName()));
        if( $this->reflection->hasMethod($method_name))
        {
            $method = $this->reflection->getMethod($method_name);
            if( $method->getAnnotation('cache') == 'update' || $method->getAnnotation('cache') == 'insert'
            || strpos(String::lower($method_name), 'update') > 0 || strpos(String::lower($method_name), 'insert') > 0 || strpos(String::lower($method_name), 'delete') > 0 || strpos(String::lower($method_name), 'edit') > 0
            || strpos(String::lower($method_name), 'save') > 0 || strpos(String::lower($method_name), 'create') > 0)
            {
                fd('cache cleaned');
                if(!empty($args)) $method->invokeArgs($this, $args); 
                else $this->$method_name();
                //WORKAROUND
                $cache_folder = Environment::getVariable('tempDir').'/c-'.$this->cache->namespace;
                if(file_exists($cache_folder)) 
                {
                    $files = glob($cache_folder.'/*');
                    if($files != false)
                    foreach($files as $file)
                    {
                        if(file_exists($file)) unlink($file);
                    }
                }
                //$this->cache->clean();
            }
            else
            {   
                if(!empty($args))
                { 
                    $ckey = sha1($method_name.serialize($args));
                    if(!isset($this->cache[$ckey])) $this->cache->save($ckey, $method->invokeArgs($this, $args)); 
                }
                else
                {
                    $ckey = sha1($method_name);
                    if(!isset($this->cache[$ckey])) $this->cache->save($ckey, $this->$method_name());   
                }
                
                return $this->cache[$ckey];
            }
        } 
    }
}
?>