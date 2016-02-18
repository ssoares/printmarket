-- Version SVN: $Id: moduleBannerCreate.sql 1849 2016-01-15 17:41:04Z ssoares $
DROP TABLE IF EXISTS `BannerGroup`;
CREATE TABLE  `BannerGroup` (
  `BG_ID` INT(5) NOT NULL AUTO_INCREMENT ,
  `BG_Name` VARCHAR( 255 ) NOT NULL ,
  PRIMARY KEY (  `BG_ID` )
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `BannerImage`;
CREATE TABLE  `BannerImage` (
  `BI_ID` INT(5) NOT NULL AUTO_INCREMENT ,
  `BI_GroupID` INT(5) NOT NULL ,
  `BI_Seq` INT( 5 ) NULL ,
  PRIMARY KEY (  `BI_ID` )
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `BannerImageIndex`;
CREATE TABLE  `BannerImageIndex` (
  `BII_BannerImageID` INT(5) NOT NULL ,
  `BII_LanguageID` INT(2) NOT NULL DEFAULT  '1',
  `BII_Text` TEXT NULL ,
  `BII_Filename` VARCHAR( 255 ) NOT NULL,
  `BII_Url` VARCHAR( 255 ) NULL,
  `BII_Bubble` VARCHAR( 255 ) NOT NULL,
  PRIMARY KEY ( `BII_BannerImageID` , `BII_LanguageID` )
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `BannerFeaturedData`;
CREATE TABLE `BannerFeaturedData` (
`BF_ID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`BF_Name` VARCHAR( 50 ) NOT NULL
) ENGINE = MYISAM ;

DROP TABLE IF EXISTS `BannerFeaturedIndex`;
CREATE TABLE `BannerFeaturedIndex` (
`BFI_DataID` INT( 11 ) NOT NULL ,
`BFI_LanguageID` INT( 11 ) NOT NULL ,
PRIMARY KEY ( `BFI_DataID` , `BFI_LanguageID` )
) ENGINE = MYISAM ;

DROP TABLE IF EXISTS `BannerFeaturedImageData`;
CREATE TABLE IF NOT EXISTS `BannerFeaturedImageData` (
  `IF_ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Cet id est la position de l''image dans la bannière',
  `IF_ImgID` int(11) NOT NULL,
  `IF_DataID` int(11) NOT NULL COMMENT 'Id de la bannière parent (BF_ID)',
  `IF_Img` varchar(255) DEFAULT NULL,
  `IF_Style` varchar(255) DEFAULT NULL,
  `IF_Effect` varchar(25) DEFAULT NULL,
  `IF_ImgOver` varchar(255) NOT NULL,
  PRIMARY KEY (`IF_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=181 ;

DROP TABLE IF EXISTS `BannerFeaturedImageIndex`;
CREATE TABLE IF NOT EXISTS `BannerFeaturedImageIndex` (
  `IFI_ImgDataID` int(11) NOT NULL,
  `IFI_LanguageID` int(11) NOT NULL,
  `IFI_Label` varchar(255) DEFAULT NULL,
  `IFI_Url` varchar(255) DEFAULT NULL,
  `IFI_TextA` text,
  `IFI_TextB` text,
  `IFI_Video` int(11) NOT NULL DEFAULT '0',
  `IFI_UrlVideo` int(11) NOT NULL DEFAULT '0',
  `IFI_File` varchar(255) NOT NULL,
  PRIMARY KEY (`IFI_ImgDataID`,`IFI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `Modules` (`M_ID` ,`M_Title` ,`M_MVCModuleTitle`) VALUES
('18',  'Banner',  'banners');

INSERT INTO  `Modules_ControllersActionsPermissions` (`MCAP_ID` ,`MCAP_ModuleID` ,`MCAP_ControllerTitle` ,`MCAP_ControllerActionTitle` ,`MCAP_PermissionTitle` ,`MCAP_Position`) VALUES
(NULL ,  '18',  'index',  'list-group',  'edit',  '1'),
(NULL ,  '18',  'index',  'list-images',  'edit',  '2'),
(NULL ,  '18',  'featured',  'list',  'edit',  '3');

INSERT INTO `ModuleViews` (`MV_ID`, `MV_Name`, `MV_ModuleID`) VALUES
(18001, 'index', 18),
(18002, 'featured', 18);

INSERT INTO `ModuleViewsIndex` (`MVI_ModuleViewsID`, `MVI_LanguageID`, `MVI_ActionName`) VALUES
(18001, 1, 'index'),
(18001, 2, 'index'),
(18002, 1, 'vedette'),
(18002, 2, 'featured');

INSERT INTO Extranet_Resources (ER_ID, ER_ControlName) VALUES (18, 'banners');

INSERT INTO Extranet_ResourcesIndex (ERI_ResourceID, ERI_LanguageID, ERI_Name) VALUES
(18, 1, 'Bannières'),
(18, 2, 'Banners');

INSERT INTO Extranet_RolesResources (ERR_ID, ERR_RoleID, ERR_ResourceID, ERR_InheritedParentID) VALUES
(1801,1, 18, 0);

INSERT INTO Extranet_RolesResourcesIndex (ERRI_RoleResourceID, ERRI_LanguageID, ERRI_Name, ERRI_Description) VALUES
(1801,1, 'Gestionnaire de bannières', 'Tous les droits'),
(1801,2, 'Banners manager', 'All access');

INSERT INTO Extranet_RolesResourcesPermissions (ERRP_RoleResourceID, ERRP_PermissionID) VALUES
(1801, 1);

REPLACE INTO `Static_Texts` (`ST_Identifier` ,`ST_LangID` ,`ST_Value` ,`ST_Type` ,`ST_Desc_backend` ,`ST_Editable`, `ST_ModuleID`) VALUES
('management_module_banners_group',  '1',  'Gestion des groupes d''images',  'cible',  '', 0, 18),
('management_module_banners_group',  '2',  'Management of images'' group',  'cible',  '', 0, 18),
('banners_module_name',  '1',  'Bannière',  'cible',  '', 0, 18),
('banners_module_name',  '2',  'Banner',  'cible',  '', 0, 18),
('management_module_banners_images',  '1',  'Images',  'cible',  '', 0, 18),
('management_module_banners_images',  '2',  'Images',  'cible',  '', 0, 18),
('management_module_banners_list_group', '1', 'Catégories', 'cible', '', 0, 18),
('management_module_banners_list_group', '2', 'Categories', 'cible', '', 0, 18),
('management_module_banners_list_images',  '1',  'Images de bannières',  'cible',  '', 0, 18),
('management_module_banners_list_images',  '2',  'Images banner',  'cible',  '', 0, 18),
('header_list_list_group_text', '1', 'Liste des groupes de bannières', 'cible', '', 0, 18),
('header_list_list_group_text', '2', 'Group list banners', 'cible', '', 0, 18),
('button_add_list_group', '1', 'Ajouter un groupe de bannière', 'cible', '', 0, 18),
('button_add_list_group', '2', 'Add a banner group', 'cible', '', 0, 18),
('list_column_BG_Name', '1', 'Nom du groupe de bannière', 'cible', '', 0, 18),
('list_column_BG_Name', '2', 'Name of the banner group', 'cible', '', 0, 18),
('form_banner_name_label', '1', 'Nom du groupe', 'cible', '', 0, 18),
('form_banner_name_label', '2', 'Name of the group', 'cible', '', 0, 18),
('header_add_list_group_text', '1', 'Ajouter un groupe de bannière', 'cible', '', 0, 18),
('header_add_list_group_text', '2', 'Add a group of banner', 'cible', '', 0, 18),
('header_add_list_group_description', '1', 'Cette page permet d''ajouter un groupe de bannière. ', 'cible', '', 0, 18),
('header_add_list_group_description', '2', 'This page allows you to add a new group banner.', 'cible', '', 0, 18),
('header_edit_list_group_description', '1', 'Cette page permet de modifier un groupe de bannière.', 'cible', '', 0, 18),
('header_edit_list_group_description', '2', 'This page allows you to modify a new group banner.', 'cible', '', 0, 18),
('header_edit_list_group_text', '1', 'Modifier le groupe de bannière', 'cible', '', 0, 18),
('header_edit_list_group_text', '2', 'Modify the group of banner', 'cible', '', 0, 18),
('header_delete_list_group_text', '1', 'Supprimer un groupe de bannière', 'cible', '', 0, 18),
('header_delete_list_group_text', '2', 'Delete a group of banner', 'cible', '', 0, 18),
('header_list_list_images_text', '1', 'Liste des images', 'cible', '', 0, 18),
('header_list_list_images_text', '2', 'List of images', 'cible', '', 0, 18),
('list_column_BII_Filename', '1', 'Nom du fichier', 'cible', '', 0, 18),
('list_column_BII_Filename', '2', 'File name', 'cible', '', 0, 18),
('list_column_BII_Text', '1', 'Texte de l''image', 'cible', '', 0, 18),
('list_column_BII_Text', '2', 'Text of the image', 'cible', '', 0, 18),
('form_banner_image_label', '1', 'Texte pour l''image', 'cible', '', 0, 18),
('form_banner_image_label', '2', 'Text for the image', 'cible', '', 0, 18),
('header_list_list_group_description', 1, 'Cliquez sur Ajouter un groupe pour\r\ncréer un groupe de bannière.\r\nVous pouvez rechercher par mots-clés parmi la liste\r\ndes groupes. Pour revenir à la liste complète,\r\ncliquez sur Voir la liste complète.\r\nVous pouvez modifier ou supprimer un\r\ngroupe en cliquant sur l''icône . ', 'cible', '', 0, 18),
('header_list_list_group_description', 2, 'Click Add to group\r\ncreate a group banner.\r\nYou can search by keywords from the list\r\ngroups. To return to full list\r\nclick See the full list.\r\nYou can edit or delete a\r\ngroup by clicking the icon.', 'cible', '', 0, 18),
('header_list_list_images_description', '1', 'Cliquez sur Ajouter une image pour ajouter une nouvelle image. Vous pouvez rechercher par mots-clés parmi la liste des images. Pour revenir à la liste complète, cliquez sur Voir la liste complète. Vous pouvez modifier ou supprimer une image en cliquant sur l''icône . ', 'cible', '', 0, 18),
('header_list_list_images_description', '2', 'Click Add an image to add a new image. You can search by keywords from the list of images. To return to the full list, click View the full list. You can edit or delete an image by clicking the icon.', 'cible', '', 0, 18),
('button_add_list_images', '1', 'Ajouter une image', 'cible', '', 0, 18),
('button_add_list_images', '2', 'Add an image', 'cible', '', 0, 18),
('header_add_list_images_text', '1', 'Ajouter une image', 'cible', '', 0, 18),
('header_add_list_images_text', '2', 'Add an image', 'cible', '', 0, 18),
('header_add_list_images_description', '1', 'Cette page permet d''ajouter une image. ', 'cible', '', 0, 18),
('header_add_list_images_description', '2', 'Use this page to add an image', 'cible', '', 0, 18),
('form_banner_image_text_label', '1', 'Texte de l''image', 'cible', '', 0, 18),
('form_banner_image_text_label', '2', 'Image text', 'cible', '', 0, 18),
('form_banner_image_group', '1', 'Groupe pour cette image', 'cible', '', 0, 18),
('form_banner_image_group', '2', 'Group for this image', 'cible', '', 0, 18),
('header_edit_list_images_text', '1', 'Editon d''une image', 'cible', '', 0, 18),
('header_edit_list_images_text', '2', 'Image edit', 'cible', '', 0, 18),
('header_edit_list_images_description', '1', 'Cette page vous permet de modifier les informations concernant une image de bannière. ', 'cible', '', 0, 18),
('header_edit_list_images_description', '2', 'On this page, you can edit an image for a banner.', 'cible', '', 0, 18),
('header_delete_list_images_text', '1', 'Supprimer une image d''une bannière', 'cible', '', 0, 18),
('header_delete_list_images_text', '2', 'Delete an image from a banner', 'cible', '', 0, 18),
('form_banner_image_group_extranet', '1', 'Groupe d''image pour l''entête', 'cible', '', 0, 18),
('form_banner_image_group_extranet', '2', 'Group image for the header', 'cible', '', 0, 18),
('list_column_C_BannerGroupID', '1', 'Image de bannière', 'cible', '', 0, 18),
('list_column_C_BannerGroupID', '2', 'Banner image', 'cible', '', 0, 18),
('list_column_SC_BannerGroupID', '1', 'Image de bannière', 'cible', '', 0, 18),
('list_column_SC_BannerGroupID', '2', 'Banner image', 'cible', '', 0, 18),
('banners_manage_block_contents', '1', 'Gestion des images en bannière', 'cible', '', 0, 18),
('banners_manage_block_contents', '2', 'Management of the banner images', 'cible', '', 0, 18);

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('banners_transition_block_page', '1', 'Delais de transition (ms)', 'cible', '', 0, 18),
('banners_transition_block_page', '2', 'Transition delays (ms)', 'cible', '', 0, 18),
('Module_banners', '1', 'Bannière', 'cible', '', 0, 18),
('Module_banners', '2', 'Banner', 'cible', '', 0, 18),
('banners_navigation_block_page', '1', 'Flèche de navigation', 'cible', '', 0, 18),
('banners_navigation_block_page', '2', 'Navigation arrow', 'cible', '', 0, 18),
('banners_image_group_block_page', '1', 'Groupe pour cette bannière', 'cible', '', 0, 18),
('banners_image_group_block_page', '2', 'Group for this banner', 'cible', '', 0, 18),
('banners_autoPlay_block_page', '1', 'Auto-play', 'cible', '', 0, 18),
('banners_autoPlay_block_page', '2', 'Auto-play', 'cible', '', 0, 18),
('banners_effect_block_page', '1', 'Effet de transition', 'cible', '', 0, 18),
('banners_effect_block_page', '2', 'Transition effect', 'cible', '', 0, 18),
('banners_delais_block_page', 1, 'Delais (seconde)', 'cible', '', 0, 18),
('banners_delais_block_page', 2, 'Delays (second)', 'cible', '', 0, 18),
('management_module_banners_list', '1', 'Mise en vedette', 'cible', '', 0, 18),
('management_module_banners_list', '2', 'Emphasis', 'cible', '', 0, 18);

REPLACE INTO `Static_Texts` (`ST_Identifier` ,`ST_LangID` ,`ST_Value` ,`ST_Type` ,`ST_Desc_backend` ,`ST_Editable`, `ST_ModuleID`) VALUES
('header_list_featured_text', '1', 'Liste des bannières pour la mise en avant', 'cible', '', 0, 18),
('header_list_featured_text', '2', 'List of the banners', 'cible', '', 0, 18),
('button_add_featured', '1', 'Ajouter une bannière', 'cible', '', 0, 18),
('button_add_featured', '2', 'Add a banner', 'cible', '', 0, 18),
('list_column_BF_Name', '1', 'Nom du groupe de bannière', 'cible', '', 0, 18),
('list_column_BF_Name', '2', 'Name of the banner group', 'cible', '', 0, 18),
('header_add_featured_text', '1', 'Ajouter une bannière', 'cible', '', 0, 18),
('header_add_featured_text', '2', 'Add a banner', 'cible', '', 0, 18),
('header_add_featured_description', '1', 'Cette page permet d''ajouter une bannière pour mettre en vedette des produits ou des collections. ', 'cible', '', 0, 18),
('header_add_featured_description', '2', 'This page allows you to add a new banner.', 'cible', '', 0, 18),
('header_edit_featured_description', '1', 'Cette page permet de modifier une bannière et les images qui y sont présentées.', 'cible', '', 0, 18),
('header_edit_featured_description', '2', 'This page allows you to modify a new banner and the associated images.', 'cible', '', 0, 18),
('header_edit_featured_text', '1', 'Modifier la bannière', 'cible', '', 0, 18),
('header_edit_featured_text', '2', 'Modify the banner', 'cible', '', 0, 18),
('header_delete_featured_text', '1', 'Supprimer une bannière', 'cible', '', 0, 18),
('header_delete_featured_text', '2', 'Delete a banner', 'cible', '', 0, 18),
('list_column_BF_ID', '1', 'Id', 'cible', '', 0, 18),
('list_column_BF_ID', '2', 'Id', 'cible', '', 0, 18),
('header_list_featured_description', 1, 'Cliquez sur <strong>Ajouter un groupe</strong> pour créer une bannière. Vous pouvez rechercher par mots-clés parmi la liste de ces bannières. <br />Pour revenir à la liste complète, cliquez sur <strong>Voir la liste complète</strong>. <br />Vous pouvez modifier ou supprimer un groupe en cliquant sur l''icône . ', 'cible', '', 0, 18),
('header_list_featured_description', 2, 'Click <strong>Add a banner</strong> to create a new banner. You can search by keywords from this list. <br />To return to full list click <strong>See the full list</strong>. <br />You can edit or delete a banner by clicking the icon.', 'cible', '', 0, 18),
('form_label_label', '1', 'Libellé', 'cible', '', 0, 18),
('form_label_label', '2', 'Label', 'cible', '', 0, 18),
('form_label_url', '1', 'Lien', 'cible', '', 0, 18),
('form_label_url', '2', 'Link', 'cible', '', 0, 18),
('form_style_label', '1', 'Style', 'cible', '', 0, 18),
('form_style_label', '2', 'Style', 'cible', '', 0, 18),
('delete_featured_banners_confirmation', 1, 'Êtes-vous sûr de vouloir supprimer', 'cible', '', 0, 18),
('delete_featured_banners_confirmation', 2, 'Are you sure you want to delete', 'cible', '', 0, 18),
('delete_featured_banners_noexist', '1', 'Aucun enregistrement pour cet id.', 'cible', '', 0, 18),
('delete_featured_banners_noexist', '2', 'No data for this id.', 'cible', '', 0, 18),
('form_select_option_view_banners_featured', '1', 'Bannière mis en vedette', 'cible', '', 0, 18),
('form_select_option_view_banners_featured', '2', 'Banner for featured products', 'cible', '', 0, 18),
('form_label_text_1', '1', 'Texte 1', 'cible', '', '0', '18'),
('form_label_text_1', '2', 'Text 1', 'cible', '', '0', '18'),
('form_label_text_2', '1', 'Texte 2', 'cible', '', '0', '18'),
('form_label_text_2', '2', 'Text 2', 'cible', '', '0', '18'),
('form_label_video', '1', 'Vidéo', 'cible', '', '0', '18'),
('form_label_video', '2', 'Video', 'cible', '', '0', '18'),
('extranet_form_label_url', '1', 'Vers l''url', 'cible', '', '0', '18'),
('extranet_form_label_url', '2', 'To the url', 'cible', '', '0', '18'),
('extranet_form_label_video', '1', 'Ouvrir le video', 'cible', '', '0', '18'),
('extranet_form_label_video', '2', 'Open the video', 'cible', '', '0', '18'),
('form_banner_image_texturl_label', '1', 'Url du lien sur l''image', 'cible', '', '0', '18'),
('form_banner_image_texturl_label', '2', 'Url of the link', 'cible', '', '0', '18'),
('form_banner_image_seq_label', '1', 'Séquence', 'cible', '', '0', '18'),
('form_banner_image_seq_label', '2', 'Sequence', 'cible', '', '0', '18'),
('form_select_option_view_banners_index', '1', 'Bannière standard', 'cible', '', 0, 18),
('form_select_option_view_banners_index', '2', 'Standard banner', 'cible', '', 0, 18);

INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES
('list_column_BI_Seq', '1', 'Séquence', 'cible', '', '0', 18, CURRENT_TIMESTAMP),
('list_column_BI_Seq', '2', 'Sequence', 'cible', '', '0', 18, CURRENT_TIMESTAMP),
('form_label_file', 1, 'Lien vers un fichier', 'cible', '', 0, 18, CURRENT_TIMESTAMP),
('form_label_file', 2, 'Link to a file', 'cible', '', 0, 18, CURRENT_TIMESTAMP);
