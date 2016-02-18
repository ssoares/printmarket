-- Version SVN: $Id: moduleCatalogCreate.sql 1872 2016-02-09 19:58:47Z ssoares $

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

-- --------------------------------------------------------

--
-- Table structure for table `AssociatedProducts`
--

DROP TABLE IF EXISTS `Catalog_AssociatedProducts`;
CREATE TABLE IF NOT EXISTS `Catalog_AssociatedProducts` (
  `AP_MainProductID` int(11) NOT NULL,
  `AP_RelatedProductID` int(11) NOT NULL,
  `AP_Seq` int(11) NOT NULL DEFAULT '100',
  PRIMARY KEY  (`AP_MainProductID`,`AP_RelatedProductID`),
  KEY `MainProd_Product` (`AP_MainProductID`),
  KEY `AssocProd_Product` (`AP_RelatedProductID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Categories`
--

DROP TABLE IF EXISTS `Catalog_CategoriesData`;
CREATE TABLE IF NOT EXISTS `Catalog_CategoriesData` (
  `CC_ID` int(11) NOT NULL auto_increment,
  `CC_ParentId` INT(11) NULL COMMENT 'elem:select|src:categories',
  `CC_imageCat` varchar(255) default NULL COMMENT 'elem:image',
  `CC_Online` tinyint(1) NULL COMMENT 'elem:checkbox',
  `CC_Seq` INT(11) NULL,
  PRIMARY KEY  (`CC_ID`),
  KEY `CC_CI` (`CC_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `CategoriesIndex`
--

DROP TABLE IF EXISTS `Catalog_CategoriesIndex`;
CREATE TABLE IF NOT EXISTS `Catalog_CategoriesIndex` (
  `CCI_CategoryID` int(11) NOT NULL,
  `CCI_LanguageID` int(2) NOT NULL default '1',
  `CCI_Name` varchar(255) NOT NULL COMMENT 'seq:1',
  `CCI_ValUrl` varchar(255) default NULL COMMENT 'exclude:true',
  `CCI_MetaId` int(11) default NULL COMMENT 'exclude:true',
  `CCI_Seq` int(11) DEFAULT '100' COMMENT 'exclude:true',
  `CCI_Description` TEXT NULL COMMENT 'elem:tiny',
  PRIMARY KEY  (`CCI_CategoryID`,`CCI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Items`
--

DROP TABLE IF EXISTS `Catalog_ItemsData`;
CREATE TABLE `Catalog_ItemsData` (
  `I_ID` int(11) NOT NULL AUTO_INCREMENT,
  `I_ProductID` int(11) NOT NULL COMMENT 'elem:select|src:products',
  `I_Number` varchar(255) DEFAULT NULL,
  `I_Seq` int(11) DEFAULT '100',
  `I_PriceDetail` decimal(9,2) DEFAULT NULL,
  `I_PricePro` decimal(9,2) DEFAULT NULL COMMENT 'exclude:true',
  `I_PriceVol1` decimal(9,2) DEFAULT NULL COMMENT 'exclude:true',
  `I_PriceVol2` decimal(9,2) DEFAULT NULL COMMENT 'exclude:true',
  `I_PriceVol3` decimal(9,2) DEFAULT NULL COMMENT 'exclude:true',
  `I_LimitVol` int(11) DEFAULT NULL COMMENT 'exclude:true',
  `I_DispLogged` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'elem:checkbox|exclude:true',
  `I_NoAddToCart` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'elem:checkbox',
  `I_Special` tinyint(1) DEFAULT NULL COMMENT 'elem:checkbox',
  `I_SpecialPrice` decimal(9,2) DEFAULT NULL,
  `I_DiscountPercent` decimal(5,2) DEFAULT NULL COMMENT 'exclude:true',
  `I_TaxProv` tinyint(1) DEFAULT '1' COMMENT 'exclude:true|elem:checkbox',
  `I_TaxFed` tinyint(1) DEFAULT '1' COMMENT 'exclude:true|elem:checkbox',
  PRIMARY KEY (`I_ID`),
  KEY `Items_ItemIndex` (`I_ID`),
  KEY `Items_Produit` (`I_ProductID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ItemIndex`
--

CREATE TABLE `Catalog_ItemsIndex` (
  `II_ItemID` int(11) NOT NULL,
  `II_LanguageID` int(11) NOT NULL,
  `II_Name` varchar(255) NOT NULL COMMENT 'seq:1',
  `II_MetaId` int(11) DEFAULT NULL COMMENT 'exclude:true',
  `II_Seq` int(11) DEFAULT '100' COMMENT 'exclude:true',
  `II_ValUrl` VARCHAR(255) NULL DEFAULT NULL COMMENT 'exclude:true' ,
  PRIMARY KEY (`II_ItemID`,`II_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Catalog_ItemsPromo`
--

-- CREATE TABLE IF NOT EXISTS `Catalog_ItemsPromo` (
--   `IP_ID` int(11) NOT NULL auto_increment,
--   `IP_ItemId` int(11) NOT NULL,
--   `IP_Price` float NOT NULL,
--   `IP_ConditionItemId` int(11) NOT NULL,
--   `IP_NbItem` int(5) NOT NULL,
--   `IP_ConditionAmount` float NOT NULL,
--   PRIMARY KEY  (`IP_ID`)
-- ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;


-- --------------------------------------------------------

--
-- Table structure for table `Products`
--

DROP TABLE IF EXISTS `Catalog_ProductsData`;
CREATE TABLE IF NOT EXISTS `Catalog_ProductsData` (
  `P_ID` int(11) NOT NULL auto_increment,
  `P_CategoryId` int(11) NOT NULL COMMENT 'elem:multiSelect|src:category|group:productFormLeft|seq:2',
  `P_Photo` varchar(255) DEFAULT NULL COMMENT 'elem:image|group:productFormRight',
  `P_Number` varchar(100) DEFAULT NULL COMMENT 'group:productFormLeftBis',
  `P_Inactive` TINYINT(1) NULL COMMENT 'elem:checkbox',
  `P_IsNew` TINYINT(1) NULL COMMENT 'elem:checkbox',
  `P_Closeout` TINYINT(1) NULL COMMENT 'elem:checkbox',
  `P_Solde` INT(1) NULL COMMENT 'elem:checkbox',
  `P_Seq` INT(11) NULL COMMENT 'class:shortTextInput',
  `P_Keywords` VARCHAR(255) NULL DEFAULT NULL COMMENT 'elem:multiSelect|src:productsKeywords|group:productFormLeftBis|seq:1|shortCut:true|class:hasShortcut',
--   `P_Collection` INT(11) NULL DEFAULT '0' COMMENT 'elem:select|src:collections' ,
--   `P_CumulPoint` tinyint(1) default '1',
  PRIMARY KEY  (`P_ID`),
  KEY `Produit_ProduitIndex` (`P_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ProductsIndex`
--

DROP TABLE IF EXISTS `Catalog_ProductsIndex`;
CREATE TABLE IF NOT EXISTS `Catalog_ProductsIndex` (
  `PI_ProductID` int(11) NOT NULL,
  `PI_LanguageID` int(11) NOT NULL,
  `PI_Name` varchar(255) NOT NULL COMMENT 'seq:1|group:productFormLeft',
  `PI_Description` text COMMENT 'elem:tiny|group:productFormBottom|class:largeEditor',
--   `PI_Options` text COMMENT 'elem:tiny|class:largeEditor',
--   `PI_Notes` text COMMENT 'elem:tiny|class:largeEditor',
--   `PI_MotsCles` VARCHAR(255) NULL DEFAULT NULL COMMENT 'exclude:true' ,
  `PI_ValUrl` VARCHAR(255) NULL DEFAULT NULL COMMENT 'exclude:true' ,
--   `PI_MetaId` INT(11) NULL DEFAULT NULL COMMENT 'exclude:true' ,
  `PI_Seq` INT(11) NULL DEFAULT '100' COMMENT 'exclude:true' ,
  PRIMARY KEY  (`PI_ProductID`,`PI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Table structure for table `Catalog_Products_Categories`
--

CREATE TABLE `Catalog_Products_Categories` (
  `CPC_ProductId` INT(11) NOT NULL,
  `CPC_CategoryId` int(11) NOT NULL,
  PRIMARY KEY (`CPC_ProductId`, `CPC_CategoryId`))
ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `Catalog_Products_Keywords`
--

CREATE TABLE `Catalog_Products_Keywords` (
  `CPK_ProductId` INT(11) NOT NULL,
  `CPK_RefId` int(11) NOT NULL,
  PRIMARY KEY (`CPK_ProductId`, `CPK_RefId`))
ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `ProductsIndex`
--

CREATE TABLE `Catalog_Products_Tabs` (
  `CPT_ProductId` INT(11) NOT NULL,
  `CPT_TabId` int(11) NOT NULL,
  `CPT_LanguageId` int(2) NOT NULL,
  `CPT_TabTitle` varchar(255),
  `CPT_TabText` TEXT,
  PRIMARY KEY (`CPT_ProductId`, `CPT_TabId`, `CPT_LanguageId`))
ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE `Catalog_ProductsImages` (
  `CPI_ID` int(11) NOT NULL AUTO_INCREMENT,
  `CPI_ProductId` int(11) NOT NULL,
  `CPI_Img` varchar(255) NOT NULL,
  `CPI_Seq` int(11) DEFAULT NULL,
  PRIMARY KEY (`CPI_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--------------------------------------------------------

--
-- Table structure for table `Catalog_TaxesProv`
--


DROP TABLE IF EXISTS `Catalog_TaxeZone`;
CREATE TABLE IF NOT EXISTS `Catalog_TaxeZone` (
  `TZ_ID` int(11) NOT NULL auto_increment,
  `TZ_CountryCode` char(2) NOT NULL,
  `TZ_Country` char(30) NOT NULL,
  `TZ_ProvCode` char(2) NOT NULL,
  `TZ_Province` char(30) NOT NULL,
  `TZ_GroupName` char(10) NOT NULL,
  `TZ_TaxValue1` float NOT NULL default '0',
  `TZ_TaxValue2` float NOT NULL default '0',
  `TZ_TaxValue3` float NOT NULL default '0',
  `TZ_TaxValue4` float NOT NULL default '0',
  `TZ_TaxValue5` float NOT NULL default '0',
  PRIMARY KEY  (`TZ_ProvCode`,`TZ_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `Catalog_TaxeZone`
--

INSERT INTO `Catalog_TaxeZone` (`TZ_ID`, `TZ_CountryCode`, `TZ_Country`, `TZ_ProvCode`, `TZ_Province`, `TZ_GroupName`, `TZ_TaxValue1`, `TZ_TaxValue2`, `TZ_TaxValue3`, `TZ_TaxValue4`, `TZ_TaxValue5`) VALUES
(13, 'CA', 'Canada', 'YT', 'Yukon Territory', 'T.V.H.', 0.13, 0, 0, 0, 0),
(12, 'CA', 'Canada', 'SK', 'Saskatchewan', 'T.V.H.', 0.13, 0, 0, 0, 0),
(11, 'CA', 'Canada', 'QC', 'Quebec', 'QC', 0.05, 0.0975, 0, 0, 0),
(10, 'CA', 'Canada', 'PE', 'Prince Edward Island', 'T.V.H.', 0.13, 0, 0, 0, 0),
(9, 'CA', 'Canada', 'ON', 'Ontario', 'T.V.H.', 0.13, 0, 0, 0, 0),
(8, 'CA', 'Canada', 'NU', 'Nunavut', 'T.V.H.', 0.13, 0, 0, 0, 0),
(7, 'CA', 'Canada', 'NT', 'Northwest Territories', 'T.V.H.', 0.13, 0, 0, 0, 0),
(6, 'CA', 'Canada', 'NS', 'Nova Scotia', 'T.V.H.', 0.13, 0, 0, 0, 0),
(5, 'CA', 'Canada', 'NB', 'New Brunswick', 'T.V.H.', 0.13, 0, 0, 0, 0),
(4, 'CA', 'Canada', 'NF', 'Newfoundland', 'T.V.H.', 0.13, 0, 0, 0, 0),
(3, 'CA', 'Canada', 'MB', 'Manitoba', 'T.V.H.', 0.13, 0, 0, 0, 0),
(2, 'CA', 'Canada', 'BC', 'British Columbia', 'T.V.H.', 0.13, 0, 0, 0, 0),
(1, 'CA', 'Canada', 'AB', 'Alberta', 'T.V.H.', 0.13, 0, 0, 0, 0);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

--
-- Données pour activer module et les liens dans le back end
--

REPLACE INTO Modules (M_ID, M_Title, M_MVCModuleTitle, M_Indexation) VALUES (14, 'Catalogue', 'catalog', 'ProductsData');

REPLACE INTO Modules_ControllersActionsPermissions (MCAP_ModuleID, MCAP_ControllerTitle, MCAP_ControllerActionTitle, MCAP_PermissionTitle, MCAP_Position) VALUES
(14, 'index', 'categories', 'edit', 1),
(14, 'index', 'products', 'edit', 3),
(14, 'index', 'items', 'edit', 4);

INSERT INTO ModuleViews (MV_ID, MV_Name, MV_ModuleID) VALUES
-- (14001, 'detail', 14),
(14002, 'list', 14),
(14003, 'search_results', 14);

INSERT INTO `ModuleViewsIndex` (`MVI_ModuleViewsID`, `MVI_LanguageID`, `MVI_ActionName`) VALUES
-- (14001, 1, 'detail'),
-- (14001, 2, 'details'),
(14002, 1, 'toutes'),
(14002, 2, 'list-all'),
(14003, 2, 'search-results'),
(14003, 1, 'resultats-recherche');

INSERT INTO Pages (P_ID, P_Position, P_ParentID, P_Home, P_LayoutID, P_ThemeID, P_ViewID, P_ShowSiteMap, P_ShowMenu, P_ShowTitle, P_BannerGroupID) VALUES
(14001, 6, 0, 0, 2, 1, 2, 1, 1, 1, NULL);

INSERT INTO PagesIndex (PI_PageID, PI_LanguageID, PI_PageIndex, PI_PageIndexOtherLink, PI_PageTitle, PI_TitleImageSrc, PI_TitleImageAlt, PI_MetaDescription, PI_MetaKeywords, PI_MetaOther, PI_Status, PI_Secure) VALUES
(14001, 1, 'catalogue', '', 'Catalogue', '', '', '', '', '', 1, 'non'),
(14001, 2, 'catalog-en', '', 'Catalog', '', '', '', '', '', 1, 'non');

INSERT INTO ModuleCategoryViewPage (MCVP_ModuleID, MCVP_CategoryID, MCVP_ViewID, MCVP_PageID) VALUES
(14, 0, 14003, 14001),
(14, 0, 14002, 14001);

INSERT INTO Extranet_Resources (ER_ID, ER_ControlName) VALUES (14, 'catalog');

INSERT INTO Extranet_ResourcesIndex (ERI_ResourceID, ERI_LanguageID, ERI_Name) VALUES
(14, 1, 'Catalogue'),
(14, 2, 'catalog');

INSERT INTO Extranet_RolesResources (ERR_ID, ERR_RoleID, ERR_ResourceID, ERR_InheritedParentID) VALUES
(1403,1, 14, 0);

INSERT INTO Extranet_RolesResourcesIndex (ERRI_RoleResourceID, ERRI_LanguageID, ERRI_Name, ERRI_Description) VALUES
(1403,1, 'Gestionnaire de catalogue', ''),
(1403,2, 'Catalog manager', '');

INSERT INTO Extranet_RolesResourcesPermissions (ERRP_RoleResourceID, ERRP_PermissionID) VALUES
(1403, 1);

--

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('Module_catalog', 1, 'Catalogue', 'cible', '', 0, 14),
('Module_catalog', 2, 'Catalog', 'cible', '', 0, 14),
('catalog_module_name', 1, 'Catalogue', 'cible', '', 0, 14),
('catalog_module_name', 2, 'Catalog', 'cible', '', 0, 14),
('management_module_catalog_categories', 1, 'Catégories de produits', 'cible', '', 0, 14),
('management_module_catalog_categories', 2, 'Products categories', 'cible', '', 0, 14),
('management_module_catalog_products', 1, 'Produits', 'cible', '', 0, 14),
('management_module_catalog_products', 2, 'Products', 'cible', '', 0, 14),
('management_module_catalog_items', 1, 'S.K.U.', 'cible', '', 0, 14),
('management_module_catalog_items', 2, 'S.K.U.', 'cible', '', 0, 14),
('header_list_categories_text', 1, 'Liste des catégories du catalogue', 'cible', '', 0, 14),
('header_list_categories_text', 2, 'List of the catalog categories', 'cible', '', 0, 14),
('header_list_categories_description', 1, 'Cliquez sur <b>Ajouter une catégorie</b> pour créer une catégorie.<br><br>Vous pouvez <b>rechercher par mots-clés</b> parmi la liste des catégories. Pour revenir à la liste complète, cliquez sur <b>Voir la liste complète</b>.<br><br>Vous pouvez <b>modifier ou supprimer une catégories</b> en cliquant sur l''icône <img src="/extranet/icons/icon-add-24x24.png" align=middle>.', 'cible', '', 0, 14),
('header_list_categories_description', 2, 'This page is to manage all the news.', 'cible', '', 0, 14),
('header_add_categories_text', 1, 'Ajouter une catégorie', 'cible', '', 0, 14),
('header_add_categories_text', 2, 'Add a new category', 'cible', '', 0, 14),
('header_add_categories_description', 1, "Cette page permet d'ajouter une nouvelle catégorie au catalogue.", 'cible', '', 0, 14),
('header_add_categories_description', 2, 'This page is to add a new category to the catalog.', 'cible', '', 0, 14),
('header_edit_categories_text', 1, 'Modifier une catégorie', 'cible', '', 0, 14),
('header_edit_categories_text', 2, 'Edit a category', 'cible', '', 0, 14),
('header_edit_categories_description', 1, "Cette page permet d'éditer les informations de la catégorie.", 'cible', '', 0, 14),
('header_edit_categories_description', 2, 'This page is to edit data of the current category.', 'cible', '', 0, 14),
('header_delete_categories_text', 1, "Suppression d'une catégorie", 'cible', '', 0, 14),
('header_delete_categories_text', 2, 'Deletion of a category', 'cible', '', 0, 14),
('catalog_category_block_page', 1, "Catégorie par défault", 'cible', '', 0, 14),
('catalog_category_block_page', 2, 'Default category', 'cible', '', 0, 14),
('form_search_catalog_keywords_label', 1, 'Mots-clés', 'cible', '', 0, 14),
('form_search_catalog_keywords_label', 2, 'Keywords', 'cible', '', 0, 14),
('form_select_category_label', 1, 'Appartient à la catégorie:', 'cible', '', 0, 14),
('form_select_category_label', 2, 'Associated to the category:', 'cible', '', 0, 14);

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('header_add_products_text', 1, "Ajout d'un produit", 'cible', '', 0, 14),
('header_add_products_text', 2, 'Add a new product', 'cible', '', 0, 14),
('header_add_products_description', 1, 'Cette page permet d''ajouter un nouveau produit.', 'cible', '', 0, 14),
('header_add_products_description', 2, 'This page is to add a new product.', 'cible', '', 0, 14),
('header_edit_products_text', 1, "Editer un produit", 'cible', '', 0, 14),
('header_edit_products_text', 2, 'Edit a product', 'cible', '', 0, 14),
('header_edit_products_description', 1, 'Cette page permet d''éditer un produit.', 'cible', '', 0, 14),
('header_edit_products_description', 2, 'This page is to edit a product.', 'cible', '', 0, 14),
('header_list_products_text', 1, 'Liste des produits', 'cible', '', 0, 14),
('header_list_products_text', 2, 'Product list', 'cible', '', 0, 14),
('header_list_products_description', 1, 'Cette vous permet de gérer les produits. <p>Sélectionner un produit dans la liste ou ajoutez-en un nouveau.</p>', 'cible', '', 0, 14),
('header_list_products_description', 2, 'This page is to manage the products. <p>Select a product or add a new one.</p>', 'cible', '', 0, 14),
('product_label_name', 1, 'Nom du produit', 'cible', '', 0, 14),
('product_label_name', 2, 'Name of the product', 'cible', '', 0, 14),
('form_product_accumulation_label', 1, 'Ce produit donne des Cumule-Points', 'cible', '', 0, 14),
('form_product_accumulation_label', 2, 'This product gives "Cumule-Points"', 'cible', '', 0, 14),
('form_product_isnew_label', 1, 'Afficher ce produit dans les nouveautés', 'cible', '', 0, 14),
('form_product_isnew_label', 2, 'Display this product as a new one.', 'cible', '', 0, 14),
('subform_public_legend', 1, 'Grand Public', 'cible', '', 0, 14),
('subform_public_legend', 2, 'Public', 'cible', '', 0, 14),
('product_label_descriptionPublic', 1, 'Description', 'cible', '', 0, 14),
('product_label_descriptionPublic', 2, 'Description', 'cible', '', 0, 14),
('product_label_notePublic', 1, 'Note supplémentaire (post-it)', 'cible', '', 0, 14),
('product_label_notePublic', 2, 'Additionnal note (post-it)', 'cible', '', 0, 14),
('product_label_technical_specs', 1, 'Fiche technique (PDF)', 'cible', '', 0, 14),
('product_label_technical_specs', 2, 'Specs sheet (PDF)', 'cible', '', 0, 14),
('subform_professional_legend', 1, 'Professionnels', 'cible', '', 0, 14),
('subform_professional_legend', 2, 'Professionals', 'cible', '', 0, 14),
('product_label_descriptionPro', 1, 'Description', 'cible', '', 0, 14),
('product_label_descriptionPro', 2, 'Description', 'cible', '', 0, 14),
('product_label_notePro', 1, 'Note supplémentaire (post-it)', 'cible', '', 0, 14),
('product_label_notePro', 2, 'Additionnal note (post-it)', 'cible', '', 0, 14),
('product_label_tool_promo', 1, 'Outil promotionnel (PDF)', 'cible', '', 0, 14),
('product_label_tool_promo', 2, 'Promotion tool (PDF)', 'cible', '', 0, 14),
('association_set_selectOne', 1, '-- Choisir un produit --', 'cible', '', 0, 14),
('association_set_selectOne', 2, '-- Select a product --', 'cible', '', 0, 14),
('form_products_subcat_label', 1, 'Associé à la sous-catégorie:', 'cible', '', 0, 14),
('form_products_subcat_label', 2, 'Associated to the subcategory:', 'cible', '', 0, 14);


REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('header_list_items_text', 1, "Liste des SKU", 'cible', '', 0, 14),
('header_list_items_text', 2, 'List of SKU', 'cible', '', 0, 14),
('header_list_items_description', 1, "Cette page permet de gérer la liste des SKU associés aux produits.", 'cible', '', 0, 14),
('header_list_items_description', 2, 'This page is to manage SKU', 'cible', '', 0, 14),
('list_column_I_ID', 1, "Id du SKU", 'cible', '', 0, 14),
('list_column_I_ID', 2, 'SKU Id', 'cible', '', 0, 14),
('list_column_II_Name', 1, "Libellé du SKU", 'cible', '', 0, 14),
('list_column_II_Name', 2, 'SKU label', 'cible', '', 0, 14),
('form_product_code_label', 1, "Code produit", 'cible', '', 0, 14),
('form_product_code_label', 2, 'Product code', 'cible', '', 0, 14),
('header_add_items_text', 1, "Ajouter un SKU", 'cible', '', 0, 14),
('header_add_items_text', 2, 'Add a SKU', 'cible', '', 0, 14),
('header_add_items_description', 1, "Renseigner les champs du formulaire.", 'cible', '', 0, 14),
('header_add_items_description', 2, 'Fill the form to add data.', 'cible', '', 0, 14),
('header_edit_items_text', 1, "Modification du SKU", 'cible', '', 0, 14),
('header_edit_items_text', 2, 'Edit SKU', 'cible', '', 0, 14),
('header_edit_items_description', 1, "Cette page permet de modifier des informations du SKU séléctionné.", 'cible', '', 0, 14),
('header_edit_items_description', 2, 'This page is to edit data of the selected SKU.', 'cible', '', 0, 14),
('header_delete_items_text', 1, "Suppression d'un SKU", 'cible', '', 0, 14),
('header_delete_items_text', 2, 'Deletion of a SKU', 'cible', '', 0, 14),
('header_delete_categories_text', 1, "Suppression d'une catégorie", 'cible', '', 0, 14),
('header_delete_categories_text', 2, 'Deletion of a category', 'cible', '', 0, 14),
('products_no_product', 1, "Il n'y a actuellement aucun produit dans cette catégorie.", 'client', '', 0, 14),
('products_no_product', 2, 'There is no product for this category.', 'client', '', 0, 14),
('form_select_option_view_catalog_detail', 1, "Détails de produits", 'cible', '', 0, 14),
('form_select_option_view_catalog_detail', 2, 'Details of the product', 'cible', '', 0, 14),
('form_select_option_view_catalog_list', 2, 'List of products', 'cible', '', 0, 14),
('form_select_option_view_catalog_list', 1, 'Liste de produits', 'cible', '', 0, 14),
('form_select_option_view_catalog_search_results', 1, 'Résultats de recherche', 'cible', '', 0, 14),
('form_select_option_view_catalog_search_results', 2, 'Search results', 'cible', '', 0, 14),
('management_module_catalog_items_promo', '1', 'Items en promotions', 'cible', '', 0, 14),
('management_module_catalog_items_promo', '2', 'Special offer', 'cible', '', 0, 14),
('form_item_noAdd_label', 1, "Ne peut pas être ajouté au panier", 'client', '', 0, 14),
('form_item_noAdd_label', 2, 'Not possible to add to cart', 'client', '', 0, 14),
('products_details_noAdd_cart', 1, "* Contactez-nous pour commander cet article.", 'client', '', 0, 14),
('products_details_noAdd_cart', 2, '* Contact us to order this item.', 'client', '', 0, 14),
('products_details_noAdd_cart_short', 1, "Contactez-nous *", 'client', '', 0, 14),
('products_details_noAdd_cart_short', 2, 'Contact us *', 'client', '', 0, 14),
('form_address_retailer_fr', 1, "Adresse en français", 'cible', '', 0, 14),
('form_address_retailer_fr', 2, 'Address in french', 'cible', '', 0, 14),
('form_address_retailer_en', 1, "Adresse en anglais", 'cible', '', 0, 14),
('form_address_retailer_en', 2, 'Address in english', 'cible', '', 0, 14);

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID, ST_RichText) VALUES
('form_label_CCI_Name', 1, "Nom de la catégorie", "cible", "", 0, 14, 0),	('form_label_CCI_Name', 2, "Category name", "cible", "", 0, 14, 0),
('form_label_CC_ParentId', 1, "Rattachée à la catégorie", "cible", "", 0, 14, 0),	('form_label_CC_ParentId', 2, "Related to the category", "cible", "", 0, 14, 0),
('form_label_CC_Seq', 1, "Position", "cible", "", 0, 14, 0),	('form_label_CC_Seq', 2, "Position", "cible", "", 0, 14, 0),
('form_label_CCI_Description', 1, "Description de la catégorie", "cible", "", 0, 14, 0),	('form_label_CCI_Description', 2, "Category description", "cible", "", 0, 14, 0),
('form_label_PI_Name', 1, "Nom du produit", "cible", "", 0, 14, 0),	('form_label_PI_Name', 2, "Product name", "cible", "", 0, 14, 0),
('form_label_P_CategoryId', 1, "Catégorie", "cible", "", 0, 14, 0),	('form_label_P_CategoryId', 2, "Category", "cible", "", 0, 14, 0),
('form_label_P_Seq', 1, "Position", "cible", "", 0, 14, 0),	('form_label_P_Seq', 2, "Position", "cible", "", 0, 14, 0),
('form_label_PI_Description', 1, "Description du produit", "cible", "", 0, 14, 0),	('form_label_PI_Description', 2, "Product description", "cible", "", 0, 14, 0),
('form_label_PI_Options', 1, "Options", "cible", "", 0, 14, 0),	('form_label_PI_Options', 2, "Options", "cible", "", 0, 14, 0),
('form_label_PI_Notes', 1, "Notes", "cible", "", 0, 14, 0),	('form_label_PI_Notes', 2, "Notes", "cible", "", 0, 14, 0),
('header_delete_products_text', 1, "Suppression du produit", "cible", "", 0, 14, 0),
('header_delete_products_text', 2, "Delete product", "cible", "", 0, 14, 0),
('form_label_PI_Name_associate', 1, "Associer le(s) produit(s) suivant :", "cible", "", 0, 14, 0),
('form_label_PI_Name_associate', 2, "Associate following product(s)", "cible", "", 0, 14, 0),
('form_label_P_Number',1,'Code produit','cible','',0,14,0),
('form_label_P_Number',2,'Product code','cible','',0,14,0),
('form_label_I_ProductID', 1, "Associé au produit", "cible", "", 0, 14, 0),
('form_label_I_ProductID', 2, "Related to  the product", "cible", "", 0, 14, 0),
('form_label_I_Seq', 1, "Position", "cible", "", 0, 14, 0),
('form_label_I_Seq', 2, "Position", "cible", "", 0, 14, 0),
('form_label_II_Name', 1, "Libellé du SKU", "cible", "", 0, 14, 0),
('form_label_II_Name', 2, "SKU Label", "cible", "", 0, 14, 0),
('form_label_I_Number', 1, "Code SKU", "cible", "", 0, 14, 0),
('form_label_I_Number', 2, "SKU code", "cible", "", 0, 14, 0),
('form_label_related_products', 1, "Nous vous suggérons aussi", "cible", "", 0, 14, 0),
('form_label_related_products', 2, "We also suggest", "cible", "", 0, 14, 0);
REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID, ST_RichText) VALUES
('form_label_I_DispLogged', 1, "Affiché seulement si connecté", "cible", "", 0, 14, 0),
('form_label_I_DispLogged', 2, "Only display if logged in", "cible", "", 0, 14, 0),
('form_label_I_NoAddToCart', 1, "Ne peut être ajouté au panier", "cible", "", 0, 14, 0),
('form_label_I_NoAddToCart', 2, "Can not be added to cart", "cible", "", 0, 14, 0),
('form_label_I_PriceDetail', 1, "Prix unitaire", "cible", "", 0, 14, 0),
('form_label_I_PriceDetail', 2, "Unit price", "cible", "", 0, 14, 0),
('form_label_I_PriceVol1', 1, "&nbsp;", "cible", "", 0, 14, 0),
('form_label_I_PriceVol1', 2, "&nbsp;", "cible", "", 0, 14, 0),
('form_label_I_PriceVol2', 1, "&nbsp;", "cible", "", 0, 14, 0),
('form_label_I_PriceVol2', 2, "&nbsp;", "cible", "", 0, 14, 0),
('form_label_I_PriceVol3', 1, "Et plus", "cible", "", 0, 14, 0),
('form_label_I_PriceVol3', 2, "Over ", "cible", "", 0, 14, 0),
('form_label_I_LimitVol1', 1, "De 1 à ", "cible", "", 0, 14, 0),
('form_label_I_LimitVol1', 2, "1 to", "cible", "", 0, 14, 0),
('form_label_I_SpecialPrice', 1, "En solde", "cible", "", 0, 14, 0),
('form_label_I_SpecialPrice', 2, "Solde", "cible", "", 0, 14, 0),
('form_label_I_DiscountPercent', 1, "% de réduction", "cible", "", 0, 14, 0),
('form_label_I_DiscountPercent', 2, "Discount %", "cible", "", 0, 14, 0),
('form_label_I_LimitVol2', 1, "jusqu'à ", "cible", "", 0, 14, 0),
('form_label_I_LimitVol2', 2, "up to", "cible", "", 0, 14, 0),
('form_label_CC_Online', 1, "En ligne", "cible", "", 0, 14, 0),
('form_label_CC_Online', 2, "Online", "cible", "", 0, 14, 0),
('form_label_P_Inactive', 1, "Ne pas afficher en ligne", "cible", "", 0, 14, 0),
('form_label_P_Inactive', 2, "Do not display online", "cible", "", 0, 14, 0),
('form_label_I_IsNew', 1, "Nouveauté", "cible", "", 0, 14, 0),
('form_label_I_IsNew', 2, "Novelty", "cible", "", 0, 14, 0),
('form_label_I_IsCloseout', 1, "En liquidation", "cible", "", 0, 14, 0),
('form_label_I_IsCloseout', 2, "Discount %", "cible", "", 0, 14, 0),
('form_label_I_CloseoutPrice', 1, "% de réduction", "cible", "", 0, 14, 0),
('form_label_I_CloseoutPrice', 2, "Discount %", "cible", "", 0, 14, 0),
('form_label_P_Keywords', 1, "Mots-clés", "cible", "", 0, 14, 0),
('form_label_P_Keywords', 2, "Keywords", "cible", "", 0, 14, 0),
('form_enum_productsKeywords', 1, "Mots-clés (produits)", "cible", "", 0, 14, 0),
('form_enum_productsKeywords', 2, "Keywords (products)", "cilbe", "", 0, 14, 0),
('form_label_P_Solde', 1, "En solde", "cible", "", 0, 14, 0),
('form_label_P_Solde', 2, "Solde", "cible", "", 0, 14, 0),
('form_label_P_Closeout', 1, "En liquidation", "cible", "", 0, 14, 0),
('form_label_P_Closeout', 2, "Closeout", "cible", "", 0, 14, 0),
('form_label_P_IsNew', 1, "Nouveauté", "cible", "", 0, 14, 0),
('form_label_P_IsNew', 2, "New product", "cible", "", 0, 14, 0),
('form_label_P_Tabs', 1, "Libellé de l'onglet ", "cible", "", 0, 14, 0),
('form_label_P_Tabs', 2, "Label for tab ", "cible", "", 0, 14, 0),
('form_label_P_TabsText', 1, "Contenu de l'onglet ", "cible", "", 0, 14, 0),
('form_label_P_TabsText', 2, "Content for tab ", "cible", "", 0, 14, 0),
('block_catalog_listType', 1, "Choisir le type de liste", "cible", "", 0, 14, 0),
('block_catalog_listType', 2, "Select the type of list", "cible", "", 0, 14, 0),
('block_catalog_listSubtype', 1, "Choisir l'affichage des produits", "cible", "", 0, 14, 0),
('block_catalog_listSubtype', 2, "Select which data to display", "cible", "", 0, 14, 0),
('block_catalog_list_onlyNew', 1, "Afficher seulement les nouveautés", "cible", "", 0, 14, 0),
('block_catalog_list_onlyNew', 2, "Only display new products", "cible", "", 0, 14, 0),
('block_catalog_list_onlySolde', 1, "Afficher seulement les soldes", "cible", "", 0, 14, 0),
('block_catalog_list_onlySolde', 2, "Only display solde", "cible", "", 0, 14, 0),
('block_catalog_onlyCloseout', 1, "Afficher seulement les liquidations", "cible", "", 0, 14, 0),
('block_catalog_onlyCloseout', 2, "Only display closeouts", "cible", "", 0, 14, 0),
('block_catalog_option_1_list', 1, "Afficher par catégories puis produits", "cible", "", 0, 14, 0),
('block_catalog_option_1_list', 2, "Display categories then products", "cible", "", 0, 14, 0),
('block_catalog_option_2_list', 1, "Afficher les produits des catégories.", "cible", "", 0, 14, 0),
('block_catalog_option_2_list', 2, "Display products from categories", "cible", "", 0, 14, 0),
('block_catalog_option_1_sublist', 1, "Produits", "cible", "", 0, 14, 0),
('block_catalog_option_1_sublist', 2, "Products", "cible", "", 0, 14, 0),
('block_catalog_option_2_sublist', 1, "SKU", "cible", "", 0, 14, 0),
('block_catalog_option_2_sublist', 2, "SKU", "cible", "", 0, 14, 0),
('form_search_product_label', 1, 'Rechercher un produit', 'cible', '', 0, 14, 0),
('form_search_product_label', 2, 'Search for a product', 'cible', '', 0, 14, 0)
;