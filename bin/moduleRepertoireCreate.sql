SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Structure de la table `RepertoireData`
--

CREATE TABLE IF NOT EXISTS `RepertoireData` (
  `RD_ID` int(11) NOT NULL auto_increment,
  `RD_RepDist` int(11) NOT NULL COMMENT 'elem:radio|src:repDist',
  `RD_Region` int(11) NOT NULL COMMENT 'elem:select|src:regionGrp|shortCut:true|class:hasShortcut',
  `RD_AddressId` int(11) NULL COMMENT 'elem:hidden',
  PRIMARY KEY  (`RD_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Structure de la table `RepertoireIndex`
--

CREATE TABLE IF NOT EXISTS `RepertoireIndex` (
  `RI_RepertoireDataID` int(11) NOT NULL,
  `RI_LanguageID` int(11) NOT NULL,
  `RI_Name` varchar(255) default NULL comment 'seq:1',
--   `RI_Brief` text COMMENT 'class:smallTextarea|maxLength:200' ,
--   `RI_Text` longtext COMMENT 'elem:tiny',
  `RI_ValUrl` varchar(255) default NULL COMMENT 'elem:hidden',
  PRIMARY KEY  (`RI_RepertoireDataID`,`RI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RegionData` (
  `RG_ID` int(11) NOT NULL AUTO_INCREMENT,
  `RG_GroupeID` int(11) NOT NULL COMMENT 'elem:select|src:groupes',
  `RG_Seq` int(11) DEFAULT '100' COMMENT '',
  PRIMARY KEY (`RG_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RegionIndex` (
  `RGI_RegionID` int(11) NOT NULL,
  `RGI_LanguageID` int(2) NOT NULL,
  `RGI_Name` varchar(255) NOT NULL COMMENT 'seq:1',
  `RGI_ValUrl` varchar(255) DEFAULT NULL COMMENT 'exclude:true',
  PRIMARY KEY (`RGI_RegionID`,`RGI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RegionGroupeIndex` (
  `GI_GroupeID` int(11) NOT NULL,
  `GI_LanguageID` int(2) NOT NULL DEFAULT '1',
  `GI_Name` varchar(255) NOT NULL COMMENT 'seq:1',
  `GI_ValUrl` varchar(255) DEFAULT NULL COMMENT 'exclude:true',
  `GI_Description` text NOT NULL COMMENT 'elem:tiny|class:texte_exergue',
  PRIMARY KEY (`GI_GroupeID`,`GI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RegionGroupeData` (
  `G_ID` int(11) NOT NULL AUTO_INCREMENT,
  `G_Seq` int(11) DEFAULT '100' COMMENT '',
  PRIMARY KEY (`G_ID`),
  KEY `G_CI` (`G_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
--
-- Données pour activer module et les liens dans le back end
--

INSERT INTO Modules (M_ID, M_Title, M_MVCModuleTitle) VALUES (20, 'Répertoire', 'repertoire');

INSERT INTO Modules_ControllersActionsPermissions (MCAP_ModuleID, MCAP_ControllerTitle, MCAP_ControllerActionTitle, MCAP_PermissionTitle, MCAP_Position) VALUES
(20, 'index', 'groupe', 'edit', 1),
(20, 'index', 'region', 'edit', 2),
(20, 'index', 'repertoire', 'edit', 3);

INSERT INTO `ModuleViews` (`MV_ID`, `MV_Name`, `MV_ModuleID`) VALUES
(20002, 'listall', 20);

INSERT INTO `ModuleViewsIndex` (`MVI_ModuleViewsID`, `MVI_LanguageID`, `MVI_ActionName`) VALUES
(20002, 1, 'toutes'),
(20002, 2, 'list-all');

INSERT INTO Extranet_Resources (ER_ID, ER_ControlName) VALUES
(20, 'repertoire');

INSERT INTO Extranet_ResourcesIndex (ERI_ResourceID, ERI_LanguageID, ERI_Name) VALUES
(20, 1, 'Repertoire'),
(20, 2, 'Repertoire');

INSERT INTO Extranet_RolesResources (ERR_ID, ERR_RoleID, ERR_ResourceID, ERR_InheritedParentID) VALUES
(20001,1, 20, 0);

INSERT INTO Extranet_RolesResourcesIndex (ERRI_RoleResourceID, ERRI_LanguageID, ERRI_Name, ERRI_Description) VALUES
(20001,1, 'Gestionnaire', 'A tous les droits pour gérer les répertoires.'),
(20001,2, 'Manager', 'Has the rigths to manage repertoires.');

INSERT INTO Extranet_RolesResourcesPermissions (ERRP_RoleResourceID, ERRP_PermissionID) VALUES
(20001, 1);

INSERT INTO `Pages` (`P_ID`, `P_Position`, `P_ParentID`, `P_Home`, `P_LayoutID`, `P_ThemeID`, `P_ViewID`, `P_ShowSiteMap`, `P_ShowMenu`, `P_ShowTitle`) VALUES
(20001, 3, 0, 0, 2, 1, 2, 1, 1, 1),
(20002, 1, 20001, 0, 2, 1, 2, 1, 1, 1);

INSERT INTO PagesIndex (PI_PageID, PI_LanguageID, PI_PageIndex, PI_PageIndexOtherLink, PI_PageTitle, PI_TitleImageSrc, PI_TitleImageAlt, PI_MetaDescription, PI_MetaKeywords,`PI_MetaOther`, PI_Status, PI_Secure) VALUES
(20001, 1, 'repertoire', '', 'Répertoire', '', '', '', '', '', 1, 'non'),
(20001, 2, 'repertoire-en', '', 'Repertoire', '', '', '', '', '', 1, 'non');


INSERT INTO `ModuleCategoryViewPage` (`MCVP_ID`, `MCVP_ModuleID`, `MCVP_CategoryID`, `MCVP_ViewID`, `MCVP_PageID`) VALUES
(20001, 20, 0, 20001, 20001);

INSERT INTO `Views` (`V_Name`, `V_ZoneCount`, `V_Path`, `V_Image`) VALUES
('Repertoire', 1, 'template/repertoire.phtml', 'image.png');

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('module_repertoire', 1, 'Répertoire', 'cible', '', 0 , 20),
('module_repertoire', 2, 'Repertoire', 'cible', '', 0 , 20),
('repertoire_module_name', 1, 'Répertoire', 'cible', '', 0 , 20),
('repertoire_module_name', 2, 'Repertoire', 'cible', '', 0 , 20),
('management_module_repertoire_repertoire', 1, 'Répertoire', 'cible', '', 0, 20),
('management_module_repertoire_repertoire', 2, 'Repertoire', 'cible', '', 0 , 20),
('management_module_repertoire_list_approbation_request', 1, 'Entreprises à approuver', 'cible', '', 0 , 20),
('management_module_repertoire_list_approbation_request', 2, 'Businesses to be approved', 'cible', '', 0 , 20),
('management_module_repertoire_list_categories', 1, 'Catégories de repertoire', 'cible', '', 0 , 20),
('management_module_repertoire_list_categories', 2, 'Categories list', 'cible', '', 0 , 20);

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('button_add_repertoire', 1, 'Ajouter une entreprise', 'cible', '', 0 , 20),
('button_add_repertoire', 2, 'Add a businesses', 'cible', '', 0 , 20),
('repertoire_button_add_category', 1, 'Ajouter une catégorie de répertoire', 'cible', '', 0 , 20),
('repertoire_button_add_category', 2, 'Add a category', 'cible', '', 0 , 20),
('header_list_repertoire_text', 1, 'Liste des entreprises', 'cible', '', 0 , 20),
('header_list_repertoire_text', 2, 'Businesses list', 'cible', '', 0 , 20),
('header_list_repertoire_description', 1, 'Cette page vous permet de consulter la liste des entreprises.', 'cible', '', 0 , 20),
('header_list_repertoire_description', 2, 'This page is to consult the businesses list.', 'cible', '', 0 , 20),
('list_column_RD_ID', 1, 'Identifiant',  'cible', '', 0, 20),	('list_column_RD_ID', 2, 'ID', 'cible', '', 0, 20),
('list_column_RI_Name', 1, 'Nom',  'cible', '', 0, 20),	('list_column_RI_Name', 2, 'Name', 'cible', '', 0, 20),
('header_edit_repertoire_text', 1, 'Édition d\'une entreprise', 'cible', '', 0 , 20),
('header_edit_repertoire_text', 2, 'Business edit', 'cible', '', 0 , 20),
('header_edit_repertoire_description', 1, 'Cette page vous permet d\'éditer une entreprise.', 'cible', '', 0 , 20),
('header_edit_repertoire_description', 2, 'This page is to edit a businesses.', 'cible', '', 0 , 20),
('header_add_repertoire_text', 1, 'Ajout d\'une entreprise', 'cible', '', 0 , 20),
('header_add_repertoire_text', 2, 'Add a business', 'cible', '', 0 , 20),
('header_add_repertoire_description', 1, 'Cette page vous permet d\'ajouter une entreprise.', 'cible', '', 0 , 20),
('header_add_repertoire_description', 2, 'This page is to add a business.', 'cible', '', 0 , 20),
('form_label_RI_Name', 1, 'Nom de l''entreprise',  'cible', '', 0, 20),	('form_label_RI_Name', 2, 'Company name', 'cible', '', 0, 20),
('form_label_RD_RepDist', 1, 'Représentant ou distributeur',  'cible', '', 0, 20),	('form_label_RD_RepDist', 2, 'Sales representative or distributor', 'cible', '', 0, 20),
('form_label_isRep', 1, 'Représentant',  'cible', '', 0, 20),	('form_label_isRep', 2, 'sales representative', 'cible', '', 0, 20),
('form_label_isDistributor', 1, 'Distributeur',  'cible', '', 0, 20),	('form_label_isDistributor', 2, 'Distributor', 'cible', '', 0, 20),
('form_label_RD_Region', 1, 'Région',  'cible', '', 0, 20),	('form_label_RD_Region', 2, 'Area', 'cible', '', 0, 20),
('form_label_RI_Brief', 1, 'Texte bref d''introduction',  'cible', '', 0, 20),	('form_label_RI_Brief', 2, 'Short text ', 'cible', '', 0, 20),
('form_label_RI_Text', 1, 'Description',  'cible', '', 0, 20),	('form_label_RI_Text', 2, 'Description', 'cible', '', 0, 20),
-- ('header_list_repertoire_approbation_description', 1, 'Aide pour l\'approbation des entreprises', 'cible', '', 0 , 20),
-- ('header_list_repertoire_approbation_description', 2, 'Help on businesses approbation', 'cible', '', 0 , 20),
-- ('header_list_repertoire_approbation_title', 1, 'Approbation d''entreprise', 'cible', '', 0 , 20),
-- ('header_list_repertoire_approbation_title', 2, 'Business approbation', 'cible', '', 0 , 20),
('repertoire_no_repertoire', 1, 'Il n''y a présentement aucune entreprise.\r\n', 'cible', '', 0, 20),
('repertoire_no_repertoire', 2, 'There is currently no business.\r\n', 'cible', '', 0, 20);

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('form_select_option_view_repertoire_homepagelist', 1, 'Accueil', 'cible', '', 0 , 20),
('form_select_option_view_repertoire_listall', 1, 'Toutes les entreprises', 'cible', '', 0 , 20),
-- ('form_label_reperoire_category', '1', 'Catégorie', 'cible', '', '0', 20),
-- ('form_label_reperoire_category', '2', 'Category', 'cible', '', '0', 20),
-- ('label_category_repertoire_bloc', '1', 'Catégorie des répertoire de ce bloc', 'cible', '', 0 , 20),
-- ('label_category_repertoire_bloc', '2', 'Repertoire category', 'cible', '', 0 , 20),
('repertoire_categories_page_title', 1, 'Catégories de répertoire', 'cible', '', 0 , 20),
('repertoire_categories_page_description', 1, 'Cliquez sur <b>Ajouter une catégorie de répertoires</b><br>pour créer une catégorie.<br><br>Vous pouvez <b>rechercher par mots-clés</b> parmi<br>la liste des catégories. Pour revenir à la liste complète,<br>cliquez sur <b>Voir la liste complète</b>.<br><br>Vous pouvez <b>modifier ou supprimer une<br>catégorie</b> en cliquant sur l\'icône <img src="/extranet/icons/icon-add-24x24.png" align=middle>.', 'cible', '', 0 , 20),
('repertoire_categories_page_title', 2, 'Repertoire categories list', 'cible', '', 0 , 20),
('repertoire_categories_page_description', 2, 'This page is to consult the repertoire categories list.', 'cible', '', 0 , 20),
('label_number_repertoire_show', '1', 'Nombre d''entreprises à afficher', 'cible', '', 0 , 20),
('label_number_repertoire_show', '2', 'Number of businesses to show', 'cible', '', 0 , 20),
('form_block_label_orderby_name', '1', 'Liste classée par ordre alphabétique', 'cible', '', 0 , 20),
('form_block_label_orderby_name', '2', 'List in alphabetical order', 'cible', '', 0 , 20),
('form_block_label_orderby_region', '1', 'Liste classée par région', 'cible', '', 0 , 20),
('form_block_label_orderby_region', '2', 'List ordered by area', 'cible', '', 0 , 20),
('form_select_option_view_repertoire_list', 1, 'Toutes les entreprises', 'cible', '', 0 , 20),
('form_select_option_view_repertoire_details', 1, 'Détails d\'une entreprise', 'cible', '', 0 , 20);

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('form_label_RD_RegionGroupeId', 1, 'Groupe de régions',  'cible', '', 0, 20),
('form_label_RD_RegionGroupeId', 2, '', 'cible', '', 0, 20),
('see_all_repertoire_text', 1, 'Toutes les entreprises', 'client', '', 0 , 20),
('see_all_repertoire_text', 2, 'All the businesses', 'client', '', 0 , 20),
('see_details_repertoire_text', 1, 'Plus de détails', 'client', '', 0 , 20),
('see_details_repertoire_text', 2, 'More details', 'client', '', 0 , 20),
('repertoire_manage_block_contents', 1, 'Gestion du répertoire', 'cible', '', 0 , 20),
('repertoire_manage_block_contents', 2, 'Repertoire management', 'cible', '', 0 , 20),
('repertoire_latest_repertoire_text', 1, 'Répertoire', 'client', '', 0 , 20),
('repertoire_latest_repertoire_text', 2, 'Repertoire', 'client', '', 0 , 20);
Replace INTO `Static_Texts` (`ST_Identifier`,`ST_LangID`,`ST_Value`,`ST_Type`,`ST_Desc_backend`,`ST_Editable`,`ST_ModuleID`,`ST_ModifDate`,`ST_RichText`) VALUES
('management_module_repertoire_groupe',1,'Groupe de régions','cible','',0,20,'2013-01-04 16:08:37',1),
('management_module_repertoire_groupe',2,'Districts groups','cible','',0,20,'2013-01-04 16:08:37',1),
('management_module_repertoire_region',1,'Régions','cible','',0,20,'2013-01-04 16:08:37',1),
('management_module_repertoire_region',2,'Districts','cible','',0,20,'2013-01-04 16:08:37',1),
('management_module_repertoire_repertoire',1,'Répertoire','cible','',0,20,'2012-12-31 14:23:45',1),
('management_module_repertoire_repertoire',2,'Repertoire','cible','',0,20,'2012-12-31 14:23:45',1),
('header_list_groupe_text', 1, 'Liste des groupes de régions', 'cible', '', 0, 0, '2013-06-11 21:07:47', 1),
('header_list_groupe_text', 2, 'List of group of region', 'cible', '', 0, 0, '2013-06-11 21:07:47', 1),
('header_list_groupe_description', 1, 'Cette page permet de gérer la liste des groupes de région. ', 'cible', '', 0, 0, '2013-06-11 21:08:17', 1),
('header_list_groupe_description', 2, 'Cette page permet de gérer la liste des groupes de région. ', 'cible', '', 0, 0, '2013-06-11 21:08:17', 1),
('list_column_GI_Name', 1, 'Groupe', 'cible', '', 0, 0, '2013-06-11 20:34:03', 1),
('list_column_GI_Name', 2, 'Group', 'cible', '', 0, 0, '2013-06-11 20:34:03', 1),
('list_column_G_ID', 1, 'Identifiant', 'cible', '', 0, 0, '2013-06-11 21:08:46', 1),
('list_column_G_ID', 2, 'Identification', 'cible', '', 0, 0, '2013-06-11 21:08:46', 1),
('header_add_groupe_description', 1, 'Cette page permet l''ajout d''un groupe de région', 'cible', '', 0, 0, '2013-06-11 21:21:14', 1),
('header_add_groupe_description', 2, 'Cette page permet l''ajout d''un groupe de région', 'cible', '', 0, 0, '2013-06-11 21:21:14', 1),
('header_add_groupe_text', 1, 'Ajouter un groupe de région', 'cible', '', 0, 0, '2013-06-11 21:10:25', 1),
('header_add_groupe_text', 2, 'Add a group of region', 'cible', '', 0, 0, '2013-06-11 21:10:25', 1),
('form_label_GI_Description', 1, 'Description', 'cible', '', 0, 0, '2013-06-11 21:22:21', 1),
('form_label_GI_Description', 2, 'Description', 'cible', '', 0, 0, '2013-06-11 21:22:21', 1),
('form_label_GI_Name', 1, 'Nom', 'cible', '', 0, 0, '2013-06-11 21:21:47', 1),
('form_label_GI_Name', 2, 'Name', 'cible', '', 0, 0, '2013-06-11 21:21:47', 1),
('header_list_region_description', 1, 'Cette page permet de gérer la liste des régions. ', 'cible', '', 0, 0, '2013-06-11 20:32:58', 1),
('header_list_region_description', 2, 'This page allows the management of region', 'cible', '', 0, 0, '2013-06-11 20:32:58', 1),
('header_list_region_text', 1, 'Liste de régions', 'cible', '', 0, 0, '2013-06-11 20:31:56', 1),
('header_list_region_text', 2, 'Region list', 'cible', '', 0, 0, '2013-06-11 20:31:56', 1),
('list_column_RGI_Name', 1, 'Nom', 'cible', '', 0, 0, '2013-06-11 20:34:23', 1),
('list_column_RGI_Name', 2, 'Name', 'cible', '', 0, 0, '2013-06-11 20:34:23', 1),
('list_column_RG_ID', 1, 'Identifiants', 'cible', '', 0, 0, '2013-06-11 20:33:32', 1),
('list_column_RG_ID', 2, 'Identification', 'cible', '', 0, 0, '2013-06-11 20:33:32', 1),
('header_add_region_description', 1, 'Cette page permet d''ajouter une région en l''associant à un groupe ', 'cible', '', 0, 0, '2013-06-11 21:05:05', 1),
('header_add_region_description', 2, 'Cette page permet d''ajouter une région en l''associant à un groupe ', 'cible', '', 0, 0, '2013-06-11 21:05:05', 1),
('header_add_region_text', 1, 'Ajouter une région', 'cible', '', 0, 0, '2013-06-11 21:04:29', 1),
('header_add_region_text', 2, 'Add a region', 'cible', '', 0, 0, '2013-06-11 21:04:29', 1),
('form_label_RGI_Name', 1, 'Nom', 'cible', '', 0, 0, '2013-06-11 21:05:46', 1),
('form_label_RGI_Name', 2, 'Name', 'cible', '', 0, 0, '2013-06-11 21:05:46', 1),
('form_label_RG_GroupeID', 1, 'Associée au groupe', 'cible', '', 0, 0, '2013-06-11 21:06:41', 1),
('form_label_RG_GroupeID', 2, 'Associated to the group', 'cible', '', 0, 0, '2013-06-11 21:06:41', 1),
('header_edit_region_description', 1, 'Cette page permet l''édition d''une région', 'cible', '', 0, 0, '2013-06-11 21:33:48', 1),
('header_edit_region_description', 2, 'Cette page permet l''édition d''une région', 'cible', '', 0, 0, '2013-06-11 21:33:48', 1),
('header_edit_region_text', 1, 'Édition d''une région', 'cible', '', 0, 0, '2013-06-11 21:33:19', 1),
('header_edit_region_text', 2, 'Edit a region', 'cible', '', 0, 0, '2013-06-11 21:33:19', 1),
('header_edit_groupe_description', 1, 'Cette page permet l''édition d''un groupe de région', 'cible', '', 0, 0, '2013-06-11 21:31:52', 1),
('header_edit_groupe_description', 2, 'Cette page permet l''édition d''un groupe de région', 'cible', '', 0, 0, '2013-06-11 21:31:52', 1),
('header_edit_groupe_text', 1, 'Edition d''un groupe de région', 'cible', '', 0, 0, '2013-06-11 21:31:09', 1),
('header_edit_groupe_text', 2, 'Edition d''un groupe de région', 'cible', '', 0, 0, '2013-06-11 21:31:09', 1),
('form_label_cityTxt', 1, 'Ville', 'cible', '', 0, 0, '2013-06-12 12:07:59', 1),
('form_label_cityTxt', 2, 'City', 'cible', '', 0, 0, '2013-06-12 12:07:59', 1);

