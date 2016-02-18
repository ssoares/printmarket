-- Version SVN: $Id: moduleMessagesCreate.sql 1872 2016-02-09 19:58:47Z ssoares $

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

-- --------------------------------------------------------
CREATE  TABLE `MessagesAlertData` (
  `MA_ID` INT(11) NOT NULL AUTO_INCREMENT ,
  `MA_Online` TINYINT(1) NOT NULL DEFAULT 0 ,
  `MA_Timeout` INT(5) NULL DEFAULT 24 ,
  PRIMARY KEY (`MA_ID`) );

CREATE  TABLE `MessagesAlertIndex` (
  `MAI_MessageAlertID` INT(11) NOT NULL ,
  `MAI_LanguageID` INT(2) NOT NULL ,
  `MAI_Title` VARCHAR(255) NOT NULL ,
  `MAI_Text` TEXT NOT NULL ,
  PRIMARY KEY (`MAI_MessageAlertID`, `MAI_LanguageID`) );

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

--
-- Données pour activer module et les liens dans le back end
--
INSERT INTO `Modules` (`M_ID`, `M_Title`, `M_MVCModuleTitle`) VALUES
(21, 'Messages et alertes', 'messages');

INSERT INTO `Modules_ControllersActionsPermissions` (`MCAP_ModuleID`, `MCAP_ControllerTitle`, `MCAP_ControllerActionTitle`, `MCAP_PermissionTitle`, `MCAP_Position`) VALUES
(21, 'index', 'list', 'edit', 1);

-- Data for ModuleViews
-- NULL
-- Data for ModuleViewsIndex
-- NULL

INSERT INTO Extranet_Resources (ER_ID, ER_ControlName) VALUES (21, 'messages');

INSERT INTO Extranet_ResourcesIndex (ERI_ResourceID, ERI_LanguageID, ERI_Name) VALUES
(21, 1, 'Messages'),
(21, 2, 'Messages');

INSERT INTO Extranet_RolesResources (ERR_ID, ERR_RoleID, ERR_ResourceID, ERR_InheritedParentID) VALUES
(2103,1, 21, 0);

INSERT INTO Extranet_RolesResourcesIndex (ERRI_RoleResourceID, ERRI_LanguageID, ERRI_Name, ERRI_Description) VALUES
(2103,1, 'Gestionnaire de catalogue', ''),
(2103,2, 'Catalog manager', '');

INSERT INTO Extranet_RolesResourcesPermissions (ERRP_RoleResourceID, ERRP_PermissionID) VALUES
(2103, 1);

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable) VALUES
('messages_module_name', 1, 'Messages',  'cible', '', 0),
('messages_module_name', 2, 'Messages', 'cible', '', 0),
('management_module_messages_list', 1, 'Liste des messages',  'cible', '', 0),
('management_module_messages_list', 2, 'Messages list', 'cible', '', 0),
('Module_messages', 1, 'Messages',  'cible', '', 0),
('Module_messages', 2, 'Messages', 'cible', '', 0),
('header_list_messages_text', 1, 'Liste des messages',  'cible', '', 0),
('header_list_messages_text', 2, 'Messages list', 'cible', '', 0),
('header_list_messages_description', 1, 'Cliquez sur <b>Ajouter</b> pour <br>créer un message.<br><br>Vous pouvez <b>rechercher par mots-clés</b> parmi la liste<br>des messages. Pour revenir à la liste complète,<br>cliquez sur <b>Voir la liste complète</b>.<br><br>Vous pouvez <b>modifier ou supprimer un message</b> en cliquant sur l''icône <img src="/extranet/icons/icon-add-24x24.png" align="middle">.            ',  'cible', '', 0),
('header_list_messages_description', 2, 'Cliquez sur <b>Ajouter</b> pour <br>créer un message.<br><br>Vous pouvez <b>rechercher par mots-clés</b> parmi la liste<br>des messages. Pour revenir à la liste complète,<br>cliquez sur <b>Voir la liste complète</b>.<br><br>Vous pouvez <b>modifier ou supprimer un message</b> en cliquant sur l''icône <img src="/extranet/icons/icon-add-24x24.png" align="middle">.            ', 'cible', '', 0),
('list_column_MA_ID', 1, 'ID',  'cible', '', 0),
('list_column_MA_ID', 2, 'ID', 'cible', '', 0),
('list_column_MAI_Title', 1, 'Titre',  'cible', '', 0),
('list_column_MAI_Title', 2, 'Title', 'cible', '', 0),
('list_column_MA_Online', 1, 'En ligne',  'cible', '', 0),
('list_column_MA_Online', 2, 'Online', 'cible', '', 0),
('list_column_MA_Timeout', 1, 'Expire après',  'cible', '', 0),
('list_column_MA_Timeout', 2, 'Timeout delay', 'cible', '', 0),
('list_column_MA_Timeout', 1, 'Expire après',  'cible', '', 0),
('list_column_MA_Timeout', 2, 'Timeout delay', 'cible', '', 0),
('header_add_messages_text', 1, 'Ajouter un message',  'cible', '', 0),
('header_add_messages_text', 2, 'Add a message', 'cible', '', 0),
('header_add_messages_description', 1, 'Cette page permet d''ajouter un nouveau message qui peut être associé à une fiche produit.',  'cible', '', 0),
('header_add_messages_description', 2, 'Cette page permet d''ajouter un nouveau message qui peut être associé à une fiche produit.', 'cible', '', 0),
('form_label_MAI_Title', 1, 'Titre du message',  'cible', '', 0),
('form_label_MAI_Title', 2, 'Message title', 'cible', '', 0),
('form_label_MAI_Text', 1, 'Texte',  'cible', '', 0),
('form_label_MAI_Text', 2, 'Text', 'cible', '', 0),
('form_label_MA_Online', 1, 'Affiché en ligne',  'cible', '', 0),
('form_label_MA_Online', 2, 'Display online', 'cible', '', 0),
('header_edit_messages_text', 1, 'Modifer le message',  'cible', '', 0),
('header_edit_messages_text', 2, 'Modify the message', 'cible', '', 0),
('header_edit_messages_description', 1, 'Cette page permet de modifier un message',  'cible', '', 0),
('header_edit_messages_description', 2, 'This page is to change message data', 'cible', '', 0),
('header_delete_messages_text', 1, 'Suppression d''un message',  'cible', '', 0),
('header_delete_messages_text', 2, 'Delete a message', 'cible', '', 0),
('form_select_option_view_messages_list', 1, 'Liste des mesages',  'cible', '', 0),
('form_select_option_view_messages_list', 2, 'Messages list', 'cible', '', 0),
('form_label_MA_Timeout', 1, 'Ré-afficher le message après (H)',  'cible', '', 0),
('form_label_MA_Timeout', 2, 'Display the message again after (H)', 'cible', '', 0),
('label_online_1', 1, 'Oui',  'cible', '', 0),
('label_online_1', 2, 'Yes', 'cible', '', 0),
('label_online_0', 1, 'Non',  'cible', '', 0),
('label_online_0', 2, 'No', 'cible', '', 0),
('delete_message_noexist', 1, 'Aucun enregistrement n''a été trouvé',  'cible', '', 0),
('delete_message_noexist', 2, 'No data found', 'cible', '', 0),
('delete_messages_confirmation', 1, 'Êtes-vous sûr de vouloir supprimerle message ',  'cible', '', 0),
('delete_messages_confirmation', 2, 'Do you really want to delete the message ', 'cible', '', 0),
('messages_manage_block_contents', 1, 'Gestion des messages',  'cible', '', 0),
('messages_manage_block_contents', 2, 'Messages management', 'cible', '', 0),
('account_modified_admin_notification_message', '1', 'Modification de compte sur le site. <br /> <br />
Prénom : ##firstname## <br />
Nom : ##lastname## <br />
Courriel : ##email## <br /> <br />
Liste des champs modifiés :
##TABLE##
<br />Utiliser l''adresse  suivante pour accéder aux informations de ce compte: <br />
<a href="http://www.spapparel.com/extranet/profile/index/edit/order/lastName/order-direction/ASC/ID/##NEWID##">http://www.spapparel.com/extranet/profile/index/edit/order/lastName/order-direction/ASC/ID/##NEWID##</a><br />
(Identification requise)', 'client', '', '0'),
('account_modified_admin_notification_title', '1', 'Modification de compte sur le site.', 'client', '', '0'),
('form_block_label_select_message', '1', 'Message à afficher', 'cible', '', '0'),
('form_block_label_select_message', '2', 'Message to display', 'cible', '', '0'),
('form_block_label_popup_width', '1', 'Largeur de la fenêtre', 'cible', '', '0'),
('form_block_label_popup_width', '2', 'Popup width', 'cible', '', '0')
;