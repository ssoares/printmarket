-- phpMyAdmin SQL Dump
-- version 3.1.3
-- http://www.phpmyadmin.net
--
-- Serveur: 209.222.235.12:3306
-- Version SVN: $Id: moduleFormsCreate.sql 1878 2016-02-18 02:26:38Z ssoares $

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

CREATE TABLE `ContactData` (
  `CD_ID` int(11) NOT NULL AUTO_INCREMENT,
  `CD_Name` varchar(255) NOT NULL COMMENT 'placeholder:true|seq:20|group:leftGrp|grpClass:col-md-6|class:required-field',
  `CD_Surname` varchar(255) NOT NULL COMMENT 'placeholder:true|seq:10|group:leftGrp|class:required-field',
  `CD_Email` varchar(255) NOT NULL COMMENT 'placeholder:true|seq:30|class:required-field',
  `CD_City` varchar(100) NULL COMMENT 'placeholder:true|seq:40|class:required-field',
  `CD_Subject` varchar(100) NOT NULL COMMENT 'placeholder:true|seq:110|group:rightGrp|grpClass:col-md-6|class:required-field',
  `CD_Comments` text COMMENT 'placeholder:true|seq:120',
  `CD_File` varchar(255) DEFAULT NULL COMMENT 'exclude:true|placeholder:true|elem:fileManager|seq:130|class:file-manager',
  `CD_DateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'exclude:true',
  PRIMARY KEY (`CD_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1;


INSERT INTO `Modules` (`M_ID`, `M_Title`, `M_MVCModuleTitle`) VALUES
(11, 'Forms', 'forms');

INSERT INTO ModuleViews (MV_ID, MV_Name, MV_ModuleID) VALUES
(11001, 'forms_contact', 11);

INSERT INTO ModuleViewsIndex (MVI_ModuleViewsID, MVI_LanguageID, MVI_ActionName) VALUES
(11001, 1,'forms-contact'),
(11001, 2,'forms-contact');

REPLACE INTO `NotificationManagerData` (`NM_ID`, `NM_ModuleId`, `NM_Event`, `NM_Type`, `NM_Recipient`, `NM_Active`, `NM_Message`, `NM_Title`, `NM_Email`) VALUES
(11, 11, 'contact', 'email', 'admin', 1, 'contact_form_notification_admin_message', 'contact_form_notification_admin_title', 'no-reply@domainname.com');

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES
('message_contact_succeed', '1', 'Votre message a été envoyé! Merci de votre intérêt.', 'cible', '', '0', '11', CURRENT_TIMESTAMP),
('message_contact_succeed', '2', 'Your message has been sent! Thank you for your interest.', 'cible', '', '0', '11', CURRENT_TIMESTAMP),
('newsletter_captcha_label', '1', '<br /><br />Pour des raisons de sécurité, veuillez entrer les caractères alphanumériques de l''image dans l''espace ci-dessous..', 'cible', '', '0', '11', CURRENT_TIMESTAMP),
('newsletter_captcha_label', '2', '<br /><br />For security reasons, please enter the alphanumeric <br />characters from the image into the space below.', 'cible', '', '0', '11', CURRENT_TIMESTAMP),
('button_captcha_refresh', '1', 'Recharger l''image', 'cible', '', '0', '11', CURRENT_TIMESTAMP),
('button_captcha_refresh', '2', 'Reload image', 'cible', '', '0', '11', CURRENT_TIMESTAMP),
('forms_label_surname', '1', 'Prénom', 'cible', '', '0', '11', CURRENT_TIMESTAMP),
('forms_label_surname', '2', 'First name', 'cible', '', '0', '11', CURRENT_TIMESTAMP),
('formSentCode', 1, 'message-envoye', 'cible', '', 0, '11', CURRENT_TIMESTAMP),
('formSentCode', 2, 'message-sent', 'cible', '', 0, '11', CURRENT_TIMESTAMP);

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID, ST_RichText) VALUES
('contact_form_notification_admin_message', 1, "Un message vous a été envoyé via votre site Internet ##siteDomain##<br /><br /> <b>De:</b><br />##firstName## ##lastName##<br /><br /> <b>Courriel:</b> <br />##email##<br /><br /> <b>Message: </b><br />##comments##<br /><br />", "client", "Message notification admin: Formulaire de contact", 1, 11, 1),
('contact_form_notification_admin_message', 2, "A new message from your website ##siteDomain## has been posted.<br /><br /><b>From:</b><br />##CD_Surname## ##CD_Name##<br /><br /><b>Email:</b> <br />##CD_Email##<br /><br /><b>Message: </b><br />##CD_Comments##<br /><br />", "client", "Message notification admin: Formulaire de contact", 1, 11, 1),
('contact_form_notification_admin_title', 1, "Nous joindre", "client", "Titre du formulaire de contact", 1, 11, 0),
('contact_form_notification_admin_title', 2, "Contact us", "client", "Titre du formulaire de contact", 1, 11, 0),
('form_label_CD_City', 1, "Ville", "cible", "", 0, 11, 0),
('form_label_CD_City', 2, "City", "cible", "", 0, 11, 0),
('form_label_CD_Comments', 1, "Message", "cible", "", 0, 11, 0),
('form_label_CD_Comments', 2, "Message", "cible", "", 0, 11, 0),
('form_label_CD_Email', 1, "Courriel", "cible", "", 0, 11, 0),
('form_label_CD_Email', 2, "Email", "cible", "", 0, 11, 0),
('form_label_CD_File', 1, "Fichier", "cible", "", 0, 11, 0),
('form_label_CD_File', 2, "File", "cible", "", 0, 11, 0),
('form_label_CD_Name', 1, "Nom", "cible", "", 0, 11, 0),
('form_label_CD_Name', 2, "Name", "cible", "", 0, 11, 0),
('form_label_CD_Subject', 1, "Sujet", "cible", "", 0, 11, 0),
('form_label_CD_Subject', 2, "Subject", "cible", "", 0, 11, 0),
('form_label_CD_Surname', 1, "Prénom", "cible", "", 0, 11, 0),
('form_label_CD_Surname', 2, "Prénom", "cible", "", 0, 11, 0),
('placeholder_CD_City', 1, "Ville", "cible", "", 0, 11, 0),
('placeholder_CD_City', 2, "City", "cible", "", 0, 11, 0),
('placeholder_CD_Comments', 1, "Message", "cible", "", 0, 11, 0),
('placeholder_CD_Comments', 2, "Message", "cible", "", 0, 11, 0),
('placeholder_CD_Email', 1, "Courriel", "cible", "", 0, 11, 0),
('placeholder_CD_Email', 2, "First Name", "cible", "", 0, 11, 0),
('placeholder_CD_File', 1, "Fichier", "cible", "", 0, 11, 0),
('placeholder_CD_File', 2, "File", "cible", "", 0, 11, 0),
('placeholder_CD_Name', 1, "Nom", "cible", "", 0, 11, 0),
('placeholder_CD_Name', 2, "Name", "cible", "", 0, 11, 0),
('placeholder_CD_Subject', 1, "Sujet", "cible", "", 0, 11, 0),
('placeholder_CD_Subject', 2, "Subject", "cible", "", 0, 11, 0),
('placeholder_CD_Surname', 1, "Prénom", "cible", "", 0, 11, 0),
('placeholder_CD_Surname', 2, "First Name", "cible", "", 0, 11, 0);
