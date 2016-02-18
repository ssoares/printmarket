<?php
/**
 * Cible Solutions - Module formulaires
 *
 *
 * @category  Modules
 * @package   Form
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: FormForm.php 613 2011-09-16 21:12:51Z ssoares $
 */

/**
 * This form is to manage parameters of the form to create.
 *
 * @category  Modules
 * @package   Catalog
 * @version   $Id: FormForm.php 613 2011-09-16 21:12:51Z ssoares $
 */
class FormForm extends Cible_Form
{
    /**
     *
     * @param array $options Options to build the form
     */
    public function __construct($options = null)
    {
        // Disable the defaults buttons
        if (isset($options['disableAction']))
            $this->_disabledDefaultActions = $options['disableAction'];

        if (isset($options['recipients']))
        {
            $recipients     = $options['recipients'];
            $recipientsList = $this->_setRecipientList($recipients);
            unset($options['recipients']);
        }

        parent::__construct($options);

        // Title
        $title = new Zend_Form_Element_Text('FI_Title');
        $title->setLabel(
                $this->getView()->getCibleText('form_label_title'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator(
                'NotEmpty',
                true,
                array(
                    'messages' => array(
                        'isEmpty' => $this->getView()->getCibleText(
                                'validation_message_empty_field')
                     )
                )
            )
        ->setDecorators(
                array(
                    'ViewHelper',
                    array('label', array('placement' => 'prepend')),
                    array(
                        array('row' => 'HtmlTag'),
                        array(
                            'tag'   => 'dd',
                            'class' => 'form_title_inline',
                            'id'    => 'title')
                        ),
                    )
                )
        ->setAttrib('class','stdTextInput');

        $this->addElement($title);

        // Notification
        $notification = new Zend_Form_Element_Checkbox('F_Notification');
        $notification->setLabel($this->getView()->getCibleText(
                'form_label_has_notification'));

        $notification->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(
                array('row' => 'HtmlTag'),
                array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));

        $this->addElement($notification);

        $emailList = new Zend_Form_Element_Textarea('FN_Email');
        $emailList->setAttrib('title', $this->getView()->getCibleText('form_notification_emails_info'));
        if (!empty($recipientsList))
        {
            $emailList->setValue($recipientsList);
        }

        $emailList->setDecorators(
            array(
                'ViewHelper',
                array('label', array('placement' => 'prepend')),
                array(
                    array('row' => 'HtmlTag'),
                    array(
                        'tag'   => 'dd',
                        'class' => 'formRecipientsEmail'
                        )
                    ),
                ));

        $this->addElement($emailList);

        // isSercure
//            $hasProfil = new Zend_Form_Element_Checkbox('F_Profil');
//            $hasProfil->setLabel($this->getView()->getCibleText(
//                    'form_label_has_profil'));
//            $hasProfil->setDecorators(array(
//                'ViewHelper',
//                array('label', array('placement' => 'append')),
//                array(
//                    array('row' => 'HtmlTag'),
//                    array('tag' => 'dd', 'class' => 'label_after_checkbox')),
//            ));
//
//            $this->addElement($hasProfil);

        //hasCaptcha
        $hasCaptcha = new Zend_Form_Element_Checkbox('F_Captcha');
        $hasCaptcha->setLabel($this->getView()->getCibleText(
                'form_label_has_captcha'))
                   ->setValue(true)
                   ->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(
                array('row' => 'HtmlTag'),
                array('tag' => 'dd', 'class' => 'label_after_checkbox')
            ),
        ));

        $this->addElement($hasCaptcha);
        $this->setAttrib('id', 'Form');
    }

    private function _setRecipientList($recipients)
    {
        $tmp = "";
        $eof = "\n";
        $count = count($recipients);

        foreach ($recipients as $key => $recipient)
        {
            $tmp .= trim($recipient['FN_Email']) .';';

            if ($key + 1 < $count)
                $tmp .= $eof;
        }

        return $tmp;
    }
}
?>
