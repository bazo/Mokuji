<?php
class ModuleManager
{
    public static function register($module_name)
    {
        $installed = self::checkInstall($module_name);
        if($installed === false || $installed === null) 
        {
            $module_class = $module_name.'Module';
            $module = new $module_class();  
            try{
                $result = $module->install();
                fd($result);
                if($result != false)
                {
                    Admin_ModulesModel::add($module_name);
                    $cache = Environment::getCache($module_name.'Module');
                    $cache->save('installed', true);
                }    
            } 
            catch(Exception $e)
            {
                
            } 
        }    
    }
    
    public static function checkInstall($module_name)
    {
         $cache = Environment::getCache('modules.'.$module_name.'Module');
         if($cache->offsetGet('installed') == true) return true;
         else
         {
             $result = Admin_ModulesModel::checkInstall($module_name);
             if($result != false)
             {
                $cache = Environment::getCache('modules.'.$module_name.'Module');
                $cache->save('installed', true);
                return $cache->offsetGet('installed');
             }
         }
        
    }
    
    public static function getStatus($module_name)
    {
        $cache = Environment::getCache('modules.'.$module_name.'Module');
        if(isset($cache['status'])) return $cache['status'];
        else
        {
            $status = Admin_ModulesModel::getStatus($module_name);
            if($module_name = 'Gallery') var_dump($status);
            if($status == false) $cache->save('status', $status);
            else $cache->save('status', $status->status);
            return $cache->offsetGet('status');
        }
    }
    
    public static function changeStatus($module_name, $new_status)
    {
        Admin_ModulesModel::changeStatus($module_name, $new_status);
        $cache = Environment::getCache('modules.'.$module_name.'Module');
        $cache->save('status', $new_status); 
    }
}
?>
