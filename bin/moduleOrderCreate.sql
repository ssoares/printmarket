-- phpMyAdmin SQL Dump
-- version 3.1.3
-- http://www.phpmyadmin.net
--
-- Host: 209.222.235.12:3306
-- Generation Time: Jun 14, 2010 at 02:58 PM
-- Server version: 5.0.70
-- PHP Version: 5.2.10-pl0-gentoo
-- Version SVN: $Id: moduleOrderCreate.sql 1850 2016-01-15 17:56:49Z ssoares $

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

-- phpMyAdmin SQL Dump
-- version 3.1.3
-- http://www.phpmyadmin.net
--
-- Host: 209.222.235.12:3306
-- Generation Time: Jan 18, 2011 at 01:10 PM
-- Server version: 5.0.70
-- PHP Version: 5.2.10-pl0-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Table structure for table `Orders`
--

DROP TABLE IF EXISTS `Orders`;
CREATE TABLE IF NOT EXISTS `Orders` (
  `O_ID` int(11) NOT NULL auto_increment,
  `O_OrderNumber` varchar(50) NULL,
  `O_ResponseOrderId` varchar(30) NULL,
  `O_CustomerProfileId` int(11) NOT NULL,
  `O_CreateDate` datetime NOT NULL,
  `O_ApprobDate` datetime NOT NULL,
  `O_ExportDate` datetime NOT NULL,
  `O_ExpPointBonisDate` datetime NOT NULL,
  `O_LastName` varchar(50) NOT NULL,
  `O_FirstName` varchar(50) NOT NULL,
  `O_Email` varchar(50) NOT NULL,
  `O_AccountId` varchar(50) NOT NULL,
  `O_Salutation` varchar(10) NOT NULL,
  `O_Language` varchar(15) NOT NULL,
  `O_FirstBillingTel` varchar(15) NOT NULL,
  `O_SecondBillingTel` varchar(15) NOT NULL,
  `O_FirstBillingAddr` varchar(255) NOT NULL,
  `O_SecondBillingAddr` varchar(255) NOT NULL,
  `O_BillingCity` varchar(50) NOT NULL,
  `O_BillingState` varchar(50) NOT NULL,
  `O_BillingCountry` varchar(50) NOT NULL,
  `O_ZipCode` varchar(10) NOT NULL,
  `O_FirstShippingTel` varchar(15) NOT NULL,
  `O_SecondShippingTel` varchar(15) NOT NULL,
  `O_FirstShippingAddr` varchar(255) NOT NULL,
  `O_SecondShippingAddr` varchar(255) NOT NULL,
  `O_ShippingCity` varchar(50) NOT NULL,
  `O_ShippingState` varchar(50) NOT NULL,
  `O_ShipingCountry` varchar(50) NOT NULL,
  `O_ShippingZipCode` varchar(10) NOT NULL,
  `O_Notes` text NOT NULL,
  `O_Status` enum('aucun','exportee') NOT NULL,
  `O_SubTotal` float NOT NULL,
  `O_TotTaxProv` float NOT NULL,
  `O_TotTaxFed` float NOT NULL,
  `O_RateTaxProv` float NOT NULL,
  `O_RateTaxFed` float NOT NULL,
  `O_TaxProvId` float NOT NULL,
  `O_TransFees` float NOT NULL,
  `O_TotalRabaisVol` float NOT NULL,
  `O_Total` float NOT NULL,
  `O_PaymentMode` varchar(10) NOT NULL,
  `O_Paid` tinyint(1) NOT NULL,
  `O_DatePayed` datetime NOT NULL,
  `O_BankTransactionId` varchar(30) NULL,
  `O_CardHolder` varchar(30) NULL,
  `O_CardNum` varchar(30) NULL,
  `O_CardType` varchar(30) NULL,
  `O_CardExpiryDate` datetime NOT NULL,
  `O_TotalPaid` float NOT NULL,
  `O_BonusPoint` int(11) NOT NULL,
  PRIMARY KEY  (`O_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Orders_Lines`
--

DROP TABLE IF EXISTS `Orders_Lines`;
CREATE TABLE IF NOT EXISTS `Orders_Lines` (
  `OL_ID` INT( 11 ) NOT NULL AUTO_INCREMENT,
  `OL_ProductId` int(11) NOT NULL,
  `OL_OrderId` int(11) NOT NULL,
  `OL_ItemId` int(11) NOT NULL,
  `OL_Type` enum('LigneItem','LigneTexte') NOT NULL,
  `OL_Description` text NOT NULL,
  `OL_Quantity` int(5) NOT NULL,
  `OL_ProductCode` varchar(50) NOT NULL,
  `OL_Price` float NOT NULL,
  `OL_Discount` float NOT NULL,
  `OL_FinalPrice` float NOT NULL,
  `OL_FirstTax` int(11) NOT NULL,
  `OL_SecondTax` int(11) NOT NULL,
  `OL_TotFirstTax` float NOT NULL,
  `OL_TotSecondTax` float NOT NULL,
  PRIMARY KEY ( `OL_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE MemberProfiles
  ADD `MP_BillingAddrId` INT(11) NULL ,
  ADD `MP_ShippingAddrId` INT(11) NULL ,
  ADD `MP_hasAccount` TINYINT(1) NULL ,
  ADD `MP_AccountNumber` VARCHAR(45) NULL ,
  ADD `MP_NoProvTax` TINYINT(1) NULL DEFAULT 1 ,
  ADD `MP_NoFedTax` TINYINT(1) NULL DEFAULT 1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Catalog_parameters`
--

DROP TABLE IF EXISTS `Order_Parameters`;
CREATE TABLE IF NOT EXISTS `Order_Parameters` (
  `CP_ID` INT(11) NOT NULL AUTO_INCREMENT ,
  `CP_ShippingFees` FLOAT NULL DEFAULT '9.95' ,
  `CP_ShippingFeesLimit` FLOAT NULL DEFAULT '300' ,
  `CP_MontantFraisCOD` FLOAT NULL DEFAULT '4.5' ,
  `CP_AdminOrdersEmail` VARCHAR(255) NULL DEFAULT NULL ,
  `CP_FreeItemID` INT(11) NULL DEFAULT NULL ,
  `CP_FreeMiniAmount` INT(11) NULL DEFAULT NULL ,
  `CP_BonusPointDollar` INT(11) NULL DEFAULT '2' ,
  `CP_TauxTaxeFed` FLOAT NULL DEFAULT NULL ,
  `CP_IncludeTaxes` TINYINT(1) NULL COMMENT 'elem:checkbox',
  PRIMARY KEY (`CP_ID`)
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8;

INSERT INTO `Order_Parameters` (`CP_ID`) VALUES ('1');

--
-- Données pour activer module et les liens dans le back end
--

REPLACE INTO Modules (M_ID, M_Title, M_MVCModuleTitle, M_UseProfile) VALUES (17, 'Orders', 'order', 1);

REPLACE INTO Modules_ControllersActionsPermissions (MCAP_ModuleID, MCAP_ControllerTitle, MCAP_ControllerActionTitle, MCAP_PermissionTitle, MCAP_Position) VALUES
(17, 'index', 'list-clients', 'edit_customer', 1),
(17, 'index', 'list-orders', 'edit', 2),
(17, 'parameters', 'parameters', 'edit', 3);


REPLACE INTO `ModuleViews` (`MV_ID`, `MV_Name`, `MV_ModuleID`) VALUES
(17001,'become_client', 17),
(17002,'login', 17),
(17003,'order', 17),
(17004,'order_approbation', 17),
(17005,'confirm_email', 17),
(17006,'return_confirm_email', 17),
(17007,'login_with_inscription', 17),
(17008,'modify_inscription', 17);

REPLACE INTO `ModuleViewsIndex` (`MVI_ModuleViewsID`, `MVI_LanguageID`, `MVI_ActionName`) VALUES
(17001, 1, 'become-client'),
(17001, 2, 'become-client'),
(17003, 1, 'commander'),
(17003, 2, 'order'),
(17004, 1, 'confirmation'),
(17004, 2, 'confirmation'),
(17005, 1, 'confirmation-courriel'),
(17005, 2, 'confirmation-email'),
(17006, 1, 'renvoyer-confirmation'),
(17006, 2, 'return-confirmation'),
(17007, 1, 'connection-inscription'),
(17007, 2, 'login-register'),
(17008, 1, 'become-client'),
(17008, 2, 'become-client');

REPLACE INTO Pages (P_ID, P_Position, P_ParentID, P_Home, P_LayoutID, P_ThemeID, P_ViewID, P_ShowSiteMap, P_ShowMenu, P_ShowTitle, P_BannerGroupID) VALUES
(17001, 8, 0, 0, 2, 1, 2, 1, 1, 1, NULL),
(17002, 1, 17001, 0, 2, 1, 2, 1, 1, 1, NULL),
(17003, 3, 17001, 0, 2, 1, 2, 1, 1, 1, NULL),
(17004, 4, 17001, 0, 2, 1, 2, 1, 1, 1, NULL),
(17005, 9, 0, 0, 2, 1, 2, 1, 1, 1, NULL);

REPLACE INTO PagesIndex (PI_PageID, PI_LanguageID, PI_PageIndex, PI_PageIndexOtherLink, PI_PageTitle, PI_TitleImageSrc, PI_TitleImageAlt, PI_MetaDescription, PI_MetaKeywords, PI_MetaOther, PI_Status, PI_Secure) VALUES
(17001, 1, 'se-connecter', '', 'Se connecter', '', '', '', '', '', 1, 'non'),
(17001, 2, 'log-in', '', 'Login', '', '', '', '', '', 1, 'non'),
(17002, 1, 'creer-un-nouveau-compte', '', 'Créer un nouveau compte', '', '', '', '', '', 1, 'non'),
(17002, 2, 'create-a-new-acount', '', 'Create a new acount', '', '', '', '', '', 1, 'non'),
(17003, 1, 'courriel-de-confirmation', '', 'Courriel de confirmation', '', '', '', '', '', 1, 'non'),
(17003, 2, 'email-confirmation', '', 'email confirmation', '', '', '', '', '', 1, 'non'),
(17004, 1, 'valider-le-courriel', '', 'Valider le courriel', '', '', '', '', '', 1, 'non'),
(17004, 2, 'validate-email-address', '', 'Validate email address', '', '', '', '', '', 1, 'non'),
(17005, 1, 'commande', '', 'Commande', '', '', '', '', '', 1, 'non'),
(17005, 2, 'orders', '', 'Order', '', '', '', '', '', 1, 'non');

REPLACE INTO `ModuleCategoryViewPage` (`MCVP_ID` ,`MCVP_ModuleID` ,`MCVP_CategoryID` ,`MCVP_ViewID` ,`MCVP_PageID`) VALUES
(NULL , 17, 0, 17001, 17002),
(NULL , 17, 0, 17008, 17002),
(NULL , 17, 0, 17005, 17003),
(NULL , 17, 0, 17006, 17004),
(NULL , 17, 0, 17003, 17005);

REPLACE INTO Extranet_Resources (ER_ID, ER_ControlName) VALUES (17, 'order');

REPLACE INTO Extranet_ResourcesIndex (ERI_ResourceID, ERI_LanguageID, ERI_Name) VALUES
(17, 1, 'Commandes'),
(17, 2, 'Orders');

REPLACE INTO Extranet_RolesResources (ERR_ID, ERR_RoleID, ERR_ResourceID, ERR_InheritedParentID) VALUES
(17001,1, 17, 0);

REPLACE INTO Extranet_RolesResourcesIndex (ERRI_RoleResourceID, ERRI_LanguageID, ERRI_Name, ERRI_Description) VALUES
(17001,1, "Gestionnaire", 'Peut modifier les données et exécuter un export'),
(17001,2, 'Manager', 'Can edit data and run an export');

REPLACE INTO Extranet_RolesResourcesPermissions (ERRP_RoleResourceID, ERRP_PermissionID) VALUES
(17001, 1);

REPLACE INTO `NotificationManagerData` (`NM_ID`, `NM_ModuleId`, `NM_Event`, `NM_Type`, `NM_Recipient`, `NM_Active`, `NM_Message`, `NM_Title`, `NM_Email`) VALUES
(1, 17, 'newAccount', 'email', 'client', 1, 'validate_notification_client_email_message', 'validate_notification_client_email_title', 'empty'),
(2, 17, 'newAccount', 'email', 'admin', 1, 'account_created_admin_notification_message', 'account_created_admin_notification_title', 'empty'),
(3, 17, 'editResend', 'email', 'client', 1, 'revalidate_notification_client_email_message', 'validate_notification_client_email_title', 'empty'),
(4, 17, 'editAccount', 'email', 'admin', 1, 'account_modified_admin_notification_message', 'account_modified_admin_notification_title', 'empty'),
(5, 17, 'welcome', 'email', 'client', 1, 'account_notification_client_email_message', 'account_notification_client_email_title', 'empty'),
(6, 17, 'newOrder', 'email', 'client', 1, 'order_order_notification_client_email_message', 'order_order_notification_client_email_title', 'empty'),
(7, 17, 'newOrder', 'email', 'admin', 1, 'order_notification_admin_email_message', 'order_order_notification_client_email_title', 'empty'),
(9, 17, 'rejectOrder', 'email', 'client', 1, 'order_order_notification_decline_email_message', 'order_order_notification_decline_email_title', 'empty'),
(10, 17, 'newPassword', 'email', 'client', 1, 'lost_password_notification_message', 'lost_password_notification_title', 'empty')
;

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID) VALUES
('Module_order', 1, 'Commande', 'cible', '', 0, 17),
('Module_order', 2, 'Order', 'cible', '', 0, 17),
('order_form_order_submit', 1, 'Commander maintenant', 'client', '', 0, 17),
('order_form_order_submit', 2, 'Proceed to checkout', 'client', '', 0, 17),
('order_module_name', 1, 'Commandes', 'cible', '', 0, 17),
('order_module_name', 2, 'Orders', 'cible', '', 0, 17),
('order_order_form_view_account', 1, 'Modifier votre compte', 'client', '', 0, 17),
('order_order_form_view_account', 2, 'Modify your Account', 'client', '', 0, 17),
('order_order_form_view_cart', 1, 'Voir mon panier', 'client', '', 0, 17),
('order_order_form_view_cart', 2, 'View Cart', 'client', '', 0, 17);

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID) VALUES
('order_order_notification_approbation_email_title', 1, "Notification d\'approbation de commande - # ##confirmationNumber##", 'client', 'Message de notification: Couriel d\'approbation de commande (titre)', 1, 17),
('order_order_notification_approbation_email_title', 2, "Order approbation notification - # ##confirmationNumber##", 'client', 'Notification message: Order approbation email (title)', 1, 17),
('order_order_notification_client_email_message', 1, "<p>Votre commande a bien &eacute;t&eacute; re&ccedil;ue et sera trait&eacute;e dans les prochaines 24 heures ouvrables. Voici le num&eacute;ro de confirmation de votre commande : ##confirmationNumber##.</p><p>Pour toute question ou renseignement, veuillez communiquer avec nous; il nous fera plaisir de r&eacute;pondre &agrave; vos questions.</p><p>&nbsp;</p>", 'client', 'Message de notification: nouvelle commande ', 1, 17),
('order_order_notification_client_email_message', 2, "<p>Your order has been received and will be treated within the next working day. Here is your confirmation number in order to follow your order: ##confirmationNumber##.</p><p>If you have questions regarding your order or for more information, please feel free to contact us.</p><p>&nbsp;</p>", 'client', 'Notification message: New order', 1, 17),
('order_order_notification_decline_email_message', 1, "<p>Votre requ&ecirc;te a &eacute;t&eacute; rejet&eacute;e. Un membre de notre &eacute;quipe entrera en communication avec vous sous peu afin de rem&eacute;dier &agrave; la situation. Nous vous rappelons que nous traitons chaque demande avec grand int&eacute;r&ecirc;t.</p><p>Pour toutes questions ou renseignements, veuillez communiquer avec nous.</p><p>Merci de votre compr&eacute;hension,</p>", 'client', 'Message de notification: Annulation de commande', 1, 17),
('order_order_notification_decline_email_message', 2, "<p>Votre requ&ecirc;te a &eacute;t&eacute; rejet&eacute;e. Un membre de notre &eacute;quipe entrera en communication avec vous sous peu afin de rem&eacute;dier &agrave; la situation. Nous vous rappelons que nous traitons chaque demande avec grand int&eacute;r&ecirc;t.</p><p>Pour toutes questions ou renseignements, veuillez communiquer avec nous.</p><p>Merci de votre compr&eacute;hension,</p>", 'client', 'Notification message: order cancelled', 1, 17),
('order_order_notification_decline_email_title', 1, "Annulation de commande", 'client', 'Message de notification: Annulation de commande (titre)', 1, 17),
('order_order_notification_decline_email_title', 2, "Order cancelled", 'client', 'Notification message: order cancelled (title)', 1, 17);

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID) VALUES
('management_module_order_list_clients', 1, 'Liste des clients', 'cible', '', 0, 17),
('management_module_order_list_clients', 2, 'Customers list', 'cible', '', 0, 17),
('management_module_order_list_orders', 1, 'Liste des commandes', 'cible', '', 0, 17),
('management_module_order_list_orders', 2, 'Orders list', 'cible', '', 0, 17),
('form_select_option_view_order_order_order_approbation', 1, 'Approbation de commande', 'cible', '', 0, 17),
('header_list_order_clients_text', 1, 'Liste des clients', 'cible', '', 0, 17),
('header_list_order_clients_description', 1, 'Votre liste de clients', 'cible', '', 0, 17),
('form_select_option_view_order_become_client', 1, 'Créer un compte', 'cible', '', 0, 17),
('form_select_option_view_order_login', 1, 'Authentification', 'cible', '', 0, 17),
('form_select_option_view_order_order_order', 1, 'Commander', 'cible', '', 0, 17),
('form_select_option_view_order_order_order_approbation', 2, 'Order approbation', 'cible', '', 0, 17),
('isManager_1', 1, 'Détaillants', 'cible', '', 0, 17),
('isManager_1', 2, 'Retailers', 'cible', '', 0, 17),
('isManager_0', 1, 'Non détaillants', 'cible', '', 0, 17),
('isManager_0', 2, 'Not Retailers', 'cible', '', 0, 17),
('filter_empty_quote_status', 1, 'Choisir un statut', 'cible', '', 0, 17),
('filter_empty_quote_status', 2, 'Select a status', 'cible', '', 0, 17),
('order_status_1', 1, 'En attente', 'cible', '', 0, 17),
('order_status_1', 2, 'Pending', 'cible', '', 0, 17),
('order_status_2', 1, 'Validée', 'cible', '', 0, 17),
('order_status_2', 2, 'Validated', 'cible', '', 0, 17),
('order_account_modified_message', 1, 'Votre compte a été modifié avec succès.', 'client', '', 0, 17),
('order_account_modified_message', 2, 'Your account has been updated successfully.', 'client', '', 0, 17),
('order_return_to_previous_page', 1, '« Revenir à la page précédente', 'client', '', 0, 17),
('order_return_to_previous_page', 2, '« Return to the previous page', 'client', '', 0, 17),
('form_select_option_view_order_return_confirm_email', 1, 'Renvoyer le courriel de confirmation.', 'cible', '', 1, 17),
('form_select_option_view_order_return_confirm_email', 2, 'Return the confirmation email', 'cible', '', 1, 17),
('form_label_retailer', 1, 'Êtes-vous un détaillant?', 'client', '', 1, 17),
('form_label_retailer', 2, 'Are you a retailer?', 'client', '', 1, 17),
('order_create_account_logged_title', '1', 'Se déconnecter et créer un nouveau compte SP', 'client', '', 0, 17),
('order_create_account_logged_title', '2', 'Log out and create a new account', 'client', '', 0, 17),
('quote_login_different_account_label', '1', 'Se connecter avec un compte différent', 'client', '', 0, 17),
('quote_login_different_account_label', '2', 'Log in with a different account', 'client', '', 0, 17),
('quote_request_logged_title', '1', 'Vous êtes déjà connecté en tant que', 'client', '', 0, 17),
('quote_request_logged_title', '2', 'You are logged in as', 'client', '', 0, 17),
('summary_undefine_label', '1', 'Indéfini', 'cible', '', 0, 17),
('summary_undefine_label', '2', 'Undefined', 'cible', '', 0, 17),
('form_label_choose_retailer', '1', 'Détaillant', 'cible', '', 0, 17),
('form_label_choose_retailer', '2', 'Retailer', 'cible', '', 0, 17),
('order_resume_account_data_title', '1', 'Identification', 'client', '', 0, 17),
('order_resume_account_data_title', '2', 'Identification', 'client', '', 0, 17),
('order_resume_comments_title', '1', 'Informations additionnelles/Commentaires', 'client', '', 0, 17),
('order_resume_comments_title', '2', 'Additional Information / Comments', 'client', '', 0, 17),
('order_summary_products_title', '1', 'Produits', 'client', '', 0, 17),
('order_summary_products_title', '2', 'Products', 'client', '', 0, 17),
('order_account_modification_link', '1', 'Modifier les informations de mon compte', 'client', '', 0, 17),
('order_account_modification_link', '2', 'Edit my account information', 'client', '', 0, 17),
('order_select_retailer_title', '1', 'Choisir un détaillant', 'client', '', 0, 17),
('order_select_retailer_title', '2', 'Select a retailer', 'client', '', 0, 17),
('form_select_option_view_order_become_client', 1, 'Créer un compte', 'cible', '', 0, 17),
('form_select_option_view_order_login', 1, 'Authentification', 'cible', '', 0, 17),
('form_select_option_view_order_order', 1, 'Commander', 'cible', '', 0, 17),
('form_select_option_view_order_order', 2, 'Order', 'cible', '', 0, 17),
('form_select_option_view_order_order_approbation', 1, 'Approbation de commande', 'cible', '', 0, 17),
('form_select_option_view_order_order_approbation', 2, 'Order approbation', 'cible', '', 0, 17),
('form_select_option_view_order_return_confirm_email', 1, 'Renvoyer le courriel de confirmation.', 'cible', '', 1, 17),
('form_select_option_view_order_return_confirm_email', 2, 'Return the confirmation email', 'cible', '', 1, 17),
('back_home_link_text', '1', "Retour à la page d'accueil", 'client', 'lien retour page accueil', 1, 17),
('back_home_link_text', '2', 'Back to home page', 'client', 'link to home page', 1, 17),
('list_column_company', '1', "Entreprise", 'cible', '', 0, 17),
('list_column_company', '2', 'Company', 'cible', '', 0, 17);

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID) VALUES
('email_message_de_fin', 1, "Votre commande vous sera envoyée dans les plus bref delais.
 <br />Merci d'avoir fait affaire avec ##siteName##.", 'client', '', 1, 17),
('email_message_de_fin', 2, "Your command will be send to you shortly.
 <br />Thank you for buying at ##siteName##", 'client', '', 1, 17),
('form_account_connaissance_legend', 1, 'Mieux vous connaître', 'cible', '', 0, 17),
('form_account_connaissance_legend', 2, 'To know you better', 'cible', '', 0, 17),
('form_label_payment_means', 1, 'Moyen de paiement', 'cible', '', 0, 17),
('form_label_payment_means', 2, 'Means of payment', 'cible', '', 0, 17),
('form_label_payement_visa', 1, 'Carte VISA', 'cible', '', 0, 17),
('form_label_payement_visa', 2, 'VISA', 'cible', '', 0, 17),
('form_label_payement_mastercard', 1, 'Carte MasterCard', 'cible', '', 0, 17),
('form_label_payement_mastercard', 2, 'MasterCard', 'cible', '', 0, 17),
('form_label_payement_account', 1, 'Porter au compte', 'cible', '', 0, 17),
('form_label_payement_account', 2, 'Add to account', 'cible', '', 0, 17),
('form_label_payement_cod', 1, 'Paiement à la livraison', 'cible', '', 0, 17),
('form_label_payement_cod', 2, 'Cash On Delivery', 'cible', '', 0, 17),
('form_label_next_step_btn', 1, 'Étape suivante »', 'cible', '', 0, 17),
('form_label_next_step_btn', 2, 'Next step »', 'cible', '', 0, 17),
('form_label_prev_step_btn', 1, '« Étape précédente', 'client', '', 0, 17),
('form_label_prev_step_btn', 2, '« Previous step', 'client', '', 0, 17),
('form_label_confirm_order_btn', 1, 'Envoi commande', 'client', '', 0, 17),
('form_label_confirm_order_btn', 2, 'Send order', 'client', '', 0, 17),
('form_label_confirm_payment_btn', 1, 'Paiement Moneris', 'client', '', 0, 17),
('form_label_confirm_payment_btn', 2, 'Order Payment', 'client', '', 0, 17),
('header_order_view_description', 1, 'Cette page présente un aperçu de la commande. Les informations ne sont pas modifiables.', 'cible', '', 0, 17),
('header_order_view_description', 2, 'This page is to view the order content. Data are cannot be edited.', 'cible', '', 0, 17),
('header_order_view_text', 1, 'Visualiser la commande', 'cible', '', 0, 17),
('header_order_view_text', 2, 'View order', 'cible', '', 0, 17),
('orderCompleted_0', '1', "Non", 'cible', '', 0, 17),
('orderCompleted_0', '2', 'No', 'cible', '', 0, 17),
('orderCompleted_1', '1', "Oui", 'cible', '', 0, 17),
('orderCompleted_1', '2', 'Yes', 'cible', '', 0, 17),
('list_column_O_OrderNumber', '1', "Numéro de commande", 'cible', '', 0, 17),
('list_column_O_OrderNumber', '2', 'Order number', 'cible', '', 0, 17),
('list_column_Name', '1', "Nom / Id Acomba", 'cible', '', 0, 17),
('list_column_Name', '2', 'Name / Acomba Id', 'cible', '', 0, 17),
('list_column_O_Total', '1', "Total", 'cible', '', 0, 17),
('list_column_O_Total', '2', 'Total', 'cible', '', 0, 17),
('list_column_O_PaymentMode', '1', "Mode de paiement", 'cible', '', 0, 17),
('list_column_O_PaymentMode', '2', 'Means of payment', 'cible', '', 0, 17),
('list_column_O_Paid', '1', "Payé", 'cible', '', 0, 17),
('list_column_O_Paid', '2', 'Paid', 'cible', '', 0, 17),
('menu_submenu_action_view', 1, 'Aperçu', 'cible', '', 0, 17),
('menu_submenu_action_view', 2, 'View', 'cible', '', 0, 17),
('header_list_orders_text', 1, 'Liste des commandes', 'cible', '', 0, 17),
('header_list_orders_text', 2, 'Orders list', 'cible', '', 0, 17),
('header_list_orders_description', 1, 'Cette page permet de visualiser la liste des commandes.', 'cible', '', 0, 17),
('header_list_orders_description', 2, 'This page is to access to the orders view', 'cible', '', 0, 17),
('alert_cod_fee_amount', '1', 'Une majoration de ##CODVAL## $ sera appliquée au total de la commande.', 'client', '', 0, 17),
('alert_cod_fee_amount', '2', 'A surcharge of ##CODVAL## $ will be applied to order total.', 'client', '', 0, 17),
('card_payment_error_message', '1', 'Une erreur est survenue lors de votre paiement par carte.', 'client', 'Erreur paiement carte: message de retour', 1, 17),
('card_payment_error_message', '2', 'An error occurs with the online payment.', '', 'Card payment error message', 1, 17),
('cart_cod_label', '1', 'Frais COD', 'client', 'Erreur paiement carte: message de retour', 1, 17),
('cart_cod_label', '2', 'COD fees', '', 'Card payment error message', 1, 17),
('lost_password_sent', '1', 'Un nouveau mot de passe vous a été envoyé.', 'client', '', 0, 17),
('lost_password_sent', '2', 'we sent you a new password', 'client', '', 0, 17),
('lost_password_email_not_found', '1', 'Aucun compte n''est associé à ce courriel.', 'client', '', 0, 17),
('lost_password_email_not_found', '2', 'There is no account associated to this email.', 'client', '', 0, 17),
('no_customer_account', '1', "Vous n'avez pas de numéro de compte.", 'client', '', 0, 17),
('no_customer_account', '2', "You don't have an account number.", 'client', '', 0, 17),
('email_information_shipping', 1, 'Facturation et livraison', 'client', '', 1, 17),
('email_information_shipping', 2, 'Billing and Shipping', 'client', '', 1, 17);

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID) VALUES
('cart_label_tpFees_limit', 1, "Pour toute commande d'un montant de <span><strong>##TP_FEES_LIMIT## $ avant taxes</strong></span>, les frais de transport sont gratuits.", 'client', '', 0, 17),
('cart_label_tpFees_limit', 2, 'For orders of <span><strong>$ ##TP_FEES_LIMIT## before taxes</strong></span>, freight charges are free', 'client', '', 0, 17);

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID) VALUES
('form_select_option_view_order_login_with_inscription', 1, 'Authentification avec lien inscription', 'cible', '', 0, 17),
('form_select_option_view_order_login_with_inscription', 2, 'Authentication with link to registration', 'cible', '', 0, 17),
('form_account_subform_detaillant_legend', 1, "Detaillants", 'cible', '', 0, 17),
('form_account_subform_detaillant_legend', 2, 'Retailers', 'cible', '', 0, 17),
('quoteRequest_account_introduction_text', 1, 'Inscription pour ouvrir un compte', 'client', '', 1, 17),
('quoteRequest_account_introduction_text', 2, 'Inscription to open an account', 'client', '', 1, 17),
('inscription_authentification_inscrivez_vous', 1, 'Inscrivez-vous', 'cible', '', 0, 17),
('inscription_authentification_inscrivez_vous', 2, 'Click here to register', 'cible', '', 0, 17),
('newsletter_fo_form_label_securityCaptcha', 1, 'Pour des raisons de sécurité, veuillez entrer les caractères<br> alphanumériques de l''image dans l''espace ci-dessous.', 'cible', '', 0, 17),
('newsletter_fo_form_label_securityCaptcha', 2, 'For security reasons, please enter the alphanumeric <br>characters from the image in the space below.', 'cible', '', 0, 17),
('addto_quoterequest_label', 1, "Ajouter à la demande de soumission", 'cible', '', 0, 17),
('addto_quoterequest_label', 2, 'Add to the quote request', 'cible', '', 0, 17),
('inscription_authentification_long_texte', '1', 'Votre inscription...', 'cible', '', '0', '17'),
('inscription_authentification_long_texte', '2', 'Your subscription...', 'cible', '', '0', '17'),
('profile_tab_title_order', '1', 'Commandes', 'cible', '', '0', '17'),
('profile_tab_title_order', '2', 'Orders', 'cible', '', '0', '17'),
('add_into_option_label', 1, "Dans l'option ##X##", 'client', '', 0, 17),
('add_into_option_label', 2, 'To the option ##X##', 'client', '', 0, 17),
('management_module_order_parameters',1,"Paramètres de commande",'cible','', 0, 17),
('management_module_order_parameters',2,'Order parameters','cible','', 0, 17);

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('form_parameters_montant_label', 1, "Frais de transport", 'cible', '', 0, 17),
('form_parameters_montant_label', 2, 'Transportation fees', 'cible', '', 0, 17),
('form_parameters_limit_transport_label', 1, "Limit des frais de transport", 'cible', '', 0, 17),
('form_parameters_limit_transport_label', 2, 'Limit transportation costs', 'cible', '', 0, 17),
('form_parameters_COD_label', 1, "Frais pour le COD", 'cible', '', 0, 17),
('form_parameters_COD_label', 2, 'Fees for COD', 'cible', '', 0, 17),
('form_parameters_email_label', 1, "Courriel pour les commandes", 'cible', '', 0, 17),
('form_parameters_email_label', 2, 'Email for orders', 'cible', '', 0, 17),
('form_parameters_free_item_label', 1, "Item gratuit", 'cible', '', 0, 17),
('form_parameters_free_item_label', 2, 'Free item', 'cible', '', 0, 17),
('form_parameters_free_item_minimum_label', 1, "Montant minimum pour l'item gratuit", 'cible', '', 0, 17),
('form_parameters_free_item_minimum_label', 2, 'Minimum amount for the item free', 'cible', '', 0, 17),
('form_parameters_bonus_label', 1, "Point bonis par dollar d'achat", 'cible', '', 0, 17),
('form_parameters_bonus_label', 2, 'Bonus Point per dollar', 'cible', '', 0, 17),
('form_parameters_taxe_label', 1, "Taxe fédérale", 'cible', '', 0, 17),
('form_parameters_taxe_label', 2, 'Federal tax', 'cible', '', 0, 17),
('header_list_parameters_text', 1, "Liste des paramètres", 'cible', '', 0, 17),
('header_list_parameters_text', 2, 'Parameters list', 'cible', '', 0, 17),
('header_list_parameters_description', 1, "Gestion des préférences de commande", 'cible', '', 0, 17),
('header_list_parameters_description', 2, 'Management command parameters', 'cible', '', 0, 17),
('header_edit_parameters_text', 1, "Modification", 'cible', '', 0, 17),
('header_edit_parameters_text', 2, 'Edit', 'cible', '', 0, 17),
('header_edit_parameters_description', 1, "Cette page permet de modifier les préférences de commande.", 'cible', '', 0, 17),
('header_edit_parameters_description', 2, 'This is the page to edit the command parameters.', 'cible', '', 0, 17),
('list_column_CP_ShippingFees', 1, "Frais de transport", 'cible', '', 0, 17),
('list_column_CP_ShippingFees', 2, 'Transportation fees', 'cible', '', 0, 17),
('list_column_CP_ShippingFeesLimit', 1, "Limit des frais de transport", 'cible', '', 0, 17),
('list_column_CP_ShippingFeesLimit', 2, 'Limit transportation costs', 'cible', '', 0, 17),
('list_column_CP_MontantFraisCOD', 1, "Frais pour le COD", 'cible', '', 0, 17),
('list_column_CP_MontantFraisCOD', 2, 'Fees for COD', 'cible', '', 0, 17),
('list_column_CP_AdminOrdersEmail', 1, "Courriel pour les commandes", 'cible', '', 0, 17),
('list_column_CP_AdminOrdersEmail', 2, 'Email for orders', 'cible', '', 0, 17),
('list_column_CP_FreeItemID', 1, "Item gratuit", 'cible', '', 0, 17),
('list_column_CP_FreeItemID', 2, 'Free item', 'cible', '', 0, 17),
('list_column_CP_FreeMiniAmount', 1, "Montant minimum pour l'item gratuit", 'cible', '', 0, 17),
('list_column_CP_FreeMiniAmount', 2, 'Minimum amount for the item free', 'cible', '', 0, 17),
('list_column_CP_BonusPointDollar', 1, "Point bonis par dollar d'achat", 'cible', '', 0, 17),
('list_column_CP_BonusPointDollar', 2, 'Bonus Point per dollar', 'cible', '', 0, 17),
('list_column_CP_TauxTaxeFed', 1, "Taxe fédérale", 'cible', '', 0, 17),
('list_column_CP_TauxTaxeFed', 2, 'Federal tax', 'cible', '', 0, 17),
('list_column_parameters_taxe_label', 1, "Taxe fédérale", 'cible', '', 0, 17),
('list_column_parameters_taxe_label', 2, 'Federal tax', 'cible', '', 0, 17);

