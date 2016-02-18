-- phpMyAdmin SQL Dump
-- version 3.1.3
-- http://www.phpmyadmin.net
--
-- Serveur: 209.222.235.12:3306
-- Généré le : Dim 13 Juin 2010 à 21:04
-- Version du serveur: 5.0.70
-- Version de PHP: 5.2.10-pl0-gentoo
-- Version SVN: $Id: moduleNewsletterCreate.sql 1872 2016-02-09 19:58:47Z ssoares $

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Structure de la table `NewsletterFilter_CollectionsFiltersSet`
--

CREATE TABLE IF NOT EXISTS `NewsletterFilter_CollectionsFiltersSet` (
  `NFCFS_CollectionSetID` int(11) NOT NULL,
  `NFCFS_FilterSetID` int(11) NOT NULL,
  PRIMARY KEY  (`NFCFS_CollectionSetID`,`NFCFS_FilterSetID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `NewsletterFilter_CollectionsSet`
--



CREATE TABLE IF NOT EXISTS `NewsletterFilter_CollectionsSet` (
  `NFCS_ID` int(11) NOT NULL AUTO_INCREMENT,
  `NFCS_Name` varchar(255) NOT NULL,
  PRIMARY KEY (`NFCS_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



-- --------------------------------------------------------

--
-- Structure de la table `NewsletterFilter_Filters`
--

CREATE TABLE IF NOT EXISTS `NewsletterFilter_Filters` (
  `NFF_ID` int(11) NOT NULL auto_increment,
  `NFF_ProfileFieldName` varchar(255) NOT NULL,
  `NFF_FilterSetID` int(11) NOT NULL,
  `NFF_Value` text NOT NULL,
  PRIMARY KEY  (`NFF_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `NewsletterFilter_FiltersSet`
--

CREATE TABLE IF NOT EXISTS `NewsletterFilter_FiltersSet` (
  `NFFS_ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`NFFS_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `NewsletterFilter_ProfilesFields`
--

CREATE TABLE IF NOT EXISTS `NewsletterFilter_ProfilesFields` (
  `NFPF_ID` int(11) NOT NULL auto_increment,
  `NFPF_ProfileTableID` int(11) NOT NULL,
  `NFPF_Type` varchar(255) NOT NULL,
  `NFPF_Name` varchar(255) NOT NULL,
  PRIMARY KEY  (`NFPF_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

INSERT INTO NewsletterFilter_ProfilesFields (NFPF_ProfileTableID, NFPF_Type, NFPF_Name) VALUES
(1, 'int', 'GP_Language'),
(2, 'list', 'NP_Categories'),
(2, 'int', 'NP_TypeID'),
(2, 'qrystr', 'NP_SubscriptionDate');

-- --------------------------------------------------------

--
-- Structure de la table `NewsletterFilter_ProfilesTables`
--

CREATE TABLE IF NOT EXISTS `NewsletterFilter_ProfilesTables` (
  `NFPT_ID` int(11) NOT NULL auto_increment,
  `NFPT_Name` varchar(255) NOT NULL,
  `NFPT_JoinOn` text NOT NULL,
  PRIMARY KEY  (`NFPT_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

INSERT INTO NewsletterFilter_ProfilesTables (NFPT_Name, NFPT_JoinOn) VALUES
('GenericProfiles', ''),
('NewsletterProfiles', 'NP_ProfileId = GP_MemberID'),
('MemberProfiles', 'MP_GenericProfileId = GP_MemberID');


-- --------------------------------------------------------

--
-- Structure de la table `NewsletterProfiles`
--

CREATE TABLE IF NOT EXISTS `NewsletterProfiles` (
  `NP_ProfileId` int(11) NOT NULL,
  `NP_Categories` INT(11) DEFAULT NULL,
  `NP_TypeID` INT(11) DEFAULT NULL,
  `NP_SubscriptionDate` DATE DEFAULT NULL,
  `NP_ExternalId` INT(11) DEFAULT NULL,
  `NP_IPAddress` VARCHAR(45) DEFAULT NULL,
  PRIMARY KEY (`NP_ProfileId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Newsletter_Articles`
--

CREATE TABLE IF NOT EXISTS `Newsletter_Articles` (
  `NA_ID` int(11) NOT NULL auto_increment,
  `NA_ReleaseID` int(11) NOT NULL,
  `NA_ZoneID` int(11) NOT NULL,
  `NA_PositionID` int(11) NOT NULL,
  `NA_Title` varchar(255) NOT NULL,
  `NA_Resume` text NOT NULL,
  `NA_Text` longtext NOT NULL,
  `NA_ImageAlt` varchar(255) NOT NULL,
  `NA_ImageSrc` varchar(255) NOT NULL,
  `NA_ValUrl` varchar(255) default NULL,
  `NA_URL` VARCHAR( 512 ) NOT NULL,
  `NA_TextLink` INT( 11 ) NOT NULL DEFAULT  '1',
  PRIMARY KEY  (`NA_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



-- --------------------------------------------------------

--
-- Structure de la table `Newsletter_CategoriesModels`
--

CREATE TABLE IF NOT EXISTS `Newsletter_CategoriesModels` (
  `NC_CategoryID` int(11) NOT NULL,
  `NC_DefaultModelID` int(11) NOT NULL,
  PRIMARY KEY  (`NC_CategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Newsletter_Models`
--

CREATE TABLE IF NOT EXISTS `Newsletter_Models` (
  `NM_ID` int(11) NOT NULL auto_increment,
  `NM_Directory` varchar(255) NOT NULL,
  `NM_FromEmail` varchar(255) NOT NULL,
  `NM_DirectoryWeb` varchar(255) NOT NULL,
  `NM_DirectoryEmail` varchar(255) NOT NULL,
  `NM_IsDefault` TINYINT NOT NULL,
  PRIMARY KEY  (`NM_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `Newsletter_Models_Index`
--

CREATE TABLE IF NOT EXISTS `Newsletter_Models_Index` (
  `NMI_NewsletterModelID` int(11) NOT NULL,
  `NMI_LanguageID` int(11) NOT NULL,
  `NMI_Title` varchar(255) NOT NULL,
  `NMI_FromName` varchar(255) NOT NULL,
  PRIMARY KEY  (`NMI_NewsletterModelID`,`NMI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Newsletter_Releases`
--

CREATE TABLE IF NOT EXISTS `Newsletter_Releases` (
  `NR_ID` int(11) NOT NULL auto_increment,
  `NR_LanguageID` int(11) NOT NULL,
  `NR_CategoryID` int(11) NOT NULL,
  `NR_ModelID` int(11) NOT NULL,
  `NR_Title` VARCHAR(255) NOT NULL,
  `NR_AdminEmail` VARCHAR(255) NULL,
  `NR_Date` date NOT NULL,
  `NR_MailingDateTimeScheduled` datetime NOT NULL,
  `NR_MailingDateTimeStart` datetime NOT NULL,
  `NR_MailingDateTimeEnd` datetime NOT NULL,
  `NR_Status` int(11) NOT NULL,
  `NR_Online` tinyint(4) NOT NULL,
  `NR_SendTo` int(11) NOT NULL,
  `NR_TargetedTotal` int(11) default NULL,
  `NR_CollectionFiltersID` int(11) NOT NULL,
  `NR_ValUrl` VARCHAR(255) NOT NULL,
  `NR_TextIntro` TEXT NOT NULL,
  `NR_AfficherTitre` INT NULL DEFAULT '1',
  `NR_ExternalId` INT(11) NULL,
  PRIMARY KEY  (`NR_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `Newsletter_ReleasesMembers`
--

CREATE TABLE IF NOT EXISTS `Newsletter_ReleasesMembers` (
  `NRM_ReleaseID` int(11) NOT NULL,
  `NRM_MemberID` int(11) NOT NULL,
  `NRM_Email` VARCHAR(255) NOT NULL,
  `NRM_DateTimeReceived` datetime NOT NULL,
  `NR_CronSending` TINYINT( 1 ) NOT NULL DEFAULT '0',
  UNIQUE KEY `NRM_ReleaseID` (`NRM_ReleaseID`,`NRM_MemberID`,`NRM_Email`,`NRM_DateTimeReceived`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Newsletter_InvalidEmails`
--

CREATE TABLE IF NOT EXISTS `Newsletter_InvalidEmails` (
  `NIE_ID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `NIE_FirstName` VARCHAR( 50 ) NOT NULL ,
  `NIE_LastName` VARCHAR( 50 ) NOT NULL ,
  `NIE_Email` VARCHAR( 255 ) NOT NULL ,
  `NIE_ReleaseId` INT(11) NOT NULL,
  `NIE_Message` TEXT NULL
) ENGINE = MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Structure de la table `Newsletter_ErrorsLog`
--

CREATE TABLE IF NOT EXISTS `Newsletter_ErrorsLog` (
  `NEL_ID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `NEL_IdIndex` VARCHAR( 50 ) NOT NULL ,
  `NEL_CodeFileLine` TEXT NOT NULL ,
  `NEL_Response` TEXT NOT NULL,
  `NEL_Timestamp` TIMESTAMP NOT NULL
) ENGINE = MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
--
-- Données pour activer module et les liens dans le back end
--
-- ALTER TABLE `Newsletter_Releases` ADD `NR_CronSending` TINYINT( 1 ) NOT NULL DEFAULT '0';
REPLACE INTO Modules (M_ID, M_Title, M_MVCModuleTitle, M_UseProfile, M_Indexation) VALUES (8, 'Infolettres', 'newsletter', 1, 'NewsletterReleases');

REPLACE INTO Modules_ControllersActionsPermissions (MCAP_ModuleID, MCAP_ControllerTitle, MCAP_ControllerActionTitle, MCAP_PermissionTitle, MCAP_Position) VALUES
(8, 'index', 'list-categories', 'edit', 1),
(8, 'index', 'list-all', 'edit', 2),
(8, 'filter', 'list-collection', 'edit', 3),
(8, 'index', 'list-recipients', 'edit', 4),
(8, 'statistic', 'index', 'list', 5);

REPLACE INTO Newsletter_Models (NM_Directory, NM_FromEmail, NM_DirectoryWeb, NM_DirectoryEmail) VALUES
('index/templates/one/template-one.phtml', 'sergio.soares@ciblesolutions.com', 'index/templates/one/template-one-web.phtml', 'index/templates/one/template-one-email.phtml'),
('index/templates/two/template-two.phtml', 'sergio.soares@ciblesolutions.com', 'index/templates/two/template-two-web.phtml', 'index/templates/two/template-two-email.phtml');

REPLACE INTO Newsletter_Models_Index (NMI_NewsletterModelID, NMI_LanguageID, NMI_Title, NMI_FromName) VALUES
(1, 1, 'Infolettre modèle 1', 'xxxxxxxx'),
(1, 2, 'Newsletter model 1', 'xxxxxxxx'),
(1, 3, 'Infolettre modèle 1 espagnol', 'xxxxxxxx'),
(1, 4, 'Newsletter model 1 italiens', 'xxxxxxxx'),
(2, 1, 'Infolettre modèle 2', 'xxxxxxxx'),
(2, 2, 'Newsletter model 2', 'xxxxxxxx'),
(2, 3, 'Infolettre modèle 2 espagnol', 'xxxxxxxx'),
(2, 4, 'Newsletter model 2 italiens', 'xxxxxxxx');

INSERT INTO `Views` (`V_ID`, `V_Name`, `V_ZoneCount`, `V_Path`, `V_Image`)
VALUES (NULL, 'Newsletters', '3', 'template/newsletter.phtml', 'image.png');

REPLACE INTO ModuleViews (MV_ID, MV_Name, MV_ModuleID) VALUES
(8001, 'subscribe', 8),
(8002,'details_release', 8),
(8003,'details_article', 8),
(8004,'unsubscribe', 8),
(8005,'list_archives', 8),
(8006, 'show_web', 8),
(8007, 'show_web_details', 8),
(8009, 'resubscribe', 8);

REPLACE INTO `ModuleViewsIndex` (`MVI_ModuleViewsID`, `MVI_LanguageID`, `MVI_ActionName`) VALUES
(8001, 1, 'inscription'),
(8001, 2, 'subscribe'),
(8002, 1, 'parution'),
(8002, 2, 'release'),
(8003, 1, 'article'),
(8003, 2, 'article'),
(8004, 1, 'desabonnement'),
(8004, 2, 'unsubscribe'),
(8005, 1, 'liste-archives'),
(8005, 2, 'list-archives'),
(8006, 1, 'show-web'),
(8006, 2, 'shows-web'),
(8007, 1, 'show-web-detail'),
(8007, 2, 'show-web-details'),
(8009,1,'reinscription'),
(8009,2,'resubscribe');

REPLACE INTO Extranet_Resources (ER_ID, ER_ControlName) VALUES (8, 'newsletter');

REPLACE INTO Extranet_ResourcesIndex (ERI_ResourceID, ERI_LanguageID, ERI_Name) VALUES
(8, 1, 'Infolettres'),
(8, 2, 'Newsletters');

REPLACE INTO Extranet_RolesResources (ERR_ID, ERR_RoleID, ERR_ResourceID, ERR_InheritedParentID) VALUES
(8001,1, 8, 0),
(8002,2, 8, 8001),
(8003,3, 8, 8002);

REPLACE INTO Extranet_RolesResourcesIndex (ERRI_RoleResourceID, ERRI_LanguageID, ERRI_Name, ERRI_Description) VALUES
(8003,1, "Gestionnaire d'infolettre", 'A tous les droits pour gérer les infolettres.'),
(8003,2, 'Newsletter manager', 'Has all the permissions to manage newsletters.');

REPLACE INTO Extranet_RolesResourcesPermissions (ERRP_RoleResourceID, ERRP_PermissionID) VALUES
(8001, 1),
(8002, 2),
(8003, 4);

REPLACE INTO Categories (C_ID, C_ParentID, C_ModuleID, C_PageID,	C_ShowInRss,C_RssItemsCount) VALUES
(2,0,8,null,1,10);

REPLACE INTO `CategoriesIndex` (`CI_CategoryID`, `CI_LanguageID`, `CI_Title`, `CI_WordingShowAllRecords`) VALUES
(2, 1, 'Générale', 'Liste des infolettres à portée générale'),
(2, 2, 'General', 'List of the newsletters with common subject');

REPLACE INTO `Pages` (`P_ID`, `P_Position`, `P_ParentID`, `P_Home`, `P_LayoutID`, `P_ThemeID`, `P_ViewID`, `P_ShowSiteMap`, `P_ShowMenu`, `P_ShowTitle`) VALUES
(8001, 4, 0, 0, 2, 1, 2, 1, 1, 0),
(8002, 1, 8001, 0, 2, 1, 2, 1, 1, 1),
(8003, 2, 8001, 0, 2, 1, 2, 1, 1, 1),
(8004, 3, 8001, 0, 2, 1, 2, 1, 1, 1),
(8005, 4, 8001, 0, 2, 1, 2, 1, 1, 0),
(8006, 5, 8001, 0, 2, 1, 2, 1, 1, 1),
(8007, 6, 8001, 0, 2, 1, 2, 1, 1, 0);

REPLACE INTO `PagesIndex` (`PI_PageID`, `PI_LanguageID`, `PI_PageIndex`, `PI_PageIndexOtherLink`, `PI_PageTitle`, `PI_TitleImageSrc`, `PI_TitleImageAlt`, `PI_MetaDescription`, `PI_MetaKeywords`,`PI_MetaOther`, `PI_Status`, `PI_Secure`) VALUES
(8001, 1, 'infolettres', '', 'Infolettre', '', '', '', '', '', 1, 'non'),
(8001, 2, 'news-letters', '', 'Newsletter', '', '', '', '', '', 1, 'non'),
(8002, 1, 'infolettres-en-details', '', 'Archives', '', '', '', '', '', 1, 'non'),
(8002, 2, 'news-letters-in-details', '', 'Archives', '', '', '', '', '', 1, 'non'),
(8003, 1, 'abonnement', '', 'Abonnement', '', '', '', '', '', 1, 'non'),
(8003, 2, 'subscription', '', 'Subscription', '', '', '', '', '', 1, 'non'),
(8004, 1, 'desabonnement', '', 'Désabonnement', '', '', '', '', '', 1, 'non'),
(8004, 2, 'unsubscribe', '', 'Unsubscribe', '', '', '', '', '', 1, 'non'),
(8005, 1, 'infolettre-en-details', '', 'Infolettre en détails', '', '', '', '', '', 1, 'non'),
(8005, 2, 'infoletter-details', '', 'Newsletter details', '', '', '', '', '', 1, 'non'),
(8006, 1, 'show-web', '', 'Prévisualisation d''une parution', '', '', '', '', '', 1, 'non'),
(8006, 2, 'show-web-en', '', 'Newsletter preview', '', '', '', '', '', 1, 'non'),
(8007, 1, 'show-web-details', '', 'Prévisualisation d''un articles', '', '', '', '', '', 1, 'non'),
(8007, 2, 'show-web-en-details', '', 'Articles preview', '', '', '', '', '', 1, 'non');

REPLACE INTO `ModuleCategoryViewPage` (`MCVP_ID`, `MCVP_ModuleID`, `MCVP_CategoryID`, `MCVP_ViewID`, `MCVP_PageID`) VALUES
(8001, 8, 2, 8001, 8003),
(8002, 8, 2, 8002, 8001),
(8003, 8, 2, 8003, 8005),
(8004, 8, 2, 8004, 8004),
(8005, 8, 2, 8005, 8002);

REPLACE INTO `References_ListUsed` (`RLU_List`, `RLU_Table`, `RLU_Column`) VALUES
('typeClient', 'NewsletterProfiles', 'NP_TypeID');

--
-- Insert Static_Text values for the Newsletter module
--
REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('module_newsletter', 1, 'Infolettre', 'cible', '', 0 , 8),
('module_newsletter', 2, 'Newsletter', 'cible', '', 0 , 8),
('management_module_newsletter_list', 1, 'Parutions', 'cible', '', 0 , 8),
('management_module_newsletter_list', 2, 'Newsletters', 'cible', '', 0 , 8),
('management_module_newsletter_list_all', 1, 'Parutions', 'cible', '', 0 , 8),
('management_module_newsletter_list_categories', 1, 'Catégories des infolettres', 'cible', '', 0 , 8),
('management_module_newsletter_list_categories', 2, 'Categories list', 'cible', '', 0 , 8),
('management_module_newsletter_index', 1, 'Statistiques', 'cible', '', 0 , 8),
('management_module_newsletter_index', 2, 'Statistics', 'cible', '', 0 , 8),
('management_module_newsletter_list_recipients', 1, 'Inscriptions', 'cible', '', 0, 8),
('management_module_newsletter_list_recipients', 2, 'Subscribption', 'cible', '', 0 , 8),
('management_module_newsletter_list_collection', 1, 'Collections de filtres', 'cible', '', 0, 8),
('management_module_newsletter_list_collection', 2, "Filters collection", 'cible','', 0 , 8),
('newsletter_form_label_subscriptiontext', 1, "Texte pour l'abonnement à cette infolettre", 'cible', '', 0 , 8),
('newsletter_form_label_subscriptiontext', 2, 'Text for the subscribe to this newsletter', 'cible', '', 0 , 8),
('newsletter_module_name', 1, 'Infolettre', 'cible', '', 0 , 8),
('newsletter_module_name', 2, 'Newsletter', 'cible', '', 0 , 8),
('newsletter_categories_page_title', 1, 'Gestion des catégories', 'cible', '', 0 , 8),
('newsletter_categories_page_title', 2, 'Categories list', 'cible', '', 0 , 8),
('newsletter_categories_page_description', 1, "Cette page vous permet de consulter la liste des catégories d'infolettres.", 'cible', '', 0 , 8),
('newsletter_button_add_category', 1, 'Ajouter une catégorie d''infolettre', 'cible', '', 0, 8),
('newsletter_button_add_category', 2, 'Add a newsletter category', 'cible', '', 0 , 8),
('header_list_newsletter_recipient_text', 1, 'Gestion des inscriptions', 'cible', '', 0 , 8),
('header_list_newsletter_text_default', 1, 'Liste de toutes les parutions', 'cible', '', 0 , 8),
('header_list_newsletter_recipient_description', 1, "Cliquez sur <b>Ajouter une nouvelle personne</b><br>pour ajouter un destinataire.<br><br>Vous pouvez <b>rechercher par mots-clés</b> parmi la<br>liste des abonnés. Pour revenir à la liste complète,<br>cliquez sur <b>Voir la liste complète</b>.<br><br>Vous pouvez <b>modifier ou supprimer le compte<br>d'un utilisateur</b> en cliquant sur l'icône <img src=\"/edith/icons/icon-add-24x24.png\" align='middle'>.", 'cible', '', 0 , 8),
('header_list_newsletter_description_default', 1, 'Cette page vous permet de gérer toutes les parutions.', 'cible', '', 0 , 8),
('button_add_newsletter', 1, 'Ajouter une parution', 'cible', '', 0 , 8),
('button_add_newsletter', 2, 'Add a parution', 'cible', '', 0 , 8),
('header_list_newsletter_text', 1, 'Liste des parutions de la catégorie <em>«%CATEGORY_NEWSLETTER_NAME%»</em>', 'cible', '', 0 , 8),
('header_list_newsletter_text', 2, 'Parutions list of the category <em>«%CATEGORY_NEWSLETTER_NAME%»</em>', 'cible', '', 0 , 8),
('header_list_newsletter_description', 1, 'Cette page vous permet de consulter la liste des parutions de la catégorie <em><strong>«%CATEGORY_NEWSLETTER_NAME%»</em></strong>.', 'cible', '', 0 , 8),
('header_list_newsletter_description', 2, 'This page is to consult the parutions list of the category <em><strong>«%CATEGORY_NEWSLETTER_NAME%»</em></strong>.', 'cible', '', 0 , 8),
('header_list_newsletter_description_default', 2, 'This page is to manage all the parutions.', 'cible', '', 0 , 8),
('header_add_newsletter_text', 1, "Ajout d\'une parution", 'cible', '', 0 , 8),
('header_add_newsletter_description', 1, "Cette page vous permet d'ajouter une parution.", 'cible', '', 0 , 8),
('header_edit_newsletter_text', 1, "Édition d'une parution", 'cible', '', 0 , 8),
('header_edit_newsletter_description', 1, "Cette page vous permet d'éditer une parution.", 'cible', '', 0 , 8),
('form_select_option_view_newsletter_inscription', 1, 'Inscription', 'cible', '', 0 , 8),
('newsletter_manage_block_contents', 1, 'Gestion des inscriptions', 'cible', '', 0 , 8),
('newsletter_manage_block_contents', 2, 'Subscriptions management', 'cible', '', 0 , 8),
('form_select_option_view_newsletter_unsubscribe', 1, 'Désabonnement', 'cible', '', 0 , 8),
('form_select_option_view_newsletter_unsubscribe', 2, 'Unsubscribe', 'cible', '', 0 , 8),
('newsletter_unsubscribe_form_title', 1, "Désabonnement à l'infolettre", 'cible', '', 0 , 8),
('newsletter_unsubscribe_form_title', 2, 'Unsubscribe to newsletter', 'cible', '', 0 , 8),
('newsletter_subscribe_form_title', 1, "Abonnement à l'infolettre", 'cible', '', 0 , 8),
('newsletter_subscribe_form_title', 2, 'Subscribe to newsletter', 'cible', '', 0 , 8),
('newsletter_subscribe_confirmation_message1', 1, "Votre inscription à l'infolettre est complétée.", 'cible', '', 0 , 8),
('newsletter_subscribe_confirmation_message1', 2, 'You are now subscribed to the newsletter', 'cible', '', 0 , 8),
('newsletter_subscribe_confirmation_message2', 1, 'Votre inscription à cette infolettre a déjà été enregistrée.', 'cible', '', 0 , 8),
('newsletter_subscribe_confirmation_message2', 2, 'Your subscription to this newsletter has already been registered.', 'cible', '', 0 , 8),
('newsletter_unsubscribe_confirmation_message', 1, "Votre désabonnement à l'infolettre est complété.", 'cible', '', 0 , 8),
('newsletter_unsubscribe_confirmation_message', 2, 'You have successfully unsubscribed from the newsletter.', 'cible', '', 0 , 8),
('newsletter_subscribe_confirmation_button', 1, "Retour au formulaire d'inscription", 'cible', '', 0 , 8),
('newsletter_subscribe_confirmation_button', 2, 'Back to subscription form', 'cible', '', 0 , 8),
('newsletter_unsubscribe_confirmation_button', 1, "Retour à la page d'accueil", 'cible', '', 0 , 8),
('newsletter_unsubscribe_confirmation_button', 2, 'Back to Home', 'cible', '', 0 , 8),
('form_select_option_view_newsletter_details_article', 1, "Afficher un article d'une parution", 'cible', '', 0 , 8),
('form_select_option_view_newsletter_details_article', 2, 'View an article in a publication', 'cible', '', 0 , 8),
('form_select_option_view_newsletter_details_release', 1, 'Afficher une parution', 'cible', '', 0 , 8),
('form_select_option_view_newsletter_details_release', 2, 'View a publication', 'cible', '', 0 , 8),
('form_select_option_view_newsletter_subscribe', 1, 'Inscription', 'cible', '', 0 , 8),
('form_select_option_view_newsletter_subscribe', 2, 'Subscribe', 'cible', '', 0 , 8),
('newsletter_fo_form_label_lName', 1, 'Nom de famille', 'cible', '', 0 , 8),
('newsletter_fo_form_label_lName', 2, 'Surname', 'cible', '', 0 , 8),
('newsletter_fo_form_label_fName', 1, 'Prénom', 'cible', '', 0 , 8),
('newsletter_fo_form_label_fName', 2, 'First Name', 'cible', '', 0 , 8),
('newsletter_fo_form_label_email', 1, 'Adresse courriel', 'cible', '', 0 , 8),
('newsletter_fo_form_label_email', 2, 'Email', 'cible', '', 0 , 8),
('newsletter_form_label_admin_email', 2, 'Email of the administrator to notify', 'cible', '', 0 , 8),
('newsletter_fo_form_label_language', 1, 'Langue', 'cible', '', 0 , 8),
('newsletter_fo_form_label_language', 2, 'Language', 'cible', '', 0 , 8),
('newsletter_no_articles_for_newsletter_client', 1, "Il n'y a aucune publication.", 'client','', 0 , 8),
('newsletter_no_articles_for_newsletter_client', 2, 'There is no publication', 'client','', 0 , 8),
('newsletter_button_archives', 1, "Archives", 'cible','', 0 , 8),
('newsletter_button_archives', 2, 'Archives', 'cible','', 0 , 8),
('newsletter_button_subscribe', 1, "Inscription à l'infolettre", 'cible','', 0 , 8),
('newsletter_button_subscribe', 2, 'Newsletter subscribe', 'cible','', 0 , 8),
('newsletter_button_unsubscribe', 1, "Se désabonner", 'cible','', 0 , 8),
('newsletter_button_unsubscribe', 2, 'Newsletter Unsubscribe', 'cible','', 0 , 8);
REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('header_edit_newsletter_release_text', 1, "Édition d'une parution", 'cible','', 0 , 8),
('header_edit_newsletter_release_text', 2, 'Editing a publication', 'cible','', 0 , 8),
('header_edit_newsletter_release_description', 1, "Cette page vous permet d'éditer une parution", 'cible','', 0 , 8),
('header_edit_newsletter_release_description', 2, 'This page allows you to edit an issue', 'cible','', 0 , 8),
('form_title_create_article', 1, "Créer un article", 'cible','', 0 , 8),
('form_title_create_article', 2, 'Créer un article', 'cible','', 0 , 8),
('form_title_release_information', 1, "Information sur la parution", 'cible','', 0 , 8),
('form_title_release_information', 2, 'Information sur la parution', 'cible','', 0 , 8),
('form_title_overview_and_test', 1, "Aperçus et tests", 'cible','', 0 , 8),
('form_title_overview_and_test', 2, 'Aperçus et tests', 'cible','', 0 , 8),
('form_title_send_and_statistic', 1, "Envois et statistiques", 'cible','', 0 , 8),
('form_title_send_and_statistic', 2, 'Envois et statistiques', 'cible','', 0 , 8),
('form_label_overview_type', 1, "Type d'aperçu :", 'cible','', 0 , 8),
('form_label_overview_type', 2, "Type d'aperçu :", 'cible','', 0 , 8),
('form_label_send_test', 1, "Envoi d'un test :", 'cible','', 0 , 8),
('form_label_send_test', 2, "Envoi d'un test :", 'cible','', 0 , 8),
('form_label_recipients', 1, "Les destinataires :", 'cible','', 0 , 8),
('form_label_recipients', 2, "Les destinataires :", 'cible','', 0 , 8),
('form_label_statistics', 1, "Statistiques :", 'cible','', 0 , 8),
('form_label_statistics', 2, "Statistiques :", 'cible','', 0 , 8),
('form_label_releaseDate', 1, 'Afficher à partir de la date', 'cible', '', 0, 8),
('form_label_releaseDate', 2, 'Show from this date', 'cible', '', 0, 8),
('form_label_releaseDate_planned_date', 1, 'À envoyer le ', 'cible', '', 0, 8),
('form_label_releaseDate_planned_date', 2, 'To send on ', 'cible', '', 0, 8),
('texts', 1, "Textes", 'cible','', 0 , 8),
('texts', 2, "Textes", 'cible','', 0 , 8),
('button_overview_email', 1, "Courriel", 'cible','', 0 , 8),
('button_overview_email', 2, "Courriel", 'cible','', 0 , 8),
('button_overview_web', 1, "Web", 'cible','', 0 , 8),
('button_overview_web', 2, "Web", 'cible','', 0 , 8),
('button_edit_informations', 1, "Éditer ces informations", 'cible','', 0 , 8),
('button_edit_informations', 2, "Éditer ces informations", 'cible','', 0 , 8),
('button_send', 1, "Envoyer", 'cible','', 0 , 8),
('button_send', 2, "Send", 'cible','', 0 , 8),
('button_set_list', 1, 'Définir les listes', 'cible', '', 0, 8),
('button_set_list', 2, 'Define lists', 'cible', '', 0, 8),
('button_send_to_list', 1, 'Envoyer à la liste', 'cible', '', 0, 8),
('button_send_to_list', 2, 'Envoyer à la liste', 'cible', '', 0, 8),
('header_list_newsletter_filter_collection_text', 1, "Liste des collections de filtres", 'cible','', 0 , 8),
('header_list_newsletter_filter_collection_text', 2, "Collection list filter", 'cible','', 0 , 8),
('header_list_newsletter_filter_collection_description', 1, "Les collections contiennent un ensemble de filtres permettant de faire un envoi d'infolettre<br>à un ensemble de gens ciblés. ", 'cible','', 0 , 8),
('header_list_newsletter_filter_collection_description', 2, "The collections contain a set of filters to be sending a newsletter to a targeted group of people", 'cible','', 0 , 8),
('list_column_NFCS_Name', 1, "Nom des collections", 'cible','', 0 , 8),
('list_column_NFCS_Name', 2, "Collection name's", 'cible','', 0 , 8),
('header_add_newsletter_filter_collection_text', 1, "Ajout d'une collection de filtres", 'cible','', 0 , 8),
('header_add_newsletter_filter_collection_text', 2, "Adding a collection filter", 'cible','', 0 , 8),
('header_add_newsletter_filter_collection_description', 1, "Une collection permet de rassembler plusieurs filtres<br>afin de faire une liste d'envoi dynamique.", 'cible','', 0 , 8),
('header_add_newsletter_filter_collection_description', 2, "A collection brings together several filters to make a list of dynamics sends", 'cible','', 0 , 8),
('link_add_newsletter_filterSet', 1, "Ajouter un ensemble de filtre", 'cible','', 0 , 8),
('link_add_newsletter_filterSet', 2, "Add a set of filter", 'cible','', 0 , 8),
('form_label_collection_name', 1, "Nom de la collection :", 'cible','', 0 , 8),
('form_label_collection_name', 2, "Collection name", 'cible','', 0 , 8),
('newsletter_filterset_title', 1, "Ensemble de filtres", 'cible','', 0 , 8),
('newsletter_filterset_title', 2, "Ensemble de filtres", 'cible','', 0 , 8),
('link_add_newsletter_filter', 1, "Ajouter un filtre", 'cible','', 0 , 8),
('link_add_newsletter_filter', 2, "Add a filter to this group", 'cible','', 0 , 8),
('link_delete_newsletter_filterSet', 1, "Supprimer cet ensemble", 'cible','', 0 , 8),
('link_delete_newsletter_filterSet', 2, "Delete all filter", 'cible','', 0 , 8),
('link_delete_newsletter_filter', 1, "Supprimer", 'cible','', 0 , 8),
('link_delete_newsletter_filter', 2, "Delete this filter", 'cible','', 0 , 8),
('newsletter_send_filter_selectOne', 1, "--- Choissisez un filtre ---", 'cible','', 0 , 8),
('newsletter_send_filter_selectOne', 2, "--- Choose a filter ---", 'cible','', 0 , 8),
('header_edit_newsletter_filter_collection_text', 1, "Édition d'une collection de filtres", 'cible','', 0 , 8),
('header_edit_newsletter_filter_collection_text', 2, "Editing a collection filter", 'cible','', 0 , 8),
('header_edit_newsletter_filter_collection_description', 1, "Une collection permet de rassembler plusieurs filtres<br>afin de faire une liste d'envoi dynamique.", 'cible','', 0 , 8),
('header_edit_newsletter_filter_collection_description', 2, "A collection brings together several filters to make a list of dynamics send", 'cible','', 0 , 8),
('filter_empty_diffusion', 1, "Par diffusion", 'cible','', 0 , 8),
('filter_empty_diffusion', 2, "Par diffusion", 'cible','', 0 , 8),
('header_view_newsletter_email_title', 1, "Aperçu courriel", 'cible','', 0 , 8),
('header_view_newsletter_email_title', 2, "Aperçu courriel", 'cible','', 0 , 8),
('header_view_newsletter_email_description', 1, "Cette page vous donne un aperçu de la parution qui sera envoyée lors de l'envoi massif", 'cible','', 0 , 8),
('header_view_newsletter_email_description', 2, "Cette page vous donne un aperçu de la parution qui sera envoyée lors de l'envoi massif", 'cible','', 0 , 8),
('header_view_newsletter_web_title', 1, "Aperçu Web", 'cible','', 0 , 8),
('header_view_newsletter_web_title', 2, "Aperçu Web", 'cible','', 0 , 8),
('header_view_newsletter_web_description', 1, "Cette page vous donne un aperçu de la parution qui sera affichée sur votre site Internet", 'cible','', 0 , 8),
('header_view_newsletter_web_description', 2, "Cette page vous donne un aperçu de la parution qui sera affichée sur votre site Internet", 'cible','', 0 , 8),
('header_manage_send_newsletter_text', 1, "Infolettre : Préparation", 'cible','', 0 , 8),
('header_manage_send_newsletter_text', 2, "Newsletter preparation", 'cible','', 0 , 8),
('header_manage_send_newsletter_description', 1, "Préparation de l'envoi de l'infolettre", 'cible','', 0 , 8),
('header_manage_send_newsletter_description', 2, "Preparation of sending the newsletter", 'cible','', 0 , 8),
('header_add_newsletter_article_title', 1, "Ajout d'un article à une parution", 'cible','', 0 , 8),
('header_add_newsletter_article_title', 2, "Ajout d'un article à une parution", 'cible','', 0 , 8),
('header_add_newsletter_article_description', 1, "Cette page vous permet d'ajouter le texte d'un article qui apparaît dans une parution d'une infolettre ", 'cible','', 0 , 8),
('header_add_newsletter_article_description', 2, "Cette page vous permet d'ajouter le texte d'un article qui apparaît dans une parution d'une infolettre ", 'cible','', 0 , 8),
('see_details_newsletter_text', 1, "Lire la suite", 'client','', 0 , 8),
('see_details_newsletter_text', 2, "Read more", 'client','', 0 , 8),
('form_select_option_view_newsletter_list_archives', 1, "Afficher la liste des archives", 'cible','', 0 , 8),
('form_select_option_view_newsletter_list_archives', 2, "Afficher la liste des archives", 'cible','', 0 , 8),
('newsletter_no_archives_client', 1, "Il n'y a aucune archive.", 'client','', 0 , 8),
('newsletter_no_archives_client', 2, "There are no archives.", 'client','', 0 , 8),
('newsletter_back_button_client', 1, "Retour à la parution", 'client','', 0 , 8),
('header_list_newsletter_recipient_text', 2, "Registration management", 'cible','', 0 , 8),
('header_list_newsletter_recipient_Description', 2, 'Click on <b>Add a new person</b><br>to add a recipient.<br><br>You can <b>search by keywords</b> into <br>the suscriber list. To go back to the whole list,<br>click on <b>See the complete list</b>.<br><br>You can <b>modify or delete the account<br>of a user</b> by a click on the icon <img src="/edith/icons/icon-add-24x24.png" align=middle>.', 'cible','', 0 , 8),
('header_newsletter_statistic_title', 1, "Journal de l'activité pour l'infolettre", 'cible','', 0 , 8),
('header_newsletter_statistic_title', 2, 'Log of the newsletter activity', 'cible','', 0 , 8),
('header_newsletter_statistic_description', 1, "Cette page affiche des informations sur le nombre de consultations, abonnements et désabonnements.<br />
Il est possible de filtrer les données affichées par parution, catégorie ou par date selon le rapport sélectionné. <br />
En cliquant sur les chiffres, des informations complémentaires seront affichées.", 'cible','', 0 , 8),
('header_newsletter_statistic_description', 2, 'This page displays informations about the newsletter : consultation, subscriptions or unsubscription. <br />
It allows to filter data by date, release or category according to the selected report. <br />
Clicking on numbers, it will disdplay additional informations', 'cible','', 0 , 8),
('management_module_newsletter_list_all', 2, "All the publications", 'cible','', 0 , 8),
('newsletter_captcha_label', 1, "<br /><br />Pour des raisons de sécurité, veuillez entrer les caractères alphanumériques de l'image dans l'espace ci-dessous.", 'client','', 0 , 8),
('newsletter_captcha_label', 2, '<br /><br />For security reasons, please enter the alphanumeric <br />characters from the image into the space below.', 'client', '', 0, 8);

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID) VALUES
('newsletter_send_filter_NP_Categories', 1, "Abonné à l'infolettre", 'cible', '', 0 , 8),
('newsletter_send_filter_NP_Categories', 2, 'Subscriber to newsletter', 'cible', '', 0 , 8),
('newsletter_send_filter_GP_Language', 2, 'Language', 'cible', '', 0 , 8),
('newsletter_send_filter_GP_Language', 1, 'Langue', 'cible', '', 0 , 8),
('form_select_option_view_newsletter_show_web', '1', 'Prévisualisation d''une parution', 'cible', '', 0 , 8),
('form_select_option_view_newsletter_show_web', '2', 'Newsletter preview', 'cible', '', 0 , 8),
('form_select_option_view_newsletter_show_web_details', '1', 'Prévisualisation d''un article', 'cible', '', 0 , 8),
('form_select_option_view_newsletter_show_web_details', '2', 'Article preview', 'cible', '', 0 , 8),
('header_send_newsletter_description', '1', 'Envoi d''une infolettre à la liste', 'cible', '', 0 , 8),
('header_send_newsletter_description', '2', 'Sending a newsletter to the list', 'cible', '', 0 , 8),
('massmailing_message_inprogress', '1', 'Début de l''envoi de l''infolettre', 'cible', '', 0 , 8),
('massmailing_message_inprogress', '2', 'Start sending the newsletter', 'cible', '', 0 , 8),
('massmailing_message_execution1','1','Début :  %DATE% à %TIME%','cible','', 0 , 8),
('massmailing_message_execution1','2','Start :  %DATE% at %TIME%','cible','', 0 , 8),
('massmailing_message_execution_wait','1',"Envoi de l'infolettre en cours (ne pas quitter cette page avant la parution du bouton 'retour')",'cible','', 0 , 8),
('massmailing_message_execution_wait','2',"Envoi de l'infolettre en cours (ne pas quitter cette page avant la parution du bouton 'retour')",'cible','', 0 , 8),
('massmailing_message_execution2','1',"Fin : %DATE% à %TIME%",'cible','', 0 , 8),
('massmailing_message_execution2','2',"End: %DATE% at %TIME%",'cible','', 0 , 8),
('massmailing_message_execution3','1',"Envoyé à %SENT_TO% sur %TARGETED_TOTAL% au total.",'cible','', 0 , 8),
('massmailing_message_execution3','2',"Sent to %SENT_TO% on %TARGETED_TOTAL% total.",'cible','', 0 , 8),
('newsletter_release_sent_stats', '1', 'Fin de l''envoie de l''infolettre', 'cible', '', 0 , 8),
('newsletter_release_sent_stats', '2', 'End of sending the newsletter', 'cible', '', 0 , 8),
('header_send_newsletter_text', '1', 'Envoi d''infolettre', 'cible', '', 0 , 8),
('header_send_newsletter_text', '2', 'Sending newsletter', 'cible', '', 0 , 8),
('newsletter_title_abonnement_text', '1', 'Abonnement', 'cible','', 0, 8),
('newsletter_title_abonnement_text', '2', 'Subscribe', 'cible','', 0, 8),
('newsletter_fo_form_label_securityCaptcha_newsletter', '1', 'Pour des raisons de sécurité, veuillez entrer les caractères<br /> alphanumériques de l''image dans l''espace ci-dessous.', 'cible', '', '0', '8'),
('newsletter_fo_form_label_securityCaptcha_newsletter', '2', 'For security reasons, please enter the alphanumeric <br />characters from the image in the space below.', 'cible', '', '0', '8'),
('form_select_option_zoneViews_7', '1', 'Infolettre', 'cible', '', 0 , 8),
('form_select_option_zoneViews_7', '2', 'Newsletter', 'cible', '', 0 , 8),
('newsletter_title_desabonnement_text', '1', 'Désabonnement', 'cible', '', 0, 8),
('newsletter_title_desabonnement_text', '2', 'Unsubscribe', 'cible', '', 0, 8),
('newsletter_button_return_to_newsletter', '1', 'Retour à l''infolettre', 'cible', '', 0, 8),
('newsletter_button_return_to_newsletter', '2', 'Return to the newsletter', 'cible', '', 0, 8),
('form_select_option_view_newsletter_show_web', '1', 'Infolettre preview', 'cible', '', '0', 8),
('form_select_option_view_newsletter_show_web', '2', 'Newsletter preview', 'cible', '', '0', 8),
('form_select_option_view_newsletter_show_web_details', '1', 'Infolettre preview en détails', 'cible', '', '0', 8),
('form_select_option_view_newsletter_show_web_details', '2', 'Newsletter preview in details', 'cible', '', '0', 8),
('form_select_option_zoneViews_7', '1', 'Infolettre', 'cible', '', '0', 8),
('form_select_option_zoneViews_7', '2', 'Newsletter', 'cible', '', '0', 8),
('newsletter_title_archives_text', '1', 'Archives', 'cible', '', 0, 8),
('newsletter_title_archives_text', '2', 'Archives', 'cible', '', 0, 8),
('email_not_show_go_online1', '1', 'Si ce courriel ne s''affiche pas correctement, veuillez consulter', 'cible', '', 0, 8),
('email_not_show_go_online1', '2', 'If this email does not display correctly, please consult', 'cible', '', 0, 8),
('email_not_show_go_online2', '1', ' la parution en ligne.', 'cible', '', 0, 8),
('email_not_show_go_online2', '2', ' the online edition.', 'cible', '', 0, 8);

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID) VALUES
('header_edit_newsletter_article_title', '1', 'Édition', 'cible', '', '0', '8'),
('header_edit_newsletter_article_title', '2', 'Edit', 'cible', '', '0', '8'),
('header_edit_newsletter_article_description', '1', 'Édition d''un article de l''infolettre', 'cible', '', '0', '8'),
('header_edit_newsletter_article_description', '2', 'Edit article newsletter', 'cible', '', '0', '8'),
('joindre_fo_form_label_confident_joindre', '1', 'Politique de confidentialité', 'cible', '', '0', '8'),
('joindre_fo_form_label_confident_joindre', '2', 'Confidentiality politics', 'cible', '', '0', '8'),
('footer_site_web', '1', 'www.ciblesolutions.com', 'cible', '', '0', '0'),
('footer_site_web', '2', 'www.ciblesolutions.com', 'cible', '', '0', '0'),
('form_label_unsubscribe_reason', '1', 'Raison', 'cible', '', '0', '8'),
('form_label_unsubscribe_reason', '2', 'Reason', 'cible', '', '0', '8'),
('form_label_filterDate',  '1',  'Filtrer pour l''année',  'cible',  '',  '0', '8'),
('form_label_filterDate',  '2',  'Filter for the year',  'cible',  '',  '0', '8'),
('infolettre_text_salutation', '1', '<p class="salutation_infolettre">Bonjour, [prenom]</p>', 'client', 'Le texte qui sera afficher en haut de l''infolettre lors de l''envoie par courriel si aucun text n''a été spécifiee lors de la création d''une parution.', '1', '8'),
('infolettre_text_salutation', '2', '<p class="salutation_infolettre">Hello, [prenom]</p>', 'client', 'Le texte qui sera afficher en haut de l''infolettre lors de l''envoie par courriel si aucun text n''a été spécifiee lors de la création d''une parution.', '1', '8'),
('newsletter_notification_admin_email_subject', '1', "Infolettre envoyée", 'cible', '', '0', '8'),
('newsletter_notification_admin_email_subject', '2', "Newsletter sent", 'cible', '', '0', '8'),
('newsletter_notification_admin_email_message', '1', "Envoi d'infolettre terminé.<br /><br />
<table style=\"width: 500px;\" border=\"0\">
    <tbody>
        <tr>
            <td>Nombre d'infolettre envoy&eacute;es</td>
            <td>&nbsp;##NUMBER_OF_NEWSLETTER##</td>
        </tr>
        <tr>
            <td>Fin des op&eacute;rations</td>
            <td>&nbsp;##END_TIME_SENDING##</td>
        </tr>
        <tr>
            <td>Nombre total &agrave; envoyer</td>
            <td>&nbsp;##NB_TOTAL_TO_SEND##</td>
        </tr>
        <tr>
            <td>Nombre total d'envoi</td>
            <td>&nbsp;##NB_TOTAL_SENT##</td>
        </tr>
        <tr>
            <td>Nombre de courriel invalide (format)</td>
            <td>&nbsp;##NB_INVALID_EMAIL##</td>
        </tr>
        <tr>
            <td>Nombre d'erreur lors de l'envoi</td>
            <td>&nbsp;##NB_SENDING_ERRORS##</td>
        </tr>
    </tbody>
</table> <br />
Liste des infolettres : <br />
", 'cible', '', '0', '8'),
('newsletter_notification_admin_email_message', '2', "Newsletter sent.<br /><br />
<table style=\"width: 500px;\" border=\"0\">
            <tbody>
                <tr>
                    <td>Number of newsletter sent</td>
                    <td>&nbsp;##NUMBER_OF_NEWSLETTER##</td>
                </tr>
                <tr>
                    <td>Process finished at</td>
                    <td>&nbsp;##END_TIME_SENDING##</td>
                </tr>
                <tr>
                    <td>Nombre total &agrave; envoyer</td>
                    <td>&nbsp;##NB_TOTAL_TO_SEND##</td>
                </tr>
                <tr>
                    <td>Total of mailing</td>
                    <td>&nbsp;##NB_TOTAL_SENT##</td>
                </tr>
                <tr>
                    <td>Total of invalid email (format)</td>
                    <td>&nbsp;##NB_INVALID_EMAIL##</td>
                </tr>
                <tr>
                    <td>Total of errors during mailing</td>
                    <td>&nbsp;##NB_SENDING_ERRORS##</td>
                </tr>
            </tbody>
        </table><br />
Newsletter list: <br /> ", 'cible', '', '0', '8'),
('newsletter_notification_admin_alert', 1, '<strong>Des erreurs sont survenues lors de l''envoi. Contactez votre administrateur pour plus de renseignements.</strong><br /><br />', 'cible', '', 0, 8),
('newsletter_notification_admin_alert', 2, '<strong>Errors occur during mailing process. Please, contact your administrator for further informations.</strong><br /><br />', 'cible', '', 0, 8),
('newsletter_notification_admin_title', 1, 'Rapport sur l''envoi d''infolettre', 'cible', '', 0, 8),
('newsletter_notification_admin_title', 2, 'Newsletter mailing report', 'cible', '', 0, 8),
('newsletter_release_failed_mail', 1, 'L''envoi au(x) membre(s) suivant a échoué car leur adresse courriel est invalide.', 'cible', '', 0, 8),
('newsletter_release_failed_mail', 2, 'The following members sent failed because their email address is invalid.', 'cible', '', 0, 8),
('newsletter_release_failed_lname', 1, 'Nom', 'cible', '', 0, 8),
('newsletter_release_failed_lname', 2, 'Name', 'cible', '', 0, 8),
('newsletter_release_failed_fname', 1, 'Prénom', 'cible', '', 0, 8),
('newsletter_release_failed_fname', 2, 'Surname', 'cible', '', 0, 8),
('newsletter_release_failed_email', 1, 'Courriel', 'cible', '', 0, 8),
('newsletter_release_failed_email', 2, 'Email', 'cible', '', 0, 8),
('newsletter_no_newsletter', 1, 'Il n''a pas d''infolettre.', 'cible', '', 0, 8),
('newsletter_no_newsletter', 2, 'There is no newsletter.', 'cible', '', 0, 8),
('form_label_newsletter_text_intro', '1', 'Texte d''intro (vous pouvez utiliser [prenom], [nom] et [salutation])', 'cible', '', '0', '8'),
('form_label_newsletter_text_intro', '2', 'Texte d''intro (vous pouvez utiliser [prenom], [nom] et [salutation])', 'cible', '', '0', '8'),
('extranet_newsletter_option_text_url', '1', 'Lien vers :', 'cible', '', '0', '8'),
('extranet_newsletter_option_text_url', '2', 'Link to :', 'cible', '', '0', '8'),
('extranet_newsletter_option_text_url_text', '1', 'Vers le texte long', 'cible', '', '0', '8'),
('extranet_newsletter_option_text_url_text', '2', 'To the long text', 'cible', '', '0', '8'),
('extranet_newsletter_option_text_url_url', '1', 'Vers le url', 'cible', '', '0', '8'),
('extranet_newsletter_option_text_url_url', '2', 'To the url', 'cible', '', '0', '8'),
('extranet_newsletter_option_text_url_nothing', '1', 'Aucun', 'cible', '', '0', '8'),
('extranet_newsletter_option_text_url_nothing', '2', 'None', 'cible', '', '0', '8'),
('extranet_newsletter_text_url', '1', 'URL', 'cible', '', '0', '8'),
('extranet_newsletter_text_url', '2', 'URL', 'cible', '', '0', '8'),
('extranet_newsletter_return_list_newsletter', '1', 'Liste des infolettres', 'cible', '', '0', '8'),
('extranet_newsletter_return_list_newsletter', '2', 'Newsletters'' list', 'cible', '', '0', '8');

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID) VALUES
('newsletter_statistic_latest_activity_label', 1, "Dernières activités", 'cible', '', 0 , 8),
('newsletter_statistic_latest_activity_label', 2, 'Latest activity', 'cible', '', 0 , 8),
('newsletter_statistic_subscription_label', 1, "Abonnements ", 'cible', '', 0 , 8),
('newsletter_statistic_subscription_label', 2, 'Subscription', 'cible', '', 0 , 8),
('newsletter_statistic_unsubscription_label', 1, "Désabonnements ", 'cible', '', 0 , 8),
('newsletter_statistic_unsubscription_label', 2, 'Unsubscriptions', 'cible', '', 0 , 8),
('newsletter_statistic_lastmailing_label', 1, "Dernier envoi ", 'cible', '', 0 , 8),
('newsletter_statistic_lastmailing_label', 2, 'Last mailing', 'cible', '', 0 , 8),
('newsletter_statistic_reports_label', 1, "Rapports", 'cible', '', 0 , 8),
('newsletter_statistic_reports_label', 2, 'Reports', 'cible', '', 0 , 8),
('newsletter_statistic_releases_label', 1, "Parutions", 'cible', '', 0 , 8),
('newsletter_statistic_releases_label', 2, 'Releases', 'cible', '', 0 , 8),
('newsletter_statistic_allreleases_label', 1, "Toutes les parutions", 'cible', '', 0 , 8),
('newsletter_statistic_allreleases_label', 2, 'All releases', 'cible', '', 0 , 8),
('newsletter_statistic_categories_label', 1, "Catégories", 'cible', '', 0 , 8),
('newsletter_statistic_categories_label', 2, 'Categories', 'cible', '', 0 , 8),
('newsletter_statistic_allcategories_label', 1, "Toutes les catégories", 'cible', '', 0 , 8),
('newsletter_statistic_allcategories_label', 2, 'All categories', 'cible', '', 0 , 8),
('newsletter_statistic_datefilter_label', 1, "Filtrer pour la période", 'cible', '', 0 , 8),
('newsletter_statistic_datefilter_label', 2, 'Limit data results by date', 'cible', '', 0 , 8),
('newsletter_statistic_startdate_label', 1, "Début :", 'cible', '', 0 , 8),
('newsletter_statistic_startdate_label', 2, 'Start:', 'cible', '', 0 , 8),
('newsletter_statistic_enddate_label', 1, "Fin :", 'cible', '', 0 , 8),
('newsletter_statistic_enddate_label', 2, 'End:', 'cible', '', 0 , 8),
('newsletter_statistic_consultations_label', 1, "Consultations", 'cible', '', 0 , 8),
('newsletter_statistic_consultations_label', 2, 'Consultations', 'cible', '', 0 , 8),
('newsletter_statistic_totalsend_label', 1, "Envois/total à envoyer", 'cible', '', 0 , 8),
('newsletter_statistic_totalsend_label', 2, 'Sent/total to send', 'cible', '', 0 , 8),
('newsletter_statistic_opened_label', 1, "Courriels ouverts", 'cible', '', 0 , 8),
('newsletter_statistic_opened_label', 2, 'Mails opened', 'cible', '', 0 , 8),
('newsletter_statistic_totalread_label', 1, "Nombre de lectures", 'cible', '', 0 , 8),
('newsletter_statistic_totalread_label', 2, 'Total of views', 'cible', '', 0 , 8),
('newsletter_statistic_reciplist_title', 1, "Liste des destinataires de l'infolettre", 'cible', '', 0 , 8),
('newsletter_statistic_reciplist_title', 2, 'List of the recipients of the newsletter', 'cible', '', 0 , 8),
('newsletter_statistic_openedlist_title', 1, "Liste des personnes ayant ouvert l'infolettre", 'cible', '', 0 , 8),
('newsletter_statistic_openedlist_title', 2, 'List of users who have opened the newsletter', 'cible', '', 0 , 8),
('newsletter_statistic_showdetails_title', 1, "Afficher/masquer le détail par article", 'cible', '', 0 , 8),
('newsletter_statistic_showdetails_title', 2, 'Show/hide details for the articles', 'cible', '', 0 , 8),
('newsletter_statistic_articleview_title', 1, "Liste des personnes ayant consulté l'article", 'cible', '', 0 , 8),
('newsletter_statistic_articleview_title', 2, 'List of users who have read the article', 'cible', '', 0 , 8),
('newsletter_statistic_total_label', 1, "Total", 'cible', '', 0 , 8),
('newsletter_statistic_total_label', 2, 'Total', 'cible', '', 0 , 8),
('newsletter_statistic_subscribe_title', 1, "Liste des nouveaux inscrits à la catégorie", 'cible', '', 0 , 8),
('newsletter_statistic_subscribe_title', 2, 'List of the new subscribers to the category', 'cible', '', 0 , 8),
('newsletter_statistic_unsubscription_title', 1, "Liste des désabonnements de l'infolettre", 'cible', '', 0 , 8),
('newsletter_statistic_unsubscription_title', 2, 'List of the unsubscription  to the newsletter', 'cible', '', 0 , 8),
('newsletter_statistic_unsubother_label', 1, "Autre (pas de parution)", 'cible', '', 0 , 8),
('newsletter_statistic_unsubother_label', 2, 'Others (no release)', 'cible', '', 0 , 8),
('newsletter_statistic_fisrtlastname_label', 1, "Prénom Nom", 'cible', '', 0 , 8),
('newsletter_statistic_fisrtlastname_label', 2, 'Firstname Lastname', 'cible', '', 0 , 8),
('newsletter_statistic_email_label', 1, "Courriel", 'cible', '', 0 , 8),
('newsletter_statistic_email_label', 2, 'Email', 'cible', '', 0 , 8),
('newsletter_statistic_lastaccess_label', 1, "Dernier accès", 'cible', '', 0 , 8),
('newsletter_statistic_lastaccess_label', 2, 'Last access', 'cible', '', 0 , 8),
('newsletter_statistic_totallogged_label', 1, "Total visiteurs identifiés", 'cible', '', 0 , 8),
('newsletter_statistic_totallogged_label', 2, 'Total of logged users', 'cible', '', 0 , 8),
('newsletter_statistic_notlogged_label', 1, "Vues par des visiteurs non-identifiés", 'cible', '', 0 , 8),
('newsletter_statistic_notlogged_label', 2, 'Views of non-identified users', 'cible', '', 0 , 8),
('newsletter_statistic_unsubother_label', 1, "Autres (non identifié)", 'cible', '', 0 , 8),
('newsletter_statistic_unsubother_label', 2, 'Others (not identified)', 'cible', '', 0 , 8),
('newsletter_statistic_unsubother_linkToReason', 1, "Liste des raisons de désabonnements.", 'cible', '', 0 , 8),
('newsletter_statistic_unsubother_linkToReason', 2, 'List of reasons', 'cible', '', 0 , 8),
('newsletter_statistic_unsubother_linkToReason_title', 1, "Liste des raisons de désabonnements .", 'cible', '', 0 , 8),
('newsletter_statistic_unsubother_linkToReason_title', 2, 'List of reasons', 'cible', '', 0 , 8),
('extranet_newsletter_return_list_newsletter', '2', 'Newsletters'' list', 'cible', '', '0', '8'),
('form_extranet_newsletter_label_releaseDate', '1', 'Date d''affichage', 'cible', '', '0', '8'),
('form_extranet_newsletter_label_releaseDate', '2', 'Date of release', 'cible', '', '0', '8'),
('form_extranet_newsletter_label_releaseDate', '1', 'Date de parution', 'cible', '', '0', '8'),
('form_extranet_newsletter_label_releaseDate', '2', 'Release date', 'cible', '', '0', '8'),
('profile_tab_title_newsletter', '1', 'Infolettre', 'cible', '', '0', '0'),
('profile_tab_title_newsletter', '2', 'Newsletter', 'cible', '', '0', '0'),
('profile_title_newsletter', 1, 'Infolettre', 'cible', '', 0, 0),
('profile_title_newsletter', 2, 'Newsletter', 'cible', '', 0, 0),
('profile_addto_newletter_label', 1, 'Cocher une case ajoute l''utilisateur dans la liste des destinataires de cette infolettre.', 'cible', '', 0, 8),
('profile_addto_newletter_label', 2, 'Tick a checkbox will add the user the the recipents list of the newsletter.', 'cible', '', 0, 8),
('newsletter_statistic_diagtitle_title', 1, "Détails", 'cible', '', 0 , 8),
('newsletter_statistic_diagtitle_title', 2, 'Détails', 'cible', '', 0 , 8),
('newsletter_statistic_totalview_label', 1, 'Nombre total de vues', 'cible', '', 0, 8),
('newsletter_statistic_totalview_label', 2, 'Total of views', 'cible', '', 0, 8);

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID) VALUES
('log_arg_param_articleId', 1, "Titre de l'article", 'cible', '', 0 , 8),
('log_arg_param_articleId', 2, 'Title of the article', 'cible', '', 0 , 8),
('log_arg_param_opened', 1, "Ouverture", 'cible', '', 0 , 8),
('log_arg_param_opened', 2, 'Opened', 'cible', '', 0 , 8),
('log_arg_param_subscribe', 1, "Abonnement", 'cible', '', 0 , 8),
('log_arg_param_subscribe', 2, 'Subscription', 'cible', '', 0 , 8),
('log_arg_param_unsubscribe', 1, "Désabonnement", 'cible', '', 0 , 8),
('log_arg_param_unsubscribe', 2, 'Unsubscription', 'cible', '', 0 , 8),
('log_arg_param_details', 1, "Accès article", 'cible', '', 0 , 8),
('log_arg_param_details', 2, 'Accès article', 'cible', '', 0 , 8),
('log_arg_param_sending', 1, "Envoi", 'cible', '', 0 , 8),
('log_arg_param_sending', 2, 'Sending', 'cible', '', 0 , 8),
('log_arg_param_releaseId', 1, "Titre", 'cible', '', 0 , 8),
('log_arg_param_releaseId', 2, 'Title', 'cible', '', 0 , 8),
('log_arg_param_categoryId', 1, "Catégorie", 'cible', '', 0 , 8),
('log_arg_param_categoryId', 2, 'Category', 'cible', '', 0 , 8),
('log_arg_param_subscrArg', 1, "Raison", 'cible', '', 0 , 8),
('log_arg_param_subscrArg', 2, 'Raison', 'cible', '', 0 , 8),
('header_delete_newsletter_article_title', '1', 'Supression d''un article', 'cible', '', 0, 8),
('header_delete_newsletter_article_title', '2', 'Delete an article', 'cible', '', 0, 8),
('header_delete_newsletter_article_description', '1', 'Cette page permet la suppression d''un article ', 'cible', '', 0, 8),
('header_delete_newsletter_article_description', '2', 'This page allows to delete an article', 'cible', '', 0, 8),
('newsletter_other_label', '1', 'Autre', 'cible', '', 0, 8),
('newsletter_other_label', '2', 'Other', 'cible', '', 0, 8),
('log_arg_param_unsubscrArg', 1, "Raison", 'cible', '', 0 , 8),
('log_arg_param_unsubscrArg', 2, 'Reason', 'cible', '', 0 , 8),
('newsletter_title_desabonnement_other', '1', 'Autre', 'cible', '', 0, 8),
('newsletter_title_desabonnement_other', '2', 'Other', 'cible', '', 0, 8),
("form_select_option_view_newsletter_resubscribe",1,"Ré-inscription",'cible',"",0,8),
("form_select_option_view_newsletter_resubscribe",2,"Resubscribe",'cible',"",0,8),
("newsletter_title_reabonnement_text",1,"Réabonnement ",'client',"Titre Message loi anti-spam",1,8),
("newsletter_title_reabonnement_text",2,"Re-subscribe",'client',"Titre Message loi anti-spam",1,8),
("newsletter_reabonnement_text",1,"Afin de nous conformer à la Loi canadienne anti-pourriel, nous devons demander votre consentement pour vous faire parvenir nos messages. Les gens qui ne se seront pas réabonnés ne recevront plus d'information de notre part.",'client',"Message loi anti-spam",1,8),
("newsletter_reabonnement_text",2,"In order to comply with the Canadian Anti-Spam Legislation, we must ask your permission to send you our messages. People who do not subscribed again will no longer receive information from us.",'client',"Message loi anti-spam",1,8),
("form_label_agree",1,"Je consens à recevoir les communications électroniques de ##SITENAME##, celle-ci incluant des nouvelles, des mises à jour, des offres et des promotions. Il est possible de retirer mon consentement à tout moment.",'cible',"",0,8),
("form_label_agree",2,"I agree to receive ##SITENAME## electronic communications including news, updates, promotions and offers. You can withdraw your consent at any time.",'cible',"",0,8),
("newsletter_your_email_address",1,"Votre adresse courriel : ",'cible',"",0,8),
("newsletter_your_email_address",2,"Your email address: ",'cible',"",0,8),
("form_enum_typeClient",1,"Type de profil",'cible',"",0,8),
("form_enum_typeClient",2,"Profile type",'cible',"",0,8),
("form_enum_datelimit_gt6m",1,"Plus de 6 mois",'cible',"",0,8),
("form_enum_datelimit_gt6m",2,"More than 6 month",'cible',"",0,8),
("form_enum_datelimit_lt6m",1,"Moins de 6 mois",'cible',"",0,8),
("form_enum_datelimit_lt6m",2,"Less than 6 month",'cible',"",0,8),
("form_enum_datelimit_gt2y",1,"Plus de 2 ans",'cible',"",0,8),
("form_enum_datelimit_gt2y",2,"More than 2 years",'cible',"",0,8),
("form_enum_datelimit_lt2y",1,"Moins de 2 ans",'cible',"",0,8),
("form_enum_datelimit_lt2y",2,"Less than 2 years",'cible',"",0,8),
("form_enum_datelimit_gt3y",1,"Plus de 3 ans",'cible',"",0,8),
("form_enum_datelimit_gt3y",2,"More than 3 yars",'cible',"",0,8),
("form_enum_datelimit_lt3y",1,"Moins de 3 ans",'cible',"",0,8),
("form_enum_datelimit_lt3y",2,"Less than 3 years",'cible',"",0,8),
("newsletter_send_filter_NP_SubscriptionDate",1,"Date d'inscription",'cible',"",0,8),
("newsletter_send_filter_NP_SubscriptionDate",2,"Subscription date",'cible',"",0,8),
("newsletter_send_filter_NP_TypeID",1,"Type de profil",'cible',"",0,8),
("newsletter_send_filter_NP_TypeID",2,"Type de profil",'cible',"",0,8);
REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_RichText`) VALUES
('footer_newsletter_title', 1, 'Inscription à l''infolettre', 'client', '', 0, 8, 1),
('footer_newsletter_title', 2, 'Newsletter subscription', 'client', '', 0, 8, 1),
('newsletter_fo_form_placeholder_lName', 1, 'Nom', 'cible', '', 0, 8, 1),
('newsletter_fo_form_placeholder_lName', 2, 'Name', 'cible', '', 0, 8, 1),
('newsletter_fo_form_placeholder_fName', 1, 'Prénom', 'cible', '', 0, 8, 1),
('newsletter_fo_form_placeholder_fName', 2, 'Firstname', 'cible', '', 0, 8, 1),
('newsletter_fo_form_placeholder_email', 1, 'Courriel', 'cible', '', 0, 8, 1),
('newsletter_fo_form_placeholder_email', 2, 'Email', 'cible', '', 0, 8, 1),
('forms_label_placeholder_email', 1, 'Courriel', 'client', '', 0, 8, 1),
('forms_label_placeholder_email', 2, 'Email', 'client', '', 0, 8, 1),
('button_submit_newsletter_footer', 1, 'Soumettre', 'cible', '', 0, 8, 1),
('button_submit_newsletter_footer', 2, 'Submit', 'cible', '', 0, 8, 1);
REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_RichText`) VALUES
('footer_newsletter_title', 1, 'Inscription à l''infolettre', 'client', '', 0, 8, 1),
('footer_newsletter_title', 2, 'Newsletter subscription', 'client', '', 0, 8, 1),
('newsletter_fo_form_placeholder_lName', 1, 'Nom', 'cible', '', 0, 8, 1),
('newsletter_fo_form_placeholder_lName', 2, 'Name', 'cible', '', 0, 8, 1),
('newsletter_fo_form_placeholder_fName', 1, 'Prénom', 'cible', '', 0, 8, 1),
('newsletter_fo_form_placeholder_fName', 2, 'Firstname', 'cible', '', 0, 8, 1),
('newsletter_fo_form_placeholder_email', 1, 'Courriel', 'cible', '', 0, 8, 1),
('newsletter_fo_form_placeholder_email', 2, 'Email', 'cible', '', 0, 8, 1),
('forms_label_placeholder_email', 1, 'Courriel', 'client', '', 0, 8, 1),
('forms_label_placeholder_email', 2, 'Email', 'client', '', 0, 8, 1),
('button_submit_newsletter_footer', 1, 'Soumettre', 'cible', '', 0, 8, 1),
('button_submit_newsletter_footer', 2, 'Submit', 'cible', '', 0, 8, 1),
('newsletter_status_delivered', 1, "Envoi terminé", "cible", "", 0, 8, 0),
('newsletter_status_delivered', 2, "Sending finished", "cible", "", 0, 8, 0),
('newsletter_status_pending', 1, "En cours d'envoi", "cible", "", 0, 8, 0),	('newsletter_status_pending', 2, "Sending", "cible", "", 0, 8, 0),
('newsletter_status_incomplete', 1, "Envoyez à tous les destinataires", "cible", "", 0, 8, 0),	('newsletter_status_incomplete', 2, "Send to all recipients", "cible", "", 0, 8, 0),
('newsletter_stats_label_opened', 1, "Ouverts", "cible", "", 0, 8, 0),	('newsletter_stats_label_opened', 2, "Opened", "cible", "", 0, 8, 0),
('newsletter_stats_label_unopened', 1, "Non ouverts", "cible", "", 0, 8, 0),	('newsletter_stats_label_unopened', 2, "Unopened", "cible", "", 0, 8, 0),
('newsletter_stats_label_bounce', 1, "Non livrés", "cible", "", 0, 8, 0),	('newsletter_stats_label_bounce', 2, "Bounced", "cible", "", 0, 8, 0),
('newsletter_stats_label_unsubscribe', 1, "Désabonnements", "cible", "", 0, 8, 0),	('newsletter_stats_label_unsubscribe', 2, "Unsubscriptions", "cible", "", 0, 8, 0),
('newsletter_stats_label_spam', 1, "Pourriels", "cible", "", 0, 8, 0),	('newsletter_stats_label_spam', 2, "Spam", "cible", "", 0, 8, 0),
('newsletter_stats_label_forward', 1, "Transférer", "cible", "", 0, 8, 0),	('newsletter_stats_label_forward', 2, "Forward", "cible", "", 0, 8, 0),
('newsletter_stats_label_in_queue', 1, "Destinataires", "cible", "", 0, 8, 0),	('newsletter_stats_label_in_queue', 2, "Recipients", "cible", "", 0, 8, 0),
('newsletter_stats_label_clickthru', 1, "Clics", "cible", "", 0, 8, 0),	('newsletter_stats_label_clickthru', 2, "Clicks", "cible", "", 0, 8, 0),
('email_bounce_label_all', 1, "Tous", "cible", "", 0, 8, 0),	('email_bounce_label_all', 2, "All", "cible", "", 0, 8, 0),
('email_bounce_label_bounce_ac', 1, "Changer l'adresse", "cible", "", 0, 8, 0),	('email_bounce_label_bounce_ac', 2, "Change Address", "cible", "", 0, 8, 0),
('email_bounce_label_bounce_ar', 1, "Réponse automatisée", "cible", "", 0, 8, 0),	('email_bounce_label_bounce_ar', 2, "Automated response", "cible", "", 0, 8, 0),
('email_bounce_label_bounce_cr', 1, "Essai/réponse (CR)", "cible", "", 0, 8, 0),	('email_bounce_label_bounce_cr', 2, "Test / response (CR)", "cible", "", 0, 8, 0),
('email_bounce_label_bounce_df', 1, "Problème de DNS", "cible", "", 0, 8, 0),	('email_bounce_label_bounce_df', 2, "DNS problem", "cible", "", 0, 8, 0),
('email_bounce_label_bounce_hb', 1, "Non-livrés permanents", "cible", "", 0, 8, 0),	('email_bounce_label_bounce_hb', 2, "Permanent non-delivered", "cible", "", 0, 8, 0),
('email_bounce_label_bounce_mb', 1, "Courriel bloqué", "cible", "", 0, 8, 0),	('email_bounce_label_bounce_mb', 2, "E-mail blocked", "cible", "", 0, 8, 0),
('email_bounce_label_bounce_fm', 1, "Boîte de courriel pleine", "cible", "", 0, 8, 0),	('email_bounce_label_bounce_fm', 2, "Full mailbox", "cible", "", 0, 8, 0),
('email_bounce_label_bounce_sb', 1, "Non-livrés temporaires", "cible", "", 0, 8, 0),	('email_bounce_label_bounce_sb', 2, "Temporary non-delivered", "cible", "", 0, 8, 0),
('email_bounce_label_bounce_tr', 1, "Tentatives de livraison", "cible", "", 0, 8, 0),	('email_bounce_label_bounce_tr', 2, "Delivery attempts", "cible", "", 0, 8, 0),
('email_spam_label_aol', 1, "AOL", "cible", "", 0, 8, 0),	('email_spam_label_aol', 2, "AOL", "cible", "", 0, 8, 0),
('email_spam_label_bluetie', 1, "BlueTie", "cible", "", 0, 8, 0),	('email_spam_label_bluetie', 2, "BlueTie", "cible", "", 0, 8, 0),
('email_spam_label_comcast', 1, "Comcast", "cible", "", 0, 8, 0),	('email_spam_label_comcast', 2, "Comcast", "cible", "", 0, 8, 0),
('email_spam_label_cox', 1, "Cox", "cible", "", 0, 8, 0),	('email_spam_label_cox', 2, "Cox", "cible", "", 0, 8, 0),
('email_spam_label_mailtrust', 1, "Mailtrust", "cible", "", 0, 8, 0),	('email_spam_label_mailtrust', 2, "Mailtrust", "cible", "", 0, 8, 0),
('email_spam_label_msn', 1, "MSN", "cible", "", 0, 8, 0),	('email_spam_label_msn', 2, "MSN", "cible", "", 0, 8, 0),
('email_spam_label_rr', 1, "Road Runner", "cible", "", 0, 8, 0),	('email_spam_label_rr', 2, "Road Runner", "cible", "", 0, 8, 0),
('email_spam_label_spamcop', 1, "SpamCop", "cible", "", 0, 8, 0),	('email_spam_label_spamcop', 2, "SpamCop", "cible", "", 0, 8, 0),
('email_spam_label_tucows', 1, "Tucows", "cible", "", 0, 8, 0),	('email_spam_label_tucows', 2, "Tucows", "cible", "", 0, 8, 0),
('email_spam_label_usanet', 1, "Usanet", "cible", "", 0, 8, 0),	('email_spam_label_usanet', 2, "Usanet", "cible", "", 0, 8, 0),
('email_spam_label_unitedonline', 1, "United Online", "cible", "", 0, 8, 0),	('email_spam_label_unitedonline', 2, "United Online", "cible", "", 0, 8, 0),
('email_spam_label_yahoo', 1, "Yahoo!", "cible", "", 0, 8, 0),	('email_spam_label_yahoo', 2, "Yahoo!", "cible", "", 0, 8, 0),
('newsletter_stats_dates_recipients', 1, "Créée le ##CREATEDATE##. Envoyée le ##STARTDATE## à ##NBRECIPIENTS## destinataires.", "cible", "", 0, 8, 0),	('newsletter_stats_dates_recipients', 2, "Created on the ##CREATEDATE##. Sent on the ##STARTDATE## to ##NBRECIPIENTS## recipients.", "cible", "", 0, 8, 0),
('newsletter_summary_opened', 1, "<strong>##NBOPENED##</strong> courriels ouverts, soit un un taux d'ouverture de <strong>##RATIO##</strong> %.", "cible", "", 0, 8, 0),	('newsletter_summary_opened', 2, "<strong>##NBOPENED##</strong> emails opened, representig an opening rate of <strong>##RATIO##</strong> %.", "cible", "", 0, 8, 0),
('newsletter_summary_clickthru', 1, "<strong>##NBCLICK##</strong> clics sur les liens, soit un un taux de clics de <strong>##RATIO##</strong> %.", "cible", "", 0, 8, 0),	('newsletter_summary_clickthru', 2, "", "cible", "", 0, 8, 0),
('newsletter_summary_bounce', 1, "<strong>##TOTALBOUNCE##</strong> non livrés dont <strong>##TOTALPERM##</strong> permanents, soit un un taux de non-livrés de <strong>##RATIO##</strong> %.", "cible", "", 0, 8, 0),	('newsletter_summary_bounce', 2, "<strong>##TOTALBOUNCE##</strong> non-delivered including <strong>##TOTALPERM##</strong> permanent, representing a rate of <strong>##RATIO##</strong> %.", "cible", "", 0, 8, 0),
('newsletter_summary_spam', 1, "<strong>##TOTSPAM##</strong> envois classés comme pourriels, soit un un taux de <strong>##RATIO##</strong> %.", "cible", "", 0, 8, 0),	('newsletter_summary_spam', 2, "<strong>##TOTSPAM##</strong> emails qualified as spam, representing a rate of <strong>##RATIO##</strong> %.", "cible", "", 0, 8, 0),
('newsletter_summary_unsubscribe', 1, "<strong>##TOTUNSUB##</strong> désabonnements, soit un un taux de <strong>##RATIO##</strong> %.", "cible", "", 0, 8, 0),	('newsletter_summary_unsubscribe', 2, "<strong>##TOTUNSUB##</strong> unsubscription, representig a rate of <strong>##RATIO##</strong> %.", "cible", "", 0, 8, 0),
('button_stats', 1, "Statistiques", "cible", "", 0, 8, 0),	('button_stats', 2, "Statistics", "cible", "", 0, 8, 0),
('newsletter_summary_linksInfo', 1, "Nombre de clics par lien", "cible", "", 0, 8, 0),	('newsletter_summary_linksInfo', 2, "Total of clicks by link", "cible", "", 0, 8, 0),
('newsletter_summary_link', 1, "Liens", "cible", "", 0, 8, 0),	('newsletter_summary_link', 2, "Links", "cible", "", 0, 8, 0),
('newsletter_summary_rate', 1, "Taux", "cible", "", 0, 8, 0),	('newsletter_summary_rate', 2, "Rate", "cible", "", 0, 8, 0),
('button_copy', 1, "Copier", "cible", "", 0, 0, 0),	('button_copy', 2, "Duplicate", "cible", "", 0, 0, 0),
('header_duplicate_newsletter_text', 1, "Copier une infolettre", "cible", "", 0, 0, 0),	('header_duplicate_newsletter_text', 2, "Duplicate a newsletter", "cible", "", 0, 0, 0),
('header_duplicate_newsletter_description', 1, "Cette page permet de créer une copie de l'infolettre sélectionnée. Après la copie, pensez à mettre à jour les paramètres comme en ligne ou la date de parution, par exemple.", "cible", "", 0, 0, 0),	('header_duplicate_newsletter_description', 2, "This page allows to duplicate the selected newsletter. After the copy do not forget to update the parameters (i.e. Date or online status).", "cible", "", 0, 0, 0),
('button_stats', 1, 'Statistiques', 'cible', '', 0, 8, 0),
('button_stats', 2, 'Statistics', 'cible', '', 0, 8, 0),
('button_copy', 1, 'Copier', 'cible', '', 0, 0, 0),
('button_copy', 2, 'Duplicate', 'cible', '', 0, 0, 0),
('newsletter_form_label_admin_email', 1, 'Courriel de l''administrateur à informer', 'cible', '', 0, 8, 0),
('newsletter_form_label_admin_email', 2, 'Email of the admin to notify', 'cible', '', 0, 8, 0),
('newsletter_form_label_admin_email', 2, 'Email of the admin to notify', 'cible', '', 0, 8, 0),
('newsletter_form_label_admin_email', 1, 'Courriel de l''administrateur à informer', 'cible', '', 0, 8, 0);
