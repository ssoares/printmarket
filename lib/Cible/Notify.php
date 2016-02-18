<?php
class Cible_Notify
{
    const TEXT_ONLY = 'text';
    const HTML_ONLY = 'html';
    const MIXED = 'mixed';
    const WITH_ATTACHMENT = 'withAttachment';

    protected $_isHtml;
    protected $_to = array();
    protected $_from;
    protected $_cc;
    protected $_bcc;
    protected $_title;
    protected $_message;
    protected $_attachment = array();
    protected $_mimePart = 'text';

    public function setAttachment($attachment)
    {
        foreach ($attachment as $key => $file)
        {
            if (strstr($file, Zend_Registry::get('fullDocumentRoot'))){
                $filePath = $file;
            }else{
                $filePath = Zend_Registry::get('fullDocumentRoot') . $file;
            }

            if (file_exists($filePath))
            {
                $fileName = basename($filePath);
                $fileContent = file_get_contents($filePath);
                $attachedFile = new Zend_Mime_Part($fileContent);
                $attachedFile->disposition = Zend_Mime::DISPOSITION_INLINE;
                $attachedFile->encoding = Zend_Mime::ENCODING_BASE64;
                $attachedFile->type = Zend_Mime::TYPE_OCTETSTREAM;
                $attachedFile->filename = $fileName;
                array_push($this->_attachment, $attachedFile);
            }
        }
    }

    public function getMessage()
    {
        return $this->_message;
    }

    public function __construct($options = null)
    {
        $this->_isHtml = false;
        $this->_from = null;
        $this->_to = array();
        $this->_cc = array();
        $this->_bcc = array();

        if( !empty($options['from']) )
            $this->_from = $options['from'];

        if( !empty($options['isHtml']) )
            $this->_isHtml = $options['isHtml'];

        if( !empty($options['to']) ){
            if( is_array($options['to']))
                $this->_to = $options['to'];
            else
                array_push($this->_to, $options['to']);
        }
        if( !empty($options['mimePart']) ){
            $this->_mimePart = $options['mimePart'];
        }

        if( !empty($options['cc']) ){
            if( is_array($options['cc']))
                $this->_cc = $options['cc'];
            else
                array_push($this->_cc, $options['cc']);
        }

        if( !empty($options['bcc']) ){
            if( is_array($options['bcc']))
                $this->_bcc = $options['bcc'];
            else
                array_push($this->_bcc, $options['bcc']);
        }

        if( !empty($options['title']) )
            $this->_title = $options['title'];

        if( !empty($options['message']) )
            $this->_message = $options['message'];
    }
    public function setMimePart($mimePart){
        $this->_mimePart = $mimePart;
    }

    public function isHtml($bool){
        $this->_isHtml = $bool;
    }

    public function setFrom($address){
        $this->_from = $address;
    }

    public function addTo($address){
         if(is_array($address)){
            $this->_to = array_merge($this->_to, $address);
         }else{
            array_push($this->_to, $address);
         }
    }

    public function addBcc($address){
         if( is_array($address) ){
            $this->_bcc = array_merge($this->_bcc, $address);
         }else{
            array_push($this->_bcc, $address);
         }
    }

    public function addCc($address){
         if( is_array($address) ){
            $this->_cc = array_merge($this->_cc, $address);
         }else{
            array_push($this->_cc, $address);
         }
    }

    public function setTitle($title){
         $this->_title = $title;
    }

    public function setMessage($message){
         $this->_message = $message;
    }

    public function send()
    {
        if( is_null($this->_from) )
            throw new Exception('You need to set the from address');

        // send the mail
        $mail = new Zend_Mail('utf-8');
        $mail->setSubject($this->_title);
        $mail->setFrom($this->_from);

        $toCount = count($this->_to);
        if( !empty($this->_to) ){
            for($i = 0; $i < $toCount; $i++)
                $mail->addTo($this->_to[$i]);
        }

        $ccCount = count($this->_cc);
        if( !empty($this->_cc) ){
            for($i = 0; $i < $ccCount; $i++)
                $mail->addCc($this->_cc[$i]);
        }

        $bccCount = count($this->_bcc);
        if( !empty($this->_bcc) ){
            for($i = 0; $i < $bccCount; $i++)
                $mail->addBcc($this->_bcc[$i]);
        }

        if ($this->_isHtml){
            $this->_mimePart = self::HTML_ONLY;
        }
        if (!empty($this->_attachment))
        {
            $this->_mimePart = self::WITH_ATTACHMENT;
        }
        switch($this->_mimePart)
        {
            case self::WITH_ATTACHMENT:
//                $part = new Zend_Mime_Part('Attached files');
//                $mail->addPart($part);
                foreach ($this->_attachment as $file){
                    $mail->addAttachment($file);
                }
                $mail->setBodyHtml($this->_message);
                $text = Cible_FunctionsGeneral::html2text($this->_message);
                $mail->setBodyText($text);
                $mail->setType(Zend_Mime::MULTIPART_MIXED);
                break;
            case self::HTML_ONLY:
                $mail->setBodyHtml($this->_message);
                $mail->setType(Zend_Mime::MULTIPART_RELATED);
                break;
            case self::TEXT_ONLY:
                $text = Cible_FunctionsGeneral::html2text($this->_message);
                $mail->setBodyText($text);
                $mail->setType(Zend_Mime::MULTIPART_RELATED);
                break;
            case self::MIXED:
                $mail->setBodyHtml($this->_message);
                $text = Cible_FunctionsGeneral::html2text($this->_message);
                $mail->setBodyText($text);
                $mail->setType(Zend_Mime::MULTIPART_ALTERNATIVE);
                break;

            default: // self::MIXED = text and html
                break;
        }
        $mail->send();
    }

    protected function _defineParts($mail)
    {
        // HTML part
        $htmlPart           = new Zend_Mime_Part($this->_message);
        $htmlPart->encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE;
        $htmlPart->type     = "text/html; charset=UTF-8";

        // Plain text part
        $text = Cible_FunctionsGeneral::html2text($this->_message);
        $textPart           = new Zend_Mime_Part($text);
        $textPart->encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE;
        $textPart->type     = "text/plain; charset=UTF-8";
        $mail->setParts(array($textPart, $htmlPart));
    }

}