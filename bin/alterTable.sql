ALTER TABLE `Pages` ADD `P_SiteType` ENUM( 's', 'm' ) NOT NULL DEFAULT 's';
ALTER TABLE `Pages` ADD `P_HomeMobile` INT( 1 ) NOT NULL AFTER `P_Home` ;
ALTER TABLE `Layouts` ADD `L_SiteType` ENUM( 's', 'm' ) NOT NULL DEFAULT 's';
ALTER TABLE `Views` ADD `V_SiteType` ENUM( 's', 'm' ) NOT NULL DEFAULT 's';
ALTER TABLE `Modules` ADD `M_SiteType` ENUM( 's', 'm' ) NOT NULL DEFAULT 's';
ALTER TABLE `Menus` ADD `M_SiteType` ENUM( 's', 'm' ) NOT NULL DEFAULT 's';

INSERT INTO `Layouts` (`L_ID` ,`L_Name` ,`L_Path` ,`L_Image` ,`L_SiteType`) VALUES (NULL , 'mainMobile', 'mainMobile.phtml', '', 'm');
INSERT INTO `Views` (`V_ID` ,`V_Name` ,`V_ZoneCount` ,`V_Path` ,`V_Image` ,`V_SiteType`) VALUES (NULL , 'Common Mobile', '2', 'template/commonMobile.phtml', 'image.png', 'm');

UPDATE `Static_Texts` SET `ST_Value` = 'Contenus par page' WHERE `Static_Texts`.`ST_Identifier` = 'treeview_contents_management_title' AND `Static_Texts`.`ST_LangID` =1;

REPLACE INTO `Static_Texts` (`ST_Identifier` ,`ST_LangID` ,`ST_Value` ,`ST_Type` ,`ST_Desc_backend` ,`ST_Editable` ,`ST_ModuleID`) VALUES
('label_homepage', 1, 'Accueil', 'cible', '', 0, 0),
('label_homepage', 2, 'Home', 'cible', '', 0, 0),
('form_select_option_zoneViews_8', 1, 'Commun', 'cible', '', 0, 0),
('form_select_option_zoneViews_8', 2, 'Common', 'cible', '', 0, 0),
('label_site_type', 1, 'Page pour un site', 'cible', '', 0, 0),
('label_site_type', 2, 'Page to display on', 'cible', '', 0, 0),
('label_site_type_s', 1, 'Site standard', 'cible', '', 0, 0),
('label_site_type_s', 2, 'Standard website', 'cible', '', 0, 0),
('label_site_type_m', 1, 'Site mobile', 'cible', '', 0, 0),
('label_site_type_m', 2, 'Mobile website', 'cible', '', 0, 0),
('dashboard_administration_website_mobile_title', '2', 'Mobile Website', 'cible', '', '0', '0'),
('form_select_option_pageLayouts_3', 1, 'Mobile', 'cible', '', 0, 0),
('form_select_option_pageLayouts_3', 2, 'Mobile', 'cible', '', 0, 0);

ALTER TABLE `Newsletter_InvalidEmails` ADD `NIE_Message` TEXT NULL;

ALTER TABLE `Languages` ADD  `L_Local` VARCHAR( 10 ) NOT NULL;
UPDATE  `Languages` SET  `L_Local` =  'fr_CA' WHERE  `Languages`.`L_ID` =1;
UPDATE  `Languages` SET  `L_Local` =  'en_CA' WHERE  `Languages`.`L_ID` =2;
UPDATE  `Languages` SET  `L_Local` =  'es_ES' WHERE  `Languages`.`L_ID` =3;
UPDATE  `Languages` SET  `L_Local` =  'it_IT' WHERE  `Languages`.`L_ID` =4;
UPDATE  `Static_Texts` SET  `ST_Value` =  'Nombre de pages qui utilisent cette image:' WHERE  `Static_Texts`.`ST_Identifier` =  'numberOfPages' AND  `Static_Texts`.`ST_LangID` =1;
UPDATE  `Static_Texts` SET  `ST_Value` =  'Number of pages using this image:' WHERE  `Static_Texts`.`ST_Identifier` =  'numberOfPages' AND  `Static_Texts`.`ST_LangID` =2;

