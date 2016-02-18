-- phpMyAdmin SQL Dump
-- version 3.1.3
-- http://www.phpmyadmin.net
--
-- Host: 209.222.235.12:3306
-- Generation Time: Jun 14, 2010 at 02:58 PM
-- Server version: 5.0.70
-- PHP Version: 5.2.10-pl0-gentoo
-- Version SVN: $Id: moduleCartCreate.sql 1768 2015-01-09 02:28:07Z ssoares $

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Table structure for table `Cart`
--

CREATE TABLE IF NOT EXISTS `Cart` (
  `C_ID` int(11) NOT NULL,
  `C_CreatedOn` datetime NOT NULL,
  `C_UpdatedOn` datetime NOT NULL,
  PRIMARY KEY  (`C_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `CartItems`
--

DROP TABLE IF EXISTS `CartItems`;

CREATE TABLE IF NOT EXISTS `CartItems` (
  `CI_CartItemsID` int(11) NOT NULL,
  `CI_CartID` int(11) NOT NULL,
  `CI_ID` varchar(50) NOT NULL,
  `CI_ItemID` int(11) NOT NULL,
  `CI_PromoId` INT(11) NOT NULL DEFAULT '0',
  `CI_Quantity` int(11) NOT NULL,
  `CI_Total` float default '0',
  PRIMARY KEY  (`CI_CartItemsID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Données pour activer module et les liens dans le back end
--

INSERT INTO Modules (M_ID, M_Title, M_MVCModuleTitle) VALUES (15, 'Panier', 'cart');

INSERT INTO `ModuleViews` (`MV_ID`, `MV_Name`, `MV_ModuleID`) VALUES
(15001, 'cart_details', 15),
(15002, 'cart_summary', 15);

INSERT INTO ModuleViewsIndex (MVI_ModuleViewsID,MVI_LanguageID,MVI_ActionName) VALUES
(15001, 1, 'details'),
(15001, 2, 'details'),
(15002, 1, 'apercu'),
(15002, 2, 'apercu');

INSERT INTO Pages (P_ID, P_Position, P_ParentID, P_Home, P_LayoutID, P_ThemeID, P_ViewID, P_ShowSiteMap, P_ShowMenu, P_ShowTitle, P_BannerGroupID) VALUES
(15001, 7, 0, 0, 2, 1, 2, 1, 1, 1, NULL);

INSERT INTO PagesIndex (PI_PageID, PI_LanguageID, PI_PageIndex, PI_PageIndexOtherLink, PI_PageTitle, PI_TitleImageSrc, PI_TitleImageAlt, PI_MetaDescription, PI_MetaKeywords, PI_MetaOther, PI_Status, PI_Secure) VALUES
(15001, 1, 'mon-panier', '', 'Mon panier', '', '', '', '', '', 1, 'non'),
(15001, 2, 'my-cart', '', 'My Cart', '', '', '', '', '', 1, 'non');

INSERT INTO ModuleCategoryViewPage (MCVP_ModuleID, MCVP_CategoryID, MCVP_ViewID, MCVP_PageID) VALUES
(15, 0, 15001, 15001);


REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID) VALUES
('module_cart', 1, 'Panier', 'cible', '', 0, 15),
('module_cart', 2, 'Cart', 'cible', '', 0, 15),
('cart_module_name', 1, 'Panier', 'cible', '', 0, 15),
('cart_module_name', 2, 'Cart', 'cible', '', 0, 15),
('cart_order_continue_link', 1, "Commander maintenant", 'client', '', 0, 15),
('cart_order_continue_link', 2, 'Order Now', 'client', '', 0, 15),
('cart_details_action_remove_item', 1, "Retirer cet item", 'client', '', 0, 15),
('cart_details_action_remove_item', 2, 'Remove this item', 'client', '', 0, 15),
('btn_cart_add_another_shirt', 1, "« Ajouter un autre produit", 'client', '', 0, 15),
('btn_cart_add_another_shirt', 2, '« Add another product', 'client', '', 0, 15),
('btn_cart_next_step', 1, "Étape suivante »", 'client', '', 0, 15),
('btn_cart_next_step', 2, 'Next step »', 'client', '', 0, 15),
('cart_steps_identification_number_1', 1, "1. Quantités et formats", 'client', '', 0, 15),
('cart_steps_identification_number_1', 2, '1. Quantities and formats', 'client', '', 0, 15),
('cart_steps_identification_number_2', 1, "2. Paiement et livraisons", 'client', '', 0, 15),
('cart_steps_identification_number_2', 2, '2. Payments and shipments', 'client', '', 0, 15),
('cart_steps_identification_number_3', 1, "3. Procéder à l'achat", 'client', '', 0, 15),
('cart_steps_identification_number_3', 2, '3. Make the purchase', 'client', '', 0, 15),
('cart_steps_identification_number_4', 1, "4. Confirmation", 'client', '', 0, 15),
('cart_steps_identification_number_4', 2, '4. Confirmation', 'client', '', 0, 15),
('cart_step_1_explication_text', 1, "", 'client', 'Panier, étape 1 - Texte explicatif', 0, 15),
('cart_step_1_explication_text', 2, "", 'client', 'Panier, étape 1 - Texte explicatif', 0, 15),
('cart_step_label', 1, "Étape ##step##/4", 'client', '', 0, 15),
('cart_step_label', 2, 'Step ##step##/4', 'client', '', 0, 15),
('cart_no_item', 1, "Il n'y a aucun item dans votre panier.", 'client', '', 0, 15),
('cart_no_item', 2, "There has no item in your cart.", 'client', '', 0, 15),
('cart_details_quantity_label', 1, 'Qté', 'client', '', 0, 15),
('cart_details_quantity_label', 2, 'Qty', 'client', '', 0, 15),
('cart_details_size_label', 1, 'Format', 'client', '', 0, 15),
('cart_details_size_label', 2, 'Format', 'client', '', 0, 15),
('cart_details_size_descr_label', 1, 'Description', 'client', '', 0, 15),
('cart_details_size_descr_label', 2, 'Description', 'client', '', 0, 15),
('cart_details_select_choose_label', 1, 'Choisir', 'client', '', 0, 15),
('cart_details_select_choose_label', 2, 'Select', 'client', '', 0, 15),
('form_select_option_view_cart_cart_details', 1, 'Détails du panier', 'cible', '', 0, 15),
('form_select_option_view_cart_cart_details', 2, 'Cart details', 'cible', '', 0, 15),
('form_select_option_view_cart_cart_summary', 1, 'Résumé du panier', 'cible', '', 0, 15),
('form_select_option_view_cart_cart_summary', 2, 'Cart summary', 'cible', '', 0, 15),
('cart_summary_title', 1, 'Sommaire du panier', 'client', '', 0, 15),
('cart_summary_title', 2, 'Cart summary', 'client', '', 0, 15),
('cart_summary_number_items', 1, 'Nombre :', 'client', '', 0, 15),
('cart_summary_number_items', 2, 'Quantity :', 'client', '', 0, 15),
('cart_summary_subtotal', 1, 'Sous-total :', 'client', '', 0, 15),
('cart_summary_subtotal', 2, 'Subtotal :', 'client', '', 0, 15),
('cart_summary_see_cart', 1, 'Voir votre panier »', 'client', '', 0, 15),
('cart_summary_see_cart', 2, 'See your cart »', 'client', '', 0, 15);

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID) VALUES
('cart_transport_label', 1, 'Transport :', 'client', '', 0, 15),
('cart_transport_label', 2, 'Shipping :', 'client', '', 0, 15),
('cart_tvq_label', 1, 'TVQ :', 'client', '', 0, 15),
('cart_tvq_label', 2, 'TVQ :', 'client', '', 0, 15),
('cart_tps_label', 1, 'TPS :', 'client', '', 0, 15),
('cart_tps_label', 2, 'TPS :', 'client', '', 0, 15),
('cart_total_label', 1, 'Total :', 'client', '', 0, 15),
('cart_total_label', 2, 'Total :', 'client', '', 0, 15),
('cart_item_total_label', 1, 'Total', 'client', '', 0, 15),
('cart_item_total_label', 2, 'Total', 'client', '', 0, 15),
('unit_price_label', 1, 'Prix', 'client', '', 0, 15),
('unit_price_label', 2, 'Unit', 'client', '', 0, 15);
