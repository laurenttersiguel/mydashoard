<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {

       $form = new Application_Form_Auth();
       $form->send->setLabel('Authentificate');
       $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
      
              $formData = $this->getRequest()->getPost();
              if ($form->isValid($formData)) {

                    $user = $form->getValue('user');
                    $password = $form->getValue('password');
                    $this->redirect('graph');

              } else {

                    $form->populate($formData);

              }
        
        }
        
    }
    
 
}

