<?php
    class FormExtranetUserGroupAssociate extends Cible_Form
    {
        public function __construct($options = null, $groupsData = array()) 
        {
            // variable
            parent::__construct($options);
            $baseDir    = $options['baseDir'];
            
            // html text
            $textAdministratorGroup = new Cible_Form_Element_Html('htmlAdministratorGroup',array('value'=>$this->getView()->getCibleText('label_administrator_actives')));
            $this->addElement($textAdministratorGroup);
            
            $checkBox = new Zend_Form_Element_MultiCheckbox('groups');
            $checkBox->setDecorators(array(
                'ViewHelper',                
                array(                    
                    array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'checkbox_list')
                ),
            ));
            //$checkBox->setDescription('<em>Example:</em> mydomain.com')
            //->addDecorator('Description', array('escape' => false));
            
            //show administrator group (first level)
            $groupAdmin = Cible_FunctionsAdministrators::getAdministratorGroupData(1)->toArray();                                               
            $checkBox->addMultiOption("1",$groupAdmin['EGI_Name']." (".$groupAdmin['EGI_Description'].")");
                                   
            
            $i = 0;
            foreach ($groupsData as $group){
                if ($group['EG_Status'] == 'active'){
                    $checkBox->addMultiOption($group['EG_ID'],$group['EGI_Name']." (".$group['EGI_Description'].")");        
                }
                
                $i++;
            }
            $this->addElement($checkBox);
            
        }
    }
?>
