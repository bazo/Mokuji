<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Front_TestPresenter
 *
 * @author Martin
 */
class Front_TestPresenter extends Front_BasePresenter{
    //put your code here
    public function actionDefault($test)
    {
        var_dump(func_get_args());
        echo 'action='.$test;
    }

    public function renderDefault($test)
    {
        echo 'render='.$test;
        $this->terminate();
    }
}
?>
