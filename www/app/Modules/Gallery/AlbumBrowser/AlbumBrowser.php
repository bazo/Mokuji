<?php
/**
 * AlbumBrowser
 *
 * @author Martin
 */
class Gallery extends Control{
    //put your code here

    private $edit = null, $model;
    public $photoFolder;
    public $width =150, $height = 100;
    /**
     * @persistent
     */
    public $albumId = null;

    public function __construct(IComponentContainer $parent = NULL, $name = NULL) {
        parent::__construct($parent, $name);
        $this->model = new GalleryModuleModel();
        $this->photoFolder = APP_DIR.'/Data/Photos/';

    }

    protected function createTemplate()
    {
        $template = parent::createTemplate();
        $template->setFile(dirname(__FILE__) . '/albumbrowser.phtml');
        return $template;
    }

    public function handleBrowsePhotos($albumId)
    {
        $this->redirect('this', array('albumId' => $albumId));
    }

    public function handleAddPhoto()
    {
        $this->edit = true;
        $this->template->browser = 'photos';
        $this->invalidateControl('popup');
    }

    public function handleNewAlbum()
    {
        $this->edit = true;
        $this->template->browser = 'albums';
        $this->invalidateControl();
    }

    public function createComponentAlbumForm($name)
    {
        $form = new LiveForm($this, $name);
        $form->addText('name', 'Name')->addRule(Form::FILLED, 'Please enter a album name');
        $form->addTextArea('description', 'Description');
        $form->addButton('btnClose', 'Close');
        $form->addSubmit('btnSubmit', 'OK')->onClick[] = callback($this, 'AlbumFormSubmitted');
        return $form;
    }

    public function AlbumFormSubmitted($button)
    {
        $values = $button->getForm()->getValues();
        unset($values['btnClose']);
        try{
           $this->model->createAlbum($values);
           $this->presenter->flash('Album '.$values['name'].' created');
           $this->invalidateControl('albums');
        }
        catch (DibiDriverException $e)
        {
            $this->presenter->flash($e->getMessage());
        }

    }

    public function createComponentPhotoForm($name)
    {
        $form = new LiveForm($this, $name);
        $form->getElementPrototype()->class = 'ajax_uploader';
        $form->addFile('photo', 'Photo')->getControlPrototype()->class('multi');
        $form->addText('name', 'Name')->addRule(Form::FILLED, 'Please enter a album name');
        $form->addTextArea('description', 'Description');
        $form->addButton('btnClose', 'Close');
        $form->addSubmit('btnSubmit', 'OK')->onClick[] = callback($this, 'PhotoFormSubmitted');
        $form->addHidden('album_id')->setValue($this->getParam('albumId'));
        return $form;
    }

    public function PhotoFormSubmitted($button)
    {
        $values = $button->getForm()->getValues();
        unset($values['btnClose']);
        try{
           $file = $values['photo'];
            if($file->isOK())
            {

                $file->move($this->photoFolder.$file->getName());
                unset($values['photo']);
                $values['file'] = $file->getName();
                $this->model->savePhotos($values);
            }
           $this->presenter->flash('Photo '.$values['name'].'uploaded');
           $this->invalidateControl();
           $this->redirect('this');
        }
        catch(InvalidStateException $e)
        {
            $this->presenter->flash($e->getMessage());
        }
        catch (DibiDriverException $e)
        {
            $this->presenter->flash($e->getMessage());
        }

    }

    public function createComponentCss($name)
    {
        $css = parent::createComponentCss($name);
        $css->sourcePath = dirname(__FILE__).'/../css' ;
        return $css;
    }

    public function createComponentJs($name)
    {
        $js = parent::createComponentJs($name);
        $js->sourcePath = dirname(__FILE__).'/../js' ;
        return $js;
    }

    public function handleOutputPhoto($file)
    {
      $image = Image::fromFile($this->photoFolder.$file);
      $image->resize($this->width, $this->height);
      $image->sharpen();
      $image->send();
    }

    public function handleOutputCoverPhoto($file)
    {
        $image = Image::fromFile($this->photoFolder.$file);
        $image->resize(75, 50);
        $image->sharpen();
        $image->send();
        exit;
    }
    /*
     * used for frontend album photos rendering
     */
    public function renderPhotos($album_name)
    {
        $this->template->photos = $this->model->getPhotosByAlbumName($album_name);
        $this->template->setFile(dirname(__FILE__) . '/frontphotos.phtml');
        return $this->template->render();
    }

    /*
     * used for frontend albums rendering
     */
    public function renderAlbums()
    {
        $params = $this->getParam();
        if(isset($params['albumId']))
        {
            $album_name = $this->model->getAlbumName($params['albumId']);
            return $this->renderPhotos($album_name['name']);
        }
        $albums = $this->model->getAlbums();
        foreach($albums as $index => $album)
        {
            $albums[$index]['cover_photos'] = $this->model->getCoverPhotos($album->id, 4);
        }
        $this->template->albums = $albums;
        $this->template->setFile(dirname(__FILE__) . '/frontalbums.phtml');
        return $this->template->render();
    }

    public function render()
    {
        $params = $this->getParam();
        $this->template->edit = $this->edit;
        if(isset($params['albumId']))
        {
            $this->template->browser = 'photos';
            $album_id = $params['albumId'];
            $this->template->link = $this->createAddPhotoLink();
            $this->template->photos = $this->model->getPhotos($album_id);
        }
        else
        {
            $this->template->browser = 'albums';
            $this->template->link = $this->createAddAlbumLink();
            $albums = $this->model->getAlbums();
            foreach($albums as $index => $album)
            {
                $albums[$index]['cover_photos'] = $this->model->getCoverPhotos($album->id, 4);
            }
            $this->template->albums = $albums;
        }
        return $this->template->render();
    }

    public function createAddAlbumLink()
    {
        $link = Html::el('a')->href($this->link('newAlbum!'))->class('ajax')->add(Html::el('div')->id('create-new-link')->add(Html::el('div')->id('icon'))->add(Html::el('span')->add('Create new album')));
        return $link;
    }

    public function createAddPhotoLink()
    {
        $link = Html::el('a')->href($this->link('addPhoto!'))->class('ajax')->add(Html::el('div')->id('create-new-link')->add(Html::el('div')->id('icon'))->add(Html::el('span')->add('Add photo')));
        return $link;
    }
}
?>
