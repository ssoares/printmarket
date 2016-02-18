<?php

class FormNewsletterManageSend extends Cible_Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $status = $options['status'];
        $planedDate = $options['planedDate'];
        $planedTime = $options['planedTime'];
        $collectionsData = $options['filterList'];
        $statusOptions = new Zend_Form_Element_Select('NR_Status');
        $statusOptions->setLabel('Action')
            ->setAttrib('class', 'largeSelect');

        $statusOptions->addMultiOption('1', Cible_Translation::getCibleText('newsletter_status_delivered'));
//        $statusOptions->addMultiOption('2', Cible_Translation::getCibleText(''));
        $statusOptions->addMultiOption('3', Cible_Translation::getCibleText('newsletter_status_incomplete'));

        $this->addElement($statusOptions);

        $send = new Zend_Form_Element_Submit('newsletter_send');
        $send->setLabel(Cible_Translation::getCibleText('button_send'))
            ->setAttrib('class', 'stdButton')
            ->setOrder(2);

        $this->addActionButton($send);

        $collectionFilters = new Zend_Form_Element_Select('NR_CollectionFiltersID');
        $collectionFilters->setLabel($this->getView()->getCibleText('form_label_collection_name'))
            ->setAttrib('class', 'largeSelect');

//            $collectionsSelect = new NewsletterFilterCollectionsSet();
//            $select = $collectionsSelect->select()
//            ->order('NFCS_Name');
//            $collectionsData = $collectionsSelect->fetchAll($select);
        $collectionFilters->addMultiOption(0, $this->getView()->getCibleText('newsletter_send_filter_selectOne'));
        foreach ($collectionsData as $key => $collection)
        {
            $collectionFilters->addMultiOption($key, $collection);
        }
        $this->addElement($collectionFilters);

        $appConfigCronSend = Zend_Registry::get('appConfigCronSend');

        // MailingDateScheduled
        $datePicker = new Cible_Form_Element_DatePicker('NR_MailingDate', array('jquery.params' => array('changeYear' => true, 'changeMonth' => true)));

        if ($appConfigCronSend == 1)
        {
            $datePicker->setLabel($this->getView()->getCibleText('form_label_releaseDate_planned_date'))
                ->setAttrib('class', 'stdTextInput')
                ->setValue($planedDate)
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->addValidator('Date', true, array('messages' => array('dateNotYYYY-MM-DD' => $this->getView()->getCibleText('validation_message_invalid_date'),
                        'dateInvalid' => $this->getView()->getCibleText('validation_message_invalid_date'),
                        'dateFalseFormat' => $this->getView()->getCibleText('validation_message_invalid_date')
                    )));
        }
        else
        {
            $datePicker->setAttrib('class', 'invisibleFormElement');
        }
        $this->addElement($datePicker);
        // MailingTimeScheduled
        $regexValidate = new Zend_Validate_Regex('/^([0-1]\d|2[0-3]):([0-5]\d)$/');
        $regexValidate->setMessage('Temps invalide (HH:MM)', 'regexNotMatch');

        $time = new Zend_Form_Element_Text('NR_MailingTime');
        if ($appConfigCronSend == 1)
        {
            $time->setLabel('HH:MM')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator($regexValidate)
                ->setValue($planedTime);
        }
        else
        {
            $time->setAttrib('class', 'invisibleFormElement');
        }
        $this->addElement($time);
    }

}
