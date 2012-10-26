<?php
/**
 * Photon CMS
 *
 * @copyright  Copyright (c) 2009 Martin Bazik
 * @package    Receptar
 */
/**
 * Homepage presenter.
 *
 * @author     Martin Bazik
 * @package    Receptar
 */
class Front_PagePresenter extends Front_BasePresenter
{

    public function actionCategoryView($category)
    {
        try{
            $this->data = $this->model('pages')->getBySlug($category);
            if ($this->data == false)
            {
                $this->data = $this->model('pages')->getByCategory($category);
                $this->view = 'category-'.$this->data->category->template;
            }
            else $this->view = $this->data->content_type.'-'.$this->data->template;
        }
        catch(Exception $e)
        {
            $this->template->error = $e->getMessage();
            $this->view= 'error';
        }
    }

    public function actionPageView($category, $page)
    {
        try
        {
            $this->data = $this->model('pages')->getBySlug($page);
            $this->view = $this->data->content_type.'-'.$this->data->template;
        }
        catch(Exception $e)
        {
            $this->template->error = $e->getMessage();
            $this->view= 'error';
        }
    }

    public function actionIdView($id)
    {
        try
        {
            $this->data = $this->model('pages')->getById($id);
            $this->view = $this->data->content_type.'-'.$data->template;
        }
        catch(Exception $e)
        {
            $this->template->error = $e->getMessage();
            $this->template;
            $this->view= 'error';
        }
    }

    public function actionPreview($values)
    {
        $this->data = (object)$values;
        $this->view = $this->data->content_type.'-'.$this->data->template;
    }
}