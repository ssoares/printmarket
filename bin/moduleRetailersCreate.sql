--
-- Table structure for table `Catalog_RetailersData`
--

DROP TABLE IF EXISTS `RetailersData`;
CREATE TABLE IF NOT EXISTS `RetailersData` (
  `R_RetailerProfileId` int(11) NOT NULL auto_increment,
  `R_GenericProfileId` int(11) NOT NULL,
  `R_RetailerAddressId` int(11) NOT NULL,
  `R_Status` int(1) default '1',
  `R_Active` tinyint(1) default '0',
  PRIMARY KEY (`R_RetailerProfileId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Données pour activer module et les liens dans le back end
--

REPLACE INTO Modules (M_ID, M_Title, M_MVCModuleTitle, M_UseProfile) VALUES (16, 'Retailers', 'retailers', 1);

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID) VALUES
('module_retailers', 1, 'Détaillants', 'cible', '', 0, 16),
('module_retailers', 2, 'Retailers', 'cible', '', 0, 16),
('retailers_module_name', 1, 'Détaillants', 'cible', '', 0, 16),
('retailers_module_name', 2, 'Retailers', 'cible', '', 0, 16),
('form_label_select_city', 1, 'S&eacute;lectionnez une ville', 'cible', '', 0, 16),
('form_label_select_city', 2, 'Choose a city', 'cible', '', 0, 16),
('form_label_select_state', 1, 'S&eacute;lectionnez une province', 'cible', '', 0, 16),
('form_label_select_state', 2, 'Choose a province', 'cible', '', 0, 16),
('locate_retailer_label', 1, 'Localiser un professionnel', 'client', '', 0, 16),
('locate_retailer_label', 2, 'Locate a professionnal', 'client', '', 0, 16),
('profile_title_retailer_web', 1, 'Détaillants: informations à afficher', 'cible', '', 0, 16),
('profile_title_retailer_web', 2, 'Retailers: Data to display', 'cible', '', 0, 16),
('form_label_Display_web', 1, 'Afficher sur le site', 'cible', '', 0, 16),
('form_label_Display_web', 2, 'Display on the website', 'cible', '', 0, 16);

INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('form_label_approved_onweb', 2, 'Approved  to display on the website', 'client', '', 0, 16);

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('form_label_approved_onweb', 1, 'Affichage sur le site approuvé', 'cible', '', 0, 16),
('form_label_approved_onweb', 2, 'Approved  to display on the website', 'cible', '', 0, 16),
('form_account_yes', 1, 'Oui', 'cible', '', 0, 16),
('form_account_yes', 2, 'Yes', 'cible', '', 0, 16),
('form_account_no', 1, 'Non', 'cible', '', 0, 16),
('form_account_no', 2, 'No', 'cible', '', 0, 16);
