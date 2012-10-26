<?php
/**
 * Description of Users
 *
 * @author Martin
 */
class GalleryModule implements IMokujiModule {

    private $model;

    public function __construct()
    {
        $this->model = new GalleryModuleModel();
    }
    
    public function install()
    {
        return $this->model->install();
    }
    
    public function getRoutes()
    {
        $routes = array();
        
         $routes[] = new Route('admin/galleries/<action>/<albumId>', array(
                                'module' => 'Gallery',
                                'presenter' => 'Default',
                                'action' => 'default',
                                'albumId' => null
             ));
                        
         /*
        $routes[] = new Route('admin/galleries/<action>', array(
                                'module' => 'Gallery',
                                'presenter' => 'Default',
                                'action' => 'default',
                                'browseAlbum' => null
          
                        ));
          *
          */
        //albumBrowser-browseAlbum
        return $routes;
    }

    public function uninstall()
    {
        return $this->model->uninstall();
    }

    public function getMenuItems()
    {
        return array(
            'Galleries' => array(':Gallery:Default:'),
        );
    }

    public function getActions()
    {
        return array(
            'Edit',    
        );
    }

    public function onStatusChange($new_status)
    {
        if($new_status == 'disabled')
        {
            $cache = Environment::getCache('modules.UsersModule');
            $cache->clean(array(Cache::ALL => TRUE));
        }
    }
    
    public function onLoad()
    {
        LatteMacros::$defaultMacros['gallery'] = '<?php $control->getWidget("gallery")->renderAlbums(%%); ?>';
    }

    public function renderDashboard()
    {
        $template = new Template();
        $template->setFile(dirname(__FILE__).'/templates/dashboard.phtml');
        $template->render();
    }
}