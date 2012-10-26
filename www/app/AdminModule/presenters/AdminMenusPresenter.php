<?php
class Admin_MenusPresenter extends Admin_SecurePresenter
{
	
	private $new = false, $edit =false;
	
	public function actionDefault()
	{
            $this->template->new = $this->new;
            $this->template->edit = $this->edit;
            $this->template->link = $this->createAddNewLink();
	}
	
	public function createAddNewLink()
	{
            $link = Html::el('a')->href($this->link('newMenu!'))->class('ajax')->add(Html::el('div')->id('create-new-link')->add(Html::el('div')->id('icon'))->add(Html::el('span')->add('Create new')));
            return $link;
	}
	
	public function createComponentFormNewMenu($name)
	{
            $form = new LiveForm($this, $name);
            $form->addText('name', 'Name')->addRule(Form::FILLED, 'Please enter a name.');
            $templates = $this->model('Menu')->getTemplates();
            $form->addSelect('template', 'Template', $templates);
            $form->addButton('btnClose','Close');
            $form->addSubmit('save','Save')->onClick[] = array($this, 'formNewMenuSubmitted');
            $form['save']->getControlPrototype()->class('ajax');
            return $form;
	}
	
	public function createComponentFormEditMenu($name)
	{
            $menu_id = $this->getParam('id');
            
            $templates = $this->model('Menu')->getTemplates();
            $data = $this->model('Menu')->getById($menu_id);

            $form = new LiveForm($this, $name);
            $form->addText('name', 'Name')->addRule(Form::FILLED, 'Please enter a name.')->setValue($data->name);
            $form->addSelect('template', 'Template', $templates);
            $form->addHidden('id')->setValue($menu_id); 
            $form->setDefaults($data);
            $form->addButton('btnClose','Close');
            $form->addSubmit('save','Save')->onClick[] = array($this, 'formEditMenuSubmitted');
            return $form;
	}
    
	public function formNewMenuSubmitted($button)
	{
            $form = $button->getForm();
            $values = $form->getValues();
            $name = $values['name'];
            unset($values['btnClose']);
            try
            {
                $this->model('menu')->createMenu($values);
                $this->flash('Menu '.$name.' created successfully');
                $this->invalidateControl('form');
                $this->invalidateControl('grid');
            }
            catch (DibiDriverException $e)
            {
                $this->flash($e->getMessage());
            }
	}

        public function formEditMenuSubmitted($button)
	{
            $form = $button->getForm();
            $values = $form->getValues();
            $name = $values['name'];
            unset($values['btnClose']);
            try
            {
                $this->model('menu')->update($values);
                $this->flash('Menu '.$name.' updated successfully');
                $this->invalidateControl('form');
                //$this->redirect('MenuItems:Default', array('name' => $name));
                $this->invalidateControl('grid');
            }
            catch (DibiDriverException $e)
            {
                $this->flash($e->getMessage());
            }
	}
	
	public function formMenuClose($button)
	{
            $this->invalidateControl('form');
	}
	
	public function createComponentGrid($name)
	{
            $grid = new DataGrid($this, $name);
            $ds = $this->model('menu')->getDs();
            $grid->bindDataTable($ds);

            $grid->keyName = 'id';
            $grid->addColumn('name', 'Name')->addTextFilter();
            $grid['name']->formatCallback[] = array($this, 'createNameLink');
            // přidáme sloupec pro akce
            $grid->addActionColumn('Actions');

            // a naplníme datagrid akcemi pomocí továrničky
            $grid->addAction('Items', 'MenuItems:default', Html::el('span')->class('icon icon-items'), $useAjax = true);
            $grid->addAction('Edit', 'editMenu!', Html::el('span')->class('icon icon-edit'), $useAjax = true);
            $grid->addAction('Delete', 'confirmForm:confirmDelete!', Html::el('span')->class('icon icon-delete'), $useAjax = true);
            return $grid;
	} 	
	
	public function createNameLink($value, DibiRow $data)
	{
            return Html::el('a',$value)->class('ajax')->href($this->link('MenuItems:default', array('id' => $data['id'])));
	}
	
	function createComponentConfirmForm()
        {
            $form = new ConfirmationDialog();
            $form->addConfirmer(
                            'delete', // název signálu bude 'confirmDelete!'
                            array($this, 'deleteMenu'), // callback na funkci při kliku na YES
                            array($this, 'questionDelete') // otázka (může být i callback vracející string)
                            );
            return $form;
	}
	
	public function questionDelete($dialog, $params)
	{
            return 'Really delete menu '.$params['name'].'?';
	}
	
	public function deleteMenu($params, $dialog)
	{
            $name = $params;
            try{
                    $menu_id = $this->model('menu')->getByName($name)->id;
                    $this->model('menu')->deleteById($menu_id);
                    $this->flash('Menu '.$name.' deleted!');
                    $this->invalidateControl('grid');
            }
            catch(DibiDriverException $e)
            {
                    $this->flash($e->getMessage());
            }
	}
	
	public function handleNewMenu()
	{
            $this->template->new = true;

            $this->invalidateControl('form');
            $this->flashCmd('openForm');
	}
	
	public function handleEditMenu($name)
	{
            $this->template->menu = $name;
            $this->template->edit = true;
            $this->invalidateControl('form');
            $this->flashCmd('openForm');	
	}
}
?>
