-- phpMyAdmin SQL Dump
-- version 3.1.3
-- http://www.phpmyadmin.net
--
-- Host: 209.222.235.12:3306
-- Generation Time: Sep 24, 2010 at 03:55 PM
-- Server version: 5.0.70
-- PHP Version: 5.2.10-pl0-gentoo
-- Version SVN: $Id: moduleEventsCreate.sql 1872 2016-02-09 19:58:47Z ssoares $

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Table structure for table `EventsData`
--

DROP TABLE IF EXISTS `EventsData`;
CREATE TABLE IF NOT EXISTS `EventsData` (
  `ED_ID` int(11) NOT NULL auto_increment,
  `ED_CategoryID` int(11) default NULL,
  `ED_ImageSrc` varchar(255) default NULL,
  `ED_Allstar` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`ED_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EventsDateRange`
--

DROP TABLE IF EXISTS `EventsDateRange`;
CREATE TABLE IF NOT EXISTS `EventsDateRange` (
  `EDR_ID` int(11) NOT NULL auto_increment,
  `EDR_EventsDataID` int(11) NOT NULL,
  `EDR_StartDate` date NOT NULL,
  `EDR_EndDate` date NOT NULL,
  PRIMARY KEY  (`EDR_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EventsIndex`
--

DROP TABLE IF EXISTS `EventsIndex`;
CREATE TABLE IF NOT EXISTS `EventsIndex` (
  `EI_EventsDataID` int(11) NOT NULL,
  `EI_LanguageID` int(11) NOT NULL,
  `EI_Title` varchar(255) default NULL,
  `EI_Location` TEXT NULL DEFAULT NULL ,
  `EI_Text` longtext,
  `EI_ImageAlt` varchar(255) default NULL,
  `EI_Status` tinyint(4) NOT NULL,
  `EI_ValUrl` varchar(255) NOT NULL,
  `EI_BriefText` VARCHAR(255) NOT NULL ,
  PRIMARY KEY  (`EI_EventsDataID`,`EI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

INSERT INTO `Modules` (`M_ID`, `M_Title`, `M_MVCModuleTitle`, `M_Indexation`) VALUES
(7, 'Events', 'events', 'EventsIndex');

INSERT INTO Modules_ControllersActionsPermissions (MCAP_ModuleID, MCAP_ControllerTitle, MCAP_ControllerActionTitle, MCAP_PermissionTitle, MCAP_Position) VALUES
(7, 'index', 'list-all', 'edit', 1),
(7, 'index', 'list-categories', 'edit', 2);

INSERT INTO `ModuleViews` (`MV_ID`, `MV_Name`, `MV_ModuleID`) VALUES
(7001, 'details_sidelist', 7),
(7002, 'details', 7),
(7003, 'homepagelist', 7),
(7004, 'listall', 7);

INSERT INTO `ModuleViewsIndex` (`MVI_ModuleViewsID`, `MVI_LanguageID`, `MVI_ActionName`) VALUES
(7001, 1, 'autres-evenements'),
(7001, 2, 'other-events'),
(7002, 1, 'detail'),
(7002, 2, 'details'),
(7003, 1, 'evenements-accueil'),
(7003, 2, 'events-home'),
(7004, 1, 'tout'),
(7004, 2, 'list-all');

INSERT INTO Extranet_Resources (ER_ID, ER_ControlName) VALUES (7, 'events');

INSERT INTO Extranet_ResourcesIndex (ERI_ResourceID, ERI_LanguageID, ERI_Name) VALUES
(7, 1, 'Evènement'),
(7, 2, 'Events');

INSERT INTO Extranet_RolesResources (ERR_ID, ERR_RoleID, ERR_ResourceID, ERR_InheritedParentID) VALUES
(7001,1, 7, 0);

INSERT INTO Extranet_RolesResourcesIndex (ERRI_RoleResourceID, ERRI_LanguageID, ERRI_Name, ERRI_Description) VALUES
(7001,1, "Gestionnaire d'évènements", ''),
(7001,2, 'Events manager', '');

INSERT INTO Extranet_RolesResourcesPermissions (ERRP_RoleResourceID, ERRP_PermissionID) VALUES
(7001, 1);


INSERT INTO `ModuleCategoryViewPage` (`MCVP_ID`, `MCVP_ModuleID`, `MCVP_CategoryID`, `MCVP_ViewID`, `MCVP_PageID`) VALUES
(7001, 7, 3, 7001, 7001),
(7002, 7, 3, 7002, 7002),
(7003, 7, 3, 7003, 1),
(7004, 7, 3, 7004, 7001);

INSERT INTO Categories (C_ID, C_ParentID, C_ModuleID, C_PageID,	C_ShowInRss,C_RssItemsCount) VALUES
(7001,0,7,null,1,10);

INSERT INTO `CategoriesIndex` (`CI_CategoryID`, `CI_LanguageID`, `CI_Title`, `CI_WordingShowAllRecords`) VALUES
(7001, 1, 'Générale', 'Liste générale des événements.'),
(7001, 2, 'General', 'List of the general events');

INSERT INTO `Pages` (`P_ID`, `P_Position`, `P_ParentID`, `P_Home`, `P_LayoutID`, `P_ThemeID`, `P_ViewID`, `P_ShowSiteMap`, `P_ShowMenu`, `P_ShowTitle`, `P_Indexation`) VALUES
(7001, 3, 0, 0, 2, 1, 2, 1, 1, 1,1),
(7002, 1, 7001, 0, 2, 1, 2, 1, 1, 1,0);

INSERT INTO PagesIndex (PI_PageID, PI_LanguageID, PI_PageIndex, PI_PageIndexOtherLink, PI_PageTitle, PI_TitleImageSrc, PI_TitleImageAlt, PI_MetaDescription, PI_MetaKeywords,`PI_MetaOther`, PI_Status, PI_Secure) VALUES
(7001, 1, 'evenements', '', 'Événements', '', '', '', '', '', 1, 'non'),
(7001, 2, 'events_en', '', 'Events', '', '', '', '', '', 1, 'non'),
(7002, 1, 'evenements-en-details', '', 'Événements en détails', '', '', '', '', '', 1, 'non'),
(7002, 2, 'events-details', '', 'Events details', '', '', '', '', '', 1, 'non');

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID) VALUES
('events_module_name', 1, 'Événements', 'cible', '', 0, 7),
('events_module_name', 2, 'Events', 'cible', '', 0, 7),
('module_events', 1, 'Évènements', 'cible', '', 0, 7),
('module_events', 2, 'Events', 'cible', '', 0, 7),
('header_list_events_text', 1, 'Liste des événements de la catégorie <em>«%CATEGORY_EVENTS_NAME%»</em>', 'cible', '', 0, 7),
('header_list_events_text', 2, 'Events list of the category <em>«%CATEGORY_EVENTS_NAME%»</em>', 'cible', '', 0, 7),
('header_list_events_description', 1, 'Cette page vous permet de consulter la liste des événements de la catégorie <em><strong>«%CATEGORY_EVENTS_NAME%»</em></strong>.', 'cible', '', 0, 7),
('header_list_events_description', 2, 'This page is to consult the events list of the category <em><strong>«%CATEGORY_EVENTS_NAME%»</em></strong>.', 'cible', '', 0, 7),
('button_add_events', 1, 'Ajouter un événement', 'cible', '', 0, 7),
('button_add_events', 2, 'Add a event', 'cible', '', 0, 7),
('header_add_events_text', 1, "Ajout d'un événement", 'cible', '', 0, 7),
('header_add_events_text', 2, 'Add an event', 'cible', '', 0, 7),
('header_add_events_description', 1, "Cette page vous permet d'ajouter un événement.", 'cible', '', 0, 7),
('header_add_events_description', 2, 'This page is to add an event.', 'cible', '', 0, 7),
('header_edit_events_text', 1, "Édition d'un événement", 'cible', '', 0, 7),
('header_edit_events_text', 2, 'Event edit', 'cible', '', 0, 7),
('header_edit_events_description', 1, "Cette page vous permet d'éditer un événement.", 'cible', '', 0, 7),
('header_edit_events_description', 2, 'This page is to edit an event.', 'cible', '', 0, 7),
('management_module_events_list', 1, 'Événements', 'cible', '', 0, 7),
('management_module_events_list', 2, 'Évents', 'cible', '', 0, 7),
('management_module_events_list_approbation_request', 1, 'Événements à approuver', 'cible', '', 0, 7),
('management_module_events_list_approbation_request', 2, 'Events to be approve', 'cible', '', 0, 7),
('management_module_events_list_categories', 1, "Catégories d'événements", 'cible', '', 0, 7),
('management_module_events_list_categories', 2, 'Events Categories', 'cible', '', 0, 7),
('events_categories_page_title', 1, "Liste des catégories d'événements", 'cible', '', 0, 7),
('events_categories_page_title', 2, 'Events categories list', 'cible', '', 0, 7),
('events_categories_page_description', 1, "Cliquez sur <b>Ajouter une catégorie d'événements</b><br>pour créer une catégorie.<br><br>Vous pouvez <b>rechercher par mots-clés</b> parmi<br>la liste des catégories. Pour revenir à la liste complète,<br>cliquez sur <b>Voir la liste complète</b>.<br><br>Vous pouvez <b>modifier ou supprimer une<br>catégorie</b> en cliquant sur l'icône <img src='/extranet/icons/icon-add-24x24.png' align=middle>.", 'cible', '', 0, 7),
('events_categories_page_description', 2, 'This page is to consult the events categories list.', 'cible', '', 0, 7),
('events_button_add_category', 1, "Ajouter une catégorie d'événements", 'cible', '', 0, 7),
('events_button_add_category', 2, 'Add a category', 'cible', '', 0, 7),
('management_module_events_list_all', 1, 'Liste des événements', 'cible', '', 0, 7),
('management_module_events_list_all', 2, 'All the events', 'cible', '', 0, 7),
('header_list_events_text_default', 1, 'Liste des événements', 'cible', '', 0, 7),
('header_list_events_text_default', 2, 'List of all the events', 'cible', '', 0, 7),
('header_list_events_description_default', 1, "Cliquez sur <b>Ajouter un événement</b> pour en<br>créer un nouveau.<br><br>Vous pouvez <b>rechercher par mots-clés</b> parmi<br>la liste des événements. Pour revenir à la liste complète,<br>cliquez sur <b>Voir la liste complète</b>.<br><br>Vous pouvez <b>modifier ou supprimer un<br>événement</b> en cliquant sur l'icône <img src='/extranet/icons/icon-add-24x24.png' align=middle>.", 'cible', '', 0, 7),
('header_list_events_description_default', 2, 'This page is to manage all the events.', 'cible', '', 0, 7),
('button_add_event', 1, 'Ajouter une parution', 'cible', '', 0, 7),
('button_add_event', 2, 'Add an event', 'cible', '', 0, 7),
('form_select_option_view_events_list', 1, 'Tous les événements', 'cible', '', 0, 7),
('form_select_option_view_events_list', 2, 'All the events', 'cible', '', 0, 7),
('form_select_option_view_events_details', 1, "Détails d'un événement", 'cible', '', 0, 7),
('form_select_option_view_events_details', 2, "Events' details ", 'cible', '', 0, 7),
('form_select_option_view_events_details_sidelist', 1, 'Autres événements à venir', 'cible', '', 0, 7),
('form_select_option_view_events_details_sidelist', 2, 'Others events to come', 'cible', '', 0, 7),
('form_select_option_view_events_listall', 1, 'Tous les événements', 'cible', '', 0, 7),
('form_select_option_view_events_listall', 2, 'All the events', 'cible', '', 0, 7),
('form_select_option_view_events_homepagelist', 1, 'Accueil', 'cible', '', 0, 7),
('form_select_option_view_events_homepagelist', 2, 'Home', 'cible', '', 0, 7),
('see_all_events_text', 1, 'Tous les événements', 'client', '', 0, 7),
('see_all_events_text', 2, 'All the events', 'client', '', 0, 7),
('events_manage_block_contents', 1, 'Gestion des événements', 'cible', '', 0, 7),
('events_manage_block_contents', 2, 'Events management', 'cible', '', 0, 7),
('events_no_events', 1, "Il n'y a présentement aucun événement.", 'cible', '', 0, 7),
('events_no_events', 2, "There is currently no events.", 'cible', '', 0, 7),
('events_s_and_s', 1, ' et ', 'cible', '', 0, 7),
('events_s_and_s', 2, ' and ', 'cible', '', 0, 7),
('events_s_to_s', 1, ' au ', 'cible', '', 0, 7),
('events_s_to_s', 2, ' to ', 'cible', '', 0, 7),
('home_page_gray_menu_events', 1, 'Événements', 'client', '', 0, 7),
('home_page_gray_menu_events', 2, 'Events', 'client', '', 0, 7),
('form_select_option_view_events_calendrier', '1', 'Grand calendrier', 'cible', '' , '0',7),
('form_select_option_view_events_calendrier_petit', '1', 'Petit calendrier', 'cible', '' , '0',7),
('events_add_to_calendar', 1, 'Ajouter au calendrier', 'cible', '' , 0,7),
('events_add_to_calendar', 2, 'Add to the calendar', 'cible', '' , 0,7),
('events_calendar_days', '1', '"Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"', 'cible', '', '0', '7'),
('events_calendar_days', '2', '"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"', 'cible', '', '0', '7'),
('events_calendar_day', '1', '"Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"', 'cible', '', '0', '7'),
('events_calendar_day', '2', '"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"', 'cible', '', '0', '7'),
-- ('events_calendar_day', '1', '"Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"', 'cible', '', '0', '7'),
-- ('events_calendar_day', '2', '"Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"', 'cible', '', '0', '7'),
('events_calendar_d', '1', '"D", "L", "M", "M", "J", "V", "S"', 'cible', '', '0', '7'),
('events_calendar_d', '2', '"S", "M", "T", "W", "T", "F", "S"', 'cible', '', '0', '7'),
('events_calendar_month', '1', '"Jan", "Fév", "Mar", "Avr", "Mai", "Jui", "Jul", "Aou", "Sep", "Oct", "Nov", "Dec"', 'cible', '', '0', '7'),
('events_calendar_month', '2', '"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"', 'cible', '', '0', '7'),
('events_calendar_months', '1', '"Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"', 'cible', '', '0', '7'),
('events_calendar_months', '2', '"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"', 'cible', '', '0', '7');

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID, ST_RichText) VALUES
('form_label_allstar', 1, "en vedette", "cible", "", 0, 7, 0),
('form_label_allstar', 2, "", "cible", "", 0, 7, 0)
;

-- Pour les calendrier
INSERT INTO ModuleViews (MV_ID, MV_Name, MV_ModuleID) VALUES (NULL, 'calendrier', '7'), (NULL, 'calendrier_petit', '7');
REPLACE INTO `Static_Texts` (`ST_Identifier`,`ST_LangID`,`ST_Value`,`ST_Type`,`ST_Desc_backend`,`ST_Editable`,`ST_ModuleID`) VALUES
('show_all_events_from_all_sites',1,'Montrer tous les événements de tous les sites. ','cible','',0,7),
('show_all_events_from_all_sites',2,'Show all events from all site','cible','',0,7),
('show_only_allstar',1,'Montrer seulement les événements en vedette','cible','',0,7),
('show_only_allstar',2,'Show only allstar events','cible','',0,7),
('form_select_option_view_events_homepagelist2',1,'Accueil 2 colonnes','cible','',0,7),
('form_select_option_view_events_homepagelist2',2,'Home 2 columns','cible','',0,7),
('form_select_option_view_events_homepagelist3',1,'Accueil 3 colonnes','cible','',0,7),
('form_select_option_view_events_homepagelist3',2,'Home 3 columns','cible','',0,7),
('categoryEvents',1,"Catégorie de l'événement",'cible','',0,7),
('categoryEvents',2,'Event category','cible','',0,7),
('datesEvents',1,"Dates de l'événement",'cible','',0,7),
('datesEvents',2,NULL,'cible','',0,7),
('onlineEvents',1,'Événement en ligne','cible','',0,7),
('onlineEvents',2,NULL,'cible','',0,7),
('placeEvents',1,'Lieu *','cible','',0,7),
('placeEvents',2,NULL,'cible','',0,7),
('texteLongEvents',1,'Texte *','cible','',0,7),
('texteLongEvents',2,'Events location','cible','',0,7),
('shortLongEvents',1,'Texte court*','cible','',0,7),
('shortLongEvents',2,'Short text*','cible','',0,7),
('see_details_events_text', 1, 'En savoir plus', 'cible', '', 0, 7),
('see_details_events_text', 2, 'More', 'cible', '', 0, 7);
