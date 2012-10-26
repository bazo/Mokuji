<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminUsersPresenter
 *
 * @author Martin
 */
class Gallery_DefaultPresenter extends AdminBaseModulePresenter{

    //private $edit = false;
    private $model;
    static $invalidLinkMode = Presenter::INVALID_LINK_WARNING;
    public function startup()
    {
        parent::startup();
        $this->model = new GalleryModuleModel();
    }
    
    public function createComponentGallery($name)
    {
        $browser = new Gallery($this, $name);
        $browser->photoFolder = APP_DIR.'/Data/Photos/';
        return $browser;
    }

    public function createComponentUploader($name)
    {
        $uploader = new Uploader($this, $name);
        $uploader->maxFileSize = '10MB';
        return $uploader;
    }


   public function renderPhotos($albumId)
    {
        $this->template->albumId = $albumId;
    }
    
}
?>