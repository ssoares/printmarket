<?php
class Form_IndexController extends Cible_Controller_Action
{
    protected $_name       = 'form';
    protected $_renderData = array();
    protected $_html       = '';
    protected $_formTitle  = '';
    protected $_eol;

    /**
    * Overwrite the function define in the SiteMapInterface implement in Cible_Controller_Action
    *
    * This function return the sitemap specific for this module
    *
    * @access public
    *
    * @return a string containing xml sitemap
    */
    public function siteMapAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $bannersRob = new FormRobots();
        $dataXml = $bannersRob->getXMLFile($this->_registry->absolute_web_root,$this->_request->getParam('lang'));

        parent::siteMapAction($dataXml);
    }

    public function init()
    {
        parent::init();
        $this->setModuleId();
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('form.css'),'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('form.css'),'all');
    }

    public function formAction()
    {
        $formId     = 0;
        $_blockID   = $this->_request->getParam('BlockID');
        $_params    = Cible_FunctionsBlocks::getBlockParameters( $_blockID );
        $_recipient = (int) $_params[0]['P_Value'];

        $this->_formID = $params['id']     = $_recipient;
        $params['langId'] = $this->view->languageId;        

        $oForm = new FormObject($params);
        $data  = $oForm->getFormData();

        $this->_formTitle = $data['form']['FI_Title'];

        $form = new FormFront(array('action' => $this->_request->getRequestUri()), $data);

        $hasCaptcha     = $form->getHasCaptcha();
        $isValidCaptcha = true;

        //Get form parameters and test if need to be logged to submit
        //@todo: Create the process in second phase.
        $hasProfileLogged = false;
        $lastPage         = false;

        $sendNotification = $form->getSendNotification();

        $_config = Zend_Registry::get('config');
        $name = $_config->site->title->$params['langId'];        
                        
        // If the user submit the form
        if( $this->_request->isPost())
        {
            // get submitted data
            $formData = $this->_request->getPost();
            // The form is valid
            if ($form->isValid($formData))
            {
                // Test if he captcha value is ok
                if ($hasCaptcha)
                {
                    $_captcha       = $form->getElement('captcha');
                    $isValidCaptcha = $_captcha->isValid($_POST['captcha'], $_POST);
                }
                // If correct captcha or have no captcha
                if (($hasCaptcha && $isValidCaptcha) || !$hasCaptcha)
                {
                    // Test if the user has to be logged (and have a profile)
                    if ($hasProfileLogged)
                    {
                        //TODO: Create the process to save data
                        // test if it's a multi page form and if it's the last page
                        $lastPage = false;
                        if (!$lastPage)
                        {
                            /* TODO: Load the next form and display it.
                             * Exit this process.
                             */
                        }
                    }
                    /* Send an email to the recipient int the notification list
                     * of the form.
                     */

                    // create a the mail content
                    $_config = Zend_Registry::get('config');
                    $formTest = $form->populate($formData);

                    $emailContent = $this->_emailRender($formTest);
                    $this->view->assign('formMail', $emailContent);
                    $view = $this->getHelper('ViewRenderer')->view;
                    $html = $view->render('index/emailToSend.phtml');

                    if ($sendNotification)
                    {
                        $formId = $params['id'];
                        // Retrieve the recipent list and the sender
                        $oNotification = new FormNotificationObject();
                        $sender        = $_config->form->sender;
                        $recipients    = $oNotification->getNotificationEmails($formId);

                        // Create the email and send it
                        $mail = new Zend_Mail('utf-8');

                        $mail->setBodyHtml($html)
                             ->setFrom($sender, $_config->site->title)
                             ->setSubject($this->_formTitle);

                        foreach($recipients as $recipient)
                        {
                            $mail->addTo($recipient['FN_Email']);
                        }

                        $mail->send();
                        $this->renderScript('index/forms-contact-thank-you.phtml');
                    }
                    else
                    {
                        $this->renderScript('index/form-error-notification.phtml');
                    }
                }
            }
            else
                $form->populate($formData);
        }

            $this->view->assign($this->_name, $form);
    }

    public function captchaReloadAction()
    {
        $baseDir = $this->view->baseUrl();
        $captcha_image = new Zend_Captcha_Image(array(
            'captcha' => 'Word',
            'wordLen' => 6,
            'dotNoiseLevel' => 0,
            'lineNoiseLevel' => 0,
            'fontSize' => 18,
            'height'  => 50,
            'width'   => 150,
            'timeout' => 300,
            'dotNoiseLevel' => 0,
            'lineNoiseLevel' => 0,
            'font'    => Zend_Registry::get('application_path') ."/../{$this->_config->document_root}/captcha/fonts/ARIAL.TTF",
            'imgDir'  => Zend_Registry::get('application_path') . "/../{$this->_config->document_root}/captcha/tmp",
            'imgUrl'  => "$baseDir/captcha/tmp"
        ));

        $image = $captcha_image->generate();
        $captcha['id'] = $captcha_image->getId();
        $captcha['word'] = $captcha_image->getWord();
        $captcha['url'] = $captcha_image->getImgUrl() . $image . $captcha_image->getSuffix();

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        echo Zend_Json::encode($captcha);
    }

    public function thankYouAction()
    {
        throw new Exception('Not implement yet');
    }

    private function _emailRender(FormFront $formData)
    {
        $this->_eol = chr(13);
        $html = '';
        //  Define the style for the table
        $width         = ' width="600"';
        $styleTitle    = ' style="font-family:Arial; font-size:14px; font-weight:bold; padding-bottom: 20px;"';
        $styleQuestion = ' style="font-family:Arial; font-size:11px; font-weight:bold;"';
        $styleAnswer   = ' style="font-family:Arial; font-size:11px; font-weight:normal;"';
        $styleBorder   = ' style="border-top: 1px solid #666666; padding-bottom: 20px;"';

        $formData->removeElement('submit');
        $formData->removeElement('captcha');
        $formData->removeElement('RequiredFields');
        $formData->removeElement('refresh_captcha');

        $elements = $formData->getElements();

        $html = "<table border='0' cellpadding='0' cellspacing='5' " . $width . ">" . $this->_eol;

        // Form title
        $html .= '<tr><td colspan="3" ' . $styleTitle . '>' . $this->_formTitle . '</td></tr>' . $this->_eol;

        foreach ($elements as $data => $element)
        {
            $values  = $element->getValue();
            $options = $element->options;
            $nameData = explode('_', $element->getName());

            // Select, Radio, Checkbox
            if (is_array($options)
                && !($element instanceof Cible_Form_Element_DatePicker))
            {
                unset($options['']);

                $renderType = '_render' . $nameData[0];

                $html .= '<tr valign="top">' . $this->_eol;
                $html .= '<td ' . $styleQuestion . ' ' . $width . '>';
                $html .= $element->getLabel();
                $html .= '</td>' . $this->_eol;
                $html .= '</tr>' . $this->_eol;

                // Render elements
                $html .= '<tr valign="top">' . $this->_eol;
                $html .= '<td colspan="2" ' . $styleAnswer . '>';

                $html .= "<table border='0' cellpadding='0' cellspacing='3' " . $width . ">" . $this->_eol;
                $html .= $this->$renderType($options, $element, $styleAnswer);
                $html .= "</table>" . $this->_eol;

                $html .= '</td>' . $this->_eol;
                $html .= '</tr>' . $this->_eol;
                $html .= '<tr valign="top"><td colspan="2" ' . $styleBorder . '></td></tr>' . $this->_eol;
            }
            // Others (Text, Date)
            elseif(!($element instanceof Cible_Form_Element_Html)
                    || !($element instanceof  Zend_Form_Element_Captcha))
            {
                $html .= '<tr valign="top">' . $this->_eol;
                $html .= '<td ' . $styleQuestion . '' . $width . '>' . $this->_eol;
                $html .= $element->getLabel();
                $html .= '</td>' . $this->_eol;
                $html .= '</tr>' . $this->_eol;

                $html .= '<tr valign="top">' . $this->_eol;
                $html .= '<td colspan="2" ' . $styleAnswer . '>';
                $html .= $element->getValue();
                $html .= '</td>' . $this->_eol;
                $html .= '</tr>' . $this->_eol;
                $html .= '<tr valign="top"><td colspan="2" ' . $styleBorder . '></td></tr>' . $this->_eol;
            }
        }
        $html .= "</table>" . $this->_eol;

        return $html;
    }

    private function _renderSelect($options, $element, $styleAnswer)
    {
        $html   = '';
        $first  = 0;
        $valOpt = $element->getValue();

        foreach ($options as $key => $value)
        {
            $cross = "[&nbsp;&nbsp;&nbsp;&nbsp;]";
            $data  = "";
            if ($key == $valOpt)
            {
                $cross = "[&nbsp;x&nbsp;]";
            }
            $html .= ($first > 0) ? '<tr valign="top">' . $this->_eol : '';
            $html .= '<td ' . $styleAnswer . '>'. $cross . " " . $value . '</td>' . $this->_eol;
            $html .= '<td ' . $styleAnswer . '>' . $data .  '</td>' . $this->_eol;
            $html .= '</tr>' . $this->_eol;

            ++$first;
        }

        return $html;
    }

    private function _renderSingleChoice($options, $element, $styleAnswer)
    {
        $html   = '';
        $first  = 0;
        $info   = '';
        $valOpt = $element->getValue();

        if(is_array($element->getValue()))
        {
            $tmpArr = $element->getValue();
            $valOpt = key($tmpArr);
            $info   = $tmpArr[$valOpt];
        }

        foreach ($options as $key => $value)
        {
            $cross = "[&nbsp;&nbsp;&nbsp;&nbsp;]";
            $data  = "";
            if ($key == $valOpt || $key .'0' == $valOpt)
            {
                $cross = "[&nbsp;x&nbsp;]";
                $data  = '<strong>' . $this->view->getCibleText('form_detail_label_for_response_options') . ' : </strong>' . $info;
            }
            $html .= ($first > 0) ? '<tr valign="top">' . $this->_eol : '';
            $html .= '<td ' . $styleAnswer . '>'. $cross . " " . $value . '</td>' . $this->_eol;
            $html .= '<td ' . $styleAnswer . '>' . $data .  '</td>' . $this->_eol;
            $html .= '</tr>' . $this->_eol;

            ++$first;
        }

        return $html;
    }

    private function _renderMultiChoice($options, $element, $styleAnswer)
    {
        $html   = '';
        $first  = 0;
        $valOpt = $element->getValue();
        $test   = false;
        $test2  = false;

        foreach ($options as $key => $value)
        {
            $cross = "[&nbsp;&nbsp;&nbsp;&nbsp;]";
            $data  = "";

            if (isset($valOpt[$key . '0']))
            {
                $cross = "[&nbsp;x&nbsp;]";
                $data  = '<strong>' . $this->view->getCibleText('form_detail_label_for_response_options') . ' : </strong>' . $valOpt[$key . '0'];
                unset($valOpt[$key . '0']);
            }

            if ($valOpt)
            {
                $test  = in_array($key, $valOpt);
                $test2 = array_search($key, $valOpt);
            }

            if ($test && ($test2 % 10 || $test2 == 0))
            {
                $cross = "[&nbsp;x&nbsp;]";
            }
            $html .= ($first > 0) ? '<tr valign="top">' . $this->_eol : '';
            $html .= '<td ' . $styleAnswer . '>'. $cross . " " . $value . '</td>' . $this->_eol;
            $html .= '<td ' . $styleAnswer . '>' . $data .  '</td>' . $this->_eol;
            $html .= '</tr>' . $this->_eol;

            ++$first;
        }

        return $html;
    }
}
