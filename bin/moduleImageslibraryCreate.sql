DROP TABLE IF EXISTS `ImageslibraryData` ;
CREATE  TABLE IF NOT EXISTS `ImageslibraryData` (
  `IL_ID` INT(11) NOT NULL AUTO_INCREMENT ,
  `IL_Filename` VARCHAR(255) NULL ,
  `IL_FilenameOver` VARCHAR( 255 ) NULL ,
  `IL_Seq` INT(11) NULL ,
  PRIMARY KEY (`IL_ID`) )
ENGINE = MyISAM;

DROP TABLE IF EXISTS `ImageslibraryIndex`;
CREATE  TABLE IF NOT EXISTS `ImageslibraryIndex` (
  `ILI_ImageFileId` INT(11) NOT NULL ,
  `ILI_LanguageID` INT(11) NULL ,
  `ILI_Description` TEXT NULL ,
  `ILI_Label1` VARCHAR( 255 ) NULL,
  `ILI_Label2` VARCHAR( 255 ) NULL,
  `ILI_Link` VARCHAR( 255 ) NULL,
  PRIMARY KEY (`ILI_ImageFileId`, `ILI_LanguageID`) )
ENGINE = MyISAM;

DROP TABLE IF EXISTS `Imageslibrary_Keywords`;
CREATE  TABLE IF NOT EXISTS `Imageslibrary_Keywords` (
  `ILK_ImageId` INT(11) NOT NULL ,
  `ILK_RefId` INT(11) NOT NULL ,
  PRIMARY KEY (`ILK_ImageId`, `ILK_RefId`) )
ENGINE = MyISAM;


REPLACE INTO Modules (M_ID, M_Title, M_MVCModuleTitle, M_Indexation) VALUES (24, "Librairie d'images", 'imageslibrary', 'ImageslibraryData');

REPLACE INTO Modules_ControllersActionsPermissions (MCAP_ModuleID, MCAP_ControllerTitle, MCAP_ControllerActionTitle, MCAP_PermissionTitle, MCAP_Position) VALUES
(24, 'index', 'images', 'edit', 1);

REPLACE INTO `ModuleViews` (`MV_ID`, `MV_Name`, `MV_ModuleID`) VALUES
(24001, 'gridlist', 24),
(24002, 'slidelist', 24),
(24003, 'details', 24);

REPLACE INTO `ModuleViewsIndex` (`MVI_ModuleViewsID`, `MVI_LanguageID`, `MVI_ActionName`) VALUES
(24001, 1, 'liste'),
(24001, 2, 'list'),
(24002, 1, 'defile'),
(24002, 2, 'slide'),
(24003, 1, 'details'),
(24003, 2, 'details');

INSERT INTO `Pages` (`P_ID`, `P_Position`, `P_ParentID`, `P_Home`, `P_LayoutID`, `P_ThemeID`, `P_ViewID`, `P_ShowSiteMap`, `P_ShowMenu`, `P_ShowTitle`, `P_Indexation`) VALUES
(24001, 3, 0, 0, 2, 1, 2, 1, 1, 1,1),
(24002, 1, 24001, 0, 2, 1, 2, 1, 1, 1,0);

REPLACE INTO `ModuleCategoryViewPage` (`MCVP_ID`, `MCVP_ModuleID`, `MCVP_CategoryID`, `MCVP_ViewID`, `MCVP_PageID`) VALUES
(10, 24, 0, 24001, 34),
(11, 24, 0, 24002, 34),
(12, 24, 0, 24003, 35);

REPLACE INTO Extranet_Resources (ER_ID, ER_ControlName) VALUES
(24, 'imageslibrary');

REPLACE INTO Extranet_ResourcesIndex (ERI_ResourceID, ERI_LanguageID, ERI_Name) VALUES
(24, 1, 'Images'),
(24, 2, 'Images');

REPLACE INTO Extranet_RolesResources (ERR_ID, ERR_RoleID, ERR_ResourceID, ERR_InheritedParentID) VALUES
(24001,1, 24, 0);

REPLACE INTO Extranet_RolesResourcesIndex (ERRI_RoleResourceID, ERRI_LanguageID, ERRI_Name, ERRI_Description) VALUES
(24001,1, 'Gestionnaire', 'A les droits et peut supprimer les images.'),
(24001,2, 'Manager', 'Has all the rigths and can delete images.');

REPLACE INTO Extranet_RolesResourcesPermissions (ERRP_RoleResourceID, ERRP_PermissionID) VALUES
(24001, 1);

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('gallery_no_gallery', 1, 'Aucune images dans cette galerie', 'cible', '', 0, 24),
('gallery_no_gallery', 2, 'No image in the gallery', 'cible', '', 0, 24),
('imageId_label', 1, "Identifiant de l'image", 'client', '', 0, 24),
('imageId_label', 2, 'Image ID', 'client', '', 0, 24);

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`, `ST_RichText`) VALUES
('header_list_images_text', '1', 'Librairie des images', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('header_list_images_text', '2', 'Images library', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1');

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`, `ST_RichText`) VALUES
('button_add_images', '1', 'Ajouter une image', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('button_add_images', '2', 'Add an image', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('select_all', '1', 'Tout selectionner', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('select_all', '2', 'Select all', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('header_edit_images_text', '1', 'Librairie des images', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('header_edit_images_text', '2', 'Images library', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('header_edit_images_description', '1', "Cette page vous permet d'éditer les images dans la librairie.", 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('header_edit_images_description', '2', 'This page allows the edition of images in the library', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_label_IFI_Description_fr', '1', "Description française de l'image", 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_label_IFI_Description_fr', '2', 'French description of the image', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_label_IFI_Description_en', '1', "Description anglaise de l'image", 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_label_IFI_Description_en', '2', 'English description of the image', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_label_IF_Seq', '1', "Séquence de l'image", 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_label_IF_Seq', '2', 'Sequence of the image', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_label_listKeywords', '1', 'Album', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_label_listKeywords', '2', 'Album', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_label_modify_keywordsList', '1', 'Modifier', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_label_modify_keywordsList', '2', 'Edit', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_enum_keywords', '1', 'Liste des valuer :', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_enum_keywords', '2', 'List of value for :', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_enum_album', '1', 'album', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_enum_album', '2', 'album', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('edit_select_all', '1', 'Éditer la liste sélectionnée', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('edit_select_all', '2', 'Edit the selected list', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('button_edit_list', '1', 'Selectionner', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('button_edit_list', '2', 'Select', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('header_delete_images_text', '1', 'Supprimer une image', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('header_delete_images_text', '2', 'Delete an image', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_select_option_view_imageslibrary_gridlist', '1', 'Liste en grille', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_select_option_view_imageslibrary_gridlist', '2', 'Grid list', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_select_option_view_imageslibrary_slidelist', '1', 'Liste horizontale', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_select_option_view_imageslibrary_slidelist', '2', 'Slide list', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_select_option_view_imageslibrary_details', '1', 'Détails', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('form_select_option_view_imageslibrary_details', '2', 'Details', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('Module_imageslibrary', '1', "Librairie d'images", 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('Module_imageslibrary', '2', 'Image library', 'cible', '', '0', '24', CURRENT_TIMESTAMP, '1'),
('imageslibrary_manage_block_contents', '1', 'Gestion des images', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('imageslibrary_manage_block_contents', '2', 'Image management', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1');

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID, ST_RichText) VALUES
('header_list_images_description', 1, "Cette page permet de gérer les images soit au détail soit par groupe. <br /> L'ajout des images permet d'en charger plusieurs à la fois. <br /> En sélectionnant une ou plusieurs images, il est possible d'éditer des groupes d'images. <br /> La suppression se fait image par image afin d'éviter une suppression non désirée d'un groupe d'images.", "cible", "", 0, 24, 0),	('header_list_images_description', 2, "", "cible", "", 0, 24, 0),
('imageslibrary_module_name', 1, "Bibliothèque d'images", "cible", "", 0, 24, 0),
('imageslibrary_module_name', 2, "", "cible", "", 0, 24, 0),
('management_module_imageslibrary_images', 1, "Images", "cible", "", 0, 24, 0),
('management_module_imageslibrary_images', 2, "", "cible", "", 0, 24, 0),
('gallery_no_gallery', 1, "Aucune image n'est associée à cette section.", "cible", "", 0, 24, 0),
('gallery_no_gallery', 2, "No pics related to this section.", "cible", "", 0, 24, 0),
('banners_prettyphoto_block_page', 1, "Ouvrir le contenu dans une fenêtre", "cible", "", 0, 24, 0),
('banners_prettyphoto_block_page', 2, "Pop-up a window in the page", "cible", "", 0, 24, 0),
('alert_list_empty_title', 1, "Aucune image selectionnée", "cible", "", 0, 24, 0),
('alert_list_empty_title', 2, "No image selected", "cible", "", 0, 24, 0),
('alert_list_empty_text', 1, "Selectionnez au moins une image S.V.P.", "cible", "", 0, 24, 0),
('alert_list_empty_text', 2, "Please, select at least one image.", "cible", "", 0, 24, 0),
('fo_gallery_information_message_noGallery', 1, "Il n'existe aucune galerie.", 'cible', '', 0, 9, 0),
('fo_gallery_information_message_noGallery', 2, 'There is no gallery.', 'cible', '', 0, 9, 0)
;

INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`, `ST_RichText`) VALUES
('ImageLibrairyLink_fr', '1', 'Lien français', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('ImageLibrairyLink_fr', '2', 'French link', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('ImageLibrairyLink_en', '1', 'Lien anglais', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('ImageLibrairyLink_en', '2', 'English link', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('ImageLibrairyLabel1_fr', '1', 'Libellé 1 en français', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('ImageLibrairyLabel1_en', '1', 'Libellé 1 en anglais', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('ImageLibrairyLabel1_fr', '2', 'French label 1', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('ImageLibrairyLabel1_en', '2', 'English label 1', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('ImageLibrairyLabel2_fr', '1', 'Libellé 2 en français', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('ImageLibrairyLabel2_en', '1', 'Libellé 2 en anglais', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('ImageLibrairyLabel2_fr', '2', 'French label 2', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('ImageLibrairyLabel2_en', '2', 'English label 2', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('form_label_IL_Video', '1', 'Lien vidéo', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('form_label_IL_Video', '2', 'Video Link', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('back_to_image_list', '1', 'Retourner à la liste d\'images', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1'),
('back_to_image_list', '2', 'Back to image list', 'cible', '', '0', 24, CURRENT_TIMESTAMP, '1');
