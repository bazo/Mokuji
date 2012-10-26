<?php
class db extends dibi{
    /*
	public static function connect($settings = array(), $connection_name = null)
	{
	  $config = Environment::getConfig();
	  foreach ($config['database'] as $connection_name => $settings)
	  {
    	try
			{
			  	dibi::connect($settings, $connection_name);
                                fd('connected');
			  	if ($settings['profiler'] == true ) dibi::getProfiler()->setFile(APP_DIR.'/log/db.txt');
			}
			catch (DibiException $e)
			{
				echo get_class($e), ': ', $e->getMessage(), "\n";
			}
		}
	}
     * */
}
?>