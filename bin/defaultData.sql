-- phpMyAdmin SQL Dump
-- version 3.1.3
-- http://www.phpmyadmin.net
--
-- Host: 209.222.235.12:3306
-- Generation Time: Apr 21, 2010 at 10:12 AM
-- Server version: 5.0.70
-- PHP Version: 5.2.10-pl0-gentoo
-- Version SVN: $Id: defaultData.sql 1851 2016-01-15 22:56:34Z ssoares $

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Initialize default data for the new database
--

--
-- Structure de la table `AddressData`
--

CREATE TABLE IF NOT EXISTS `AddressData` (
  `A_AddressId` int(10) NOT NULL AUTO_INCREMENT,
  `A_Duplicate` int(10) DEFAULT NULL,
  `A_CityTextValue` varchar(50) DEFAULT NULL,
  `A_CityId` int(10) NOT NULL,
  `A_StateId` int(10) NOT NULL DEFAULT '1',
  `A_CountryId` int(10) DEFAULT '1',
  `A_ZipCode` varchar(50) NOT NULL,
  `A_Fax` varchar(255) DEFAULT '0',
  `A_Email` varchar(255) DEFAULT '0',
  `A_StateTextValue` varchar(100) DEFAULT NULL,
  `AI_FirstTel` varchar(255) DEFAULT NULL,
  `AI_FirstExt` varchar(255) DEFAULT NULL,
  `AI_SecondTel` varchar(255) DEFAULT NULL,
  `AI_SecondExt` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`A_AddressId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `AddressIndex`
--

CREATE TABLE IF NOT EXISTS `AddressIndex` (
  `AI_AddressId` int(10) NOT NULL,
  `AI_LanguageID` int(10) NOT NULL,
  `AI_Name` varchar(255) DEFAULT NULL,
  `AI_FirstName` varchar(255) DEFAULT NULL,
  `AI_FirstAddress` varchar(255) DEFAULT NULL,
  `AI_SecondAddress` varchar(255) DEFAULT NULL,
  `AI_WebSite` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`AI_AddressId`,`AI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `Blocks`
--

CREATE TABLE IF NOT EXISTS `Blocks` (
  `B_ID` int(10) NOT NULL AUTO_INCREMENT,
  `B_PageID` int(10) NOT NULL,
  `B_ModuleID` int(10) NOT NULL,
  `B_ZoneID` int(11) NOT NULL DEFAULT '1',
  `B_Position` int(10) DEFAULT '1',
  `B_ShowHeader` int(2) NOT NULL,
  `B_Draft` int(1) DEFAULT '0',
  `B_Online` int(1) DEFAULT '0',
  `B_Secured` tinyint(1) NOT NULL DEFAULT '0',
  `B_FromSite` varchar(45) DEFAULT NULL,
  `B_DuplicateId` int(11) DEFAULT NULL,
  `B_LastModified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`B_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=73 ;

-- --------------------------------------------------------

--
-- Structure de la table `BlocksIndex`
--

CREATE TABLE IF NOT EXISTS `BlocksIndex` (
  `BI_BlockID` int(11) NOT NULL,
  `BI_LanguageID` int(11) NOT NULL,
  `BI_BlockTitle` varchar(255) NOT NULL,
  PRIMARY KEY (`BI_BlockID`,`BI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `Categories`
--

CREATE TABLE IF NOT EXISTS `Categories` (
  `C_ID` int(11) NOT NULL AUTO_INCREMENT,
  `C_ParentID` int(11) NOT NULL,
  `C_ModuleID` int(11) NOT NULL,
  `C_PageID` int(11) DEFAULT NULL,
  `C_ShowInRss` tinyint(4) NOT NULL DEFAULT '0',
  `C_RssItemsCount` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`C_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=7004 ;

-- --------------------------------------------------------

--
-- Structure de la table `CategoriesIndex`
--

CREATE TABLE IF NOT EXISTS `CategoriesIndex` (
  `CI_CategoryID` int(11) NOT NULL,
  `CI_LanguageID` int(11) NOT NULL,
  `CI_Title` text NOT NULL,
  `CI_WordingShowAllRecords` text NOT NULL,
  `CI_ReturnToList` varchar(255) NOT NULL,
  UNIQUE KEY `CI_CategorieID` (`CI_CategoryID`,`CI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Cities`
--

CREATE TABLE IF NOT EXISTS `Cities` (
  `C_ID` int(11) NOT NULL,
  `C_StateID` int(11) NOT NULL,
  `C_Name` varchar(255) NOT NULL,
  PRIMARY KEY (`C_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Countries`
--

CREATE TABLE IF NOT EXISTS `Countries` (
  `C_ID` int(11) NOT NULL,
  `C_Identifier` varchar(2) NOT NULL,
  PRIMARY KEY (`C_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `CountriesIndex`
--

CREATE TABLE IF NOT EXISTS `CountriesIndex` (
  `CI_CountryID` int(11) NOT NULL,
  `CI_LanguageID` int(11) NOT NULL,
  `CI_Name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`CI_CountryID`,`CI_LanguageID`),
  KEY `FK_CountryIndex_Country` (`CI_CountryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Extranet_Groups`
--

CREATE TABLE IF NOT EXISTS `Extranet_Groups` (
  `EG_ID` int(11) NOT NULL AUTO_INCREMENT,
  `EG_Status` enum('inactive','active') NOT NULL,
  PRIMARY KEY (`EG_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `Extranet_GroupsIndex`
--

CREATE TABLE IF NOT EXISTS `Extranet_GroupsIndex` (
  `EGI_GroupID` int(11) NOT NULL,
  `EGI_LanguageID` int(11) NOT NULL,
  `EGI_Name` varchar(255) NOT NULL,
  `EGI_Description` longtext NOT NULL,
  PRIMARY KEY (`EGI_GroupID`,`EGI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Extranet_Groups_Pages_Permissions`
--

CREATE TABLE IF NOT EXISTS `Extranet_Groups_Pages_Permissions` (
  `EGPP_GroupID` int(11) NOT NULL,
  `EGPP_PageID` int(11) NOT NULL,
  `EGPP_Structure` enum('Y','N') NOT NULL DEFAULT 'N',
  `EGPP_Data` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`EGPP_GroupID`,`EGPP_PageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Extranet_Groups_RolesResources`
--

CREATE TABLE IF NOT EXISTS `Extranet_Groups_RolesResources` (
  `EGRRP_GroupID` int(11) NOT NULL,
  `EGRRP_RoleResourceID` int(11) NOT NULL,
  `EGRRP_Access` enum('allow','deny') NOT NULL,
  PRIMARY KEY (`EGRRP_GroupID`,`EGRRP_RoleResourceID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Extranet_Permissions`
--

CREATE TABLE IF NOT EXISTS `Extranet_Permissions` (
  `EP_ID` int(11) NOT NULL AUTO_INCREMENT,
  `EP_ControlName` varchar(255) NOT NULL,
  PRIMARY KEY (`EP_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `Extranet_Resources`
--

CREATE TABLE IF NOT EXISTS `Extranet_Resources` (
  `ER_ID` int(11) NOT NULL AUTO_INCREMENT,
  `ER_ControlName` varchar(255) NOT NULL,
  PRIMARY KEY (`ER_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000 ;

-- --------------------------------------------------------

--
-- Structure de la table `Extranet_ResourcesIndex`
--

CREATE TABLE IF NOT EXISTS `Extranet_ResourcesIndex` (
  `ERI_ResourceID` int(11) NOT NULL,
  `ERI_LanguageID` int(11) NOT NULL,
  `ERI_Name` varchar(255) NOT NULL,
  PRIMARY KEY (`ERI_ResourceID`,`ERI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Extranet_Roles`
--

CREATE TABLE IF NOT EXISTS `Extranet_Roles` (
  `ER_ID` int(11) NOT NULL AUTO_INCREMENT,
  `ER_ControlName` varchar(255) NOT NULL,
  PRIMARY KEY (`ER_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Structure de la table `Extranet_RolesResources`
--

CREATE TABLE IF NOT EXISTS `Extranet_RolesResources` (
  `ERR_ID` int(11) NOT NULL AUTO_INCREMENT,
  `ERR_RoleID` int(11) NOT NULL,
  `ERR_ResourceID` int(11) NOT NULL,
  `ERR_InheritedParentID` int(11) NOT NULL,
  PRIMARY KEY (`ERR_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=999002 ;

-- --------------------------------------------------------

--
-- Structure de la table `Extranet_RolesResourcesIndex`
--

CREATE TABLE IF NOT EXISTS `Extranet_RolesResourcesIndex` (
  `ERRI_RoleResourceID` int(11) NOT NULL,
  `ERRI_LanguageID` int(11) NOT NULL,
  `ERRI_Name` varchar(255) NOT NULL,
  `ERRI_Description` text NOT NULL,
  PRIMARY KEY (`ERRI_RoleResourceID`,`ERRI_LanguageID`),
  UNIQUE KEY `ERRI_RoleResourceID` (`ERRI_RoleResourceID`,`ERRI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Extranet_RolesResourcesPermissions`
--

CREATE TABLE IF NOT EXISTS `Extranet_RolesResourcesPermissions` (
  `ERRP_RoleResourceID` int(11) NOT NULL,
  `ERRP_PermissionID` int(11) NOT NULL,
  PRIMARY KEY (`ERRP_RoleResourceID`,`ERRP_PermissionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Extranet_Users`
--

CREATE TABLE IF NOT EXISTS `Extranet_Users` (
  `EU_ID` int(11) NOT NULL AUTO_INCREMENT,
  `EU_LName` varchar(50) NOT NULL,
  `EU_FName` varchar(50) NOT NULL,
  `EU_Email` varchar(255) DEFAULT NULL,
  `EU_Username` varchar(50) NOT NULL,
  `EU_Password` varchar(40) NOT NULL,
  `EU_lastAccess` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EU_ShowError` tinyint(2) NOT NULL DEFAULT '1',
  `EU_DefaultSite` varchar(45) NOT NULL,
  `EU_SiteAccess` varchar(255) NOT NULL,
  `EU_Disabled` varchar(45) DEFAULT '0',
  `EU_CreaDate` timestamp NULL DEFAULT NULL,
  `EU_ModifDate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`EU_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1000 ;

-- --------------------------------------------------------

--
-- Structure de la table `Extranet_UsersGroups`
--

CREATE TABLE IF NOT EXISTS `Extranet_UsersGroups` (
  `EUG_UserID` int(11) NOT NULL,
  `EUG_GroupID` int(11) NOT NULL,
  PRIMARY KEY (`EUG_UserID`,`EUG_GroupID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `FilesImport`
--

CREATE TABLE IF NOT EXISTS `FilesImport` (
  `FI_ID` varchar(255) NOT NULL,
  `FI_Type` varchar(255) DEFAULT NULL,
  `FI_FileName` varchar(255) NOT NULL,
  `FI_LastModif` datetime NOT NULL,
  `FI_LastAccess` datetime NOT NULL,
  PRIMARY KEY (`FI_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `GenericProfiles`
--

CREATE TABLE IF NOT EXISTS `GenericProfiles` (
  `GP_MemberID` int(11) NOT NULL AUTO_INCREMENT,
  `GP_Salutation` int(2) NOT NULL COMMENT 'elem:select|src:salutations',
  `GP_FirstName` varchar(255) NOT NULL DEFAULT '',
  `GP_LastName` varchar(255) NOT NULL DEFAULT '',
  `GP_Email` varchar(255) NOT NULL DEFAULT '' COMMENT 'validate:email|unique:true',
  `GP_Password` varchar(50) DEFAULT NULL,
  `GP_Language` int(2) NOT NULL DEFAULT '1' COMMENT 'elem:select|src:languages',
  `GP_CreaDate` datetime DEFAULT NULL,
  `GP_ModifDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `GP_Deleted` tinyint(1) DEFAULT '0' COMMENT 'exclude:true',
  `GP_Hash` varchar(50) CHARACTER SET big5 DEFAULT NULL,
  `GP_Status` tinyint(1) DEFAULT '2',
  PRIMARY KEY (`GP_MemberID`),
  KEY `Email` (`GP_Email`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `Languages`
--

CREATE TABLE IF NOT EXISTS `Languages` (
  `L_ID` int(10) NOT NULL AUTO_INCREMENT,
  `L_Suffix` char(2) NOT NULL,
  `L_Title` varchar(255) NOT NULL,
  `L_ExtranetUI` tinyint(1) DEFAULT '0',
  `L_Active` tinyint(1) DEFAULT '0',
  `L_Seq` int(2) DEFAULT '0',
  `L_Local` varchar(10) NOT NULL,
  PRIMARY KEY (`L_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `Layouts`
--

CREATE TABLE IF NOT EXISTS `Layouts` (
  `L_ID` int(11) NOT NULL AUTO_INCREMENT,
  `L_Name` varchar(255) NOT NULL,
  `L_Path` varchar(255) NOT NULL,
  `L_Image` varchar(255) NOT NULL,
  `L_SiteType` enum('s','m') NOT NULL DEFAULT 's',
  PRIMARY KEY (`L_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Structure de la table `Log`
--

CREATE TABLE IF NOT EXISTS `Log` (
  `L_ID` int(11) NOT NULL AUTO_INCREMENT,
  `L_ModuleID` int(11) DEFAULT NULL,
  `L_Action` varchar(45) DEFAULT NULL,
  `L_UserID` int(11) DEFAULT NULL,
  `L_Datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `L_Data` text,
  PRIMARY KEY (`L_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Structure de la table `MemberProfiles`
--

CREATE TABLE IF NOT EXISTS `MemberProfiles` (
  `MP_GenericProfileId` int(11) NOT NULL,
  `MP_CompanyName` varchar(30) DEFAULT NULL,
  `MP_AddressId` int(11) NOT NULL,
  `MP_ValidateEmail` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`MP_GenericProfileId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `MenuItemData`
--

CREATE TABLE IF NOT EXISTS `MenuItemData` (
  `MID_ID` int(11) NOT NULL AUTO_INCREMENT,
  `MID_MenuID` int(11) NOT NULL,
  `MID_ParentID` int(11) NOT NULL DEFAULT '0',
  `MID_Position` int(11) NOT NULL,
  `MID_Style` varchar(50) DEFAULT NULL,
  `MID_Secured` tinyint(1) DEFAULT '0',
  `MID_loadImage` tinyint(1) NOT NULL DEFAULT '0',
  `MID_Image` varchar(255) DEFAULT NULL,
  `MID_ImgAndTitle` tinyint(1) NOT NULL DEFAULT '0',
  `MID_Show` tinyint(1) NOT NULL DEFAULT '1',
  `MID_Show_Sitemap` tinyint(1) NOT NULL DEFAULT '1',
  `MID_FontIcon` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`MID_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=77 ;

-- --------------------------------------------------------

--
-- Structure de la table `MenuItemIndex`
--

CREATE TABLE IF NOT EXISTS `MenuItemIndex` (
  `MII_ID` int(11) NOT NULL AUTO_INCREMENT,
  `MII_LanguageID` int(11) NOT NULL,
  `MII_MenuItemDataID` int(11) NOT NULL,
  `MII_Title` varchar(255) NOT NULL,
  `MII_Link` varchar(255) NOT NULL,
  `MII_PageID` int(11) NOT NULL DEFAULT '-1',
  `MII_Placeholder` int(11) DEFAULT '0',
  PRIMARY KEY (`MII_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=120 ;

-- --------------------------------------------------------

--
-- Structure de la table `Menus`
--

CREATE TABLE IF NOT EXISTS `Menus` (
  `M_ID` int(11) NOT NULL AUTO_INCREMENT,
  `M_Title` varchar(50) NOT NULL,
  `M_MenuType` int(11) NOT NULL DEFAULT '1',
  `M_FirstLevelNumberOfElements` int(11) DEFAULT '-1',
  `M_MaxNesting` int(11) DEFAULT '-1',
  `M_BgColor` varchar(7) NOT NULL DEFAULT '#F0F0F0',
  `M_Section` varchar(45) NOT NULL DEFAULT 'main',
  `M_Seq` int(3) DEFAULT NULL,
  `M_ShowSitemap` tinyint(1) NOT NULL DEFAULT '1',
  `M_SiteType` enum('s','m') NOT NULL DEFAULT 's',
  PRIMARY KEY (`M_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=81 ;

-- --------------------------------------------------------

--
-- Structure de la table `MenuTypes`
--

CREATE TABLE IF NOT EXISTS `MenuTypes` (
  `MT_ID` int(11) NOT NULL AUTO_INCREMENT,
  `MT_Title` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`MT_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `ModuleCategoryViewPage`
--

CREATE TABLE IF NOT EXISTS `ModuleCategoryViewPage` (
  `MCVP_ID` int(11) NOT NULL AUTO_INCREMENT,
  `MCVP_ModuleID` int(11) NOT NULL,
  `MCVP_CategoryID` int(11) NOT NULL DEFAULT '0',
  `MCVP_ViewID` int(11) NOT NULL,
  `MCVP_PageID` int(11) NOT NULL,
  PRIMARY KEY (`MCVP_ID`,`MCVP_ModuleID`,`MCVP_CategoryID`,`MCVP_ViewID`,`MCVP_PageID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20053 ;

-- --------------------------------------------------------

--
-- Structure de la table `Modules`
--

CREATE TABLE IF NOT EXISTS `Modules` (
  `M_ID` int(10) NOT NULL,
  `M_Title` varchar(255) NOT NULL,
  `M_MVCModuleTitle` varchar(255) NOT NULL,
  `M_UseProfile` tinyint(1) NOT NULL DEFAULT '0',
  `M_NeedAuth` tinyint(1) NOT NULL DEFAULT '0',
  `M_HasFrontEnd` tinyint(1) NOT NULL DEFAULT '1',
  `M_SiteType` varchar(255) NOT NULL DEFAULT 's',
  `M_Indexation` varchar(75) DEFAULT '',
  `M_Seq` int(3) DEFAULT '2',
  PRIMARY KEY (`M_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `Modules_ControllersActionsPermissions`
--

CREATE TABLE IF NOT EXISTS `Modules_ControllersActionsPermissions` (
  `MCAP_ID` int(11) NOT NULL AUTO_INCREMENT,
  `MCAP_ModuleID` int(11) NOT NULL,
  `MCAP_ControllerTitle` varchar(255) NOT NULL,
  `MCAP_ControllerActionTitle` varchar(255) NOT NULL,
  `MCAP_PermissionTitle` varchar(255) NOT NULL,
  `MCAP_Position` int(11) DEFAULT NULL,
  PRIMARY KEY (`MCAP_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Structure de la table `ModuleViews`
--

CREATE TABLE IF NOT EXISTS `ModuleViews` (
  `MV_ID` int(11) NOT NULL AUTO_INCREMENT,
  `MV_Name` varchar(50) DEFAULT NULL,
  `MV_ModuleID` int(11) DEFAULT NULL,
  PRIMARY KEY (`MV_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24005 ;

-- --------------------------------------------------------

--
-- Structure de la table `ModuleViewsIndex`
--

CREATE TABLE IF NOT EXISTS `ModuleViewsIndex` (
  `MVI_ModuleViewsID` int(11) NOT NULL,
  `MVI_LanguageID` int(11) NOT NULL,
  `MVI_ActionName` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`MVI_ModuleViewsID`,`MVI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `NotificationManagerData`
--

CREATE TABLE IF NOT EXISTS `NotificationManagerData` (
  `NM_ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'exclude:1',
  `NM_ModuleId` int(11) NOT NULL COMMENT 'elem:select|src:modules',
  `NM_Event` varchar(50) NOT NULL,
  `NM_Type` enum('email','screen') DEFAULT 'email' COMMENT 'elem:enum',
  `NM_Recipient` enum('client','admin') DEFAULT 'client' COMMENT 'elem:enum',
  `NM_Active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'elem:checkbox',
  `NM_Message` varchar(255) NOT NULL COMMENT 'elem:select|src:salutations',
  `NM_Title` varchar(255) NOT NULL,
  `NM_Email` varchar(255) NOT NULL DEFAULT 'empty' COMMENT 'validate:email|unique:true',
  `NM_ModifDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'exclude:1',
  PRIMARY KEY (`NM_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Structure de la table `Pages`
--

CREATE TABLE IF NOT EXISTS `Pages` (
  `P_ID` int(10) NOT NULL AUTO_INCREMENT,
  `P_Position` int(10) NOT NULL,
  `P_ParentID` int(10) NOT NULL,
  `P_Home` int(1) NOT NULL,
  `P_HomeMobile` int(1) NOT NULL,
  `P_LayoutID` int(11) DEFAULT NULL,
  `P_ThemeID` int(11) NOT NULL DEFAULT '1',
  `P_ViewID` int(11) NOT NULL DEFAULT '1',
  `P_ShowSiteMap` int(1) DEFAULT '1',
  `P_ShowMenu` int(1) DEFAULT '1',
  `P_ShowTitle` tinyint(4) DEFAULT '1',
  `P_BannerGroupID` int(11) DEFAULT NULL,
  `P_SiteType` enum('s','m') NOT NULL DEFAULT 's',
  `P_Indexation` tinyint(4) DEFAULT '1',
  `P_FromSite` varchar(45) DEFAULT NULL,
  `P_DuplicateId` int(11) DEFAULT NULL,
  PRIMARY KEY (`P_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=24020 ;

-- --------------------------------------------------------

--
-- Structure de la table `PagesIndex`
--

CREATE TABLE IF NOT EXISTS `PagesIndex` (
  `PI_PageID` int(10) NOT NULL,
  `PI_LanguageID` int(10) NOT NULL,
  `PI_PageIndex` varchar(255) NOT NULL,
  `PI_PageIndexOtherLink` varchar(255) NOT NULL,
  `PI_PageTitle` varchar(255) NOT NULL,
  `PI_TitleImageSrc` varchar(255) DEFAULT NULL,
  `PI_TitleImageAlt` varchar(255) DEFAULT NULL,
  `PI_MetaDescription` text NOT NULL,
  `PI_MetaKeywords` text NOT NULL,
  `PI_MetaTitle` text NOT NULL,
  `PI_MetaOther` text NOT NULL,
  `PI_Status` tinyint(4) NOT NULL,
  `PI_Secure` enum('non','oui') NOT NULL,
  `PI_AltPremiereImage` varchar(255) DEFAULT NULL,
  `PI_ImageBackground` varchar(255) DEFAULT NULL,
  `PI_CanonicalLink` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`PI_PageID`,`PI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `Page_Themes`
--

CREATE TABLE IF NOT EXISTS `Page_Themes` (
  `PT_ID` int(11) NOT NULL AUTO_INCREMENT,
  `PT_Name` varchar(50) DEFAULT NULL,
  `PT_Folder` varchar(255) DEFAULT NULL,
  `PT_SiteType` enum('s','m') NOT NULL DEFAULT 's',
  PRIMARY KEY (`PT_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `Parameters`
--

CREATE TABLE IF NOT EXISTS `Parameters` (
  `P_BlockID` int(11) NOT NULL,
  `P_Number` int(11) NOT NULL,
  `P_Value` text NOT NULL,
  UNIQUE KEY `P_BlockID` (`P_BlockID`,`P_Number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `References`
--

CREATE TABLE IF NOT EXISTS `References` (
  `R_ID` int(11) NOT NULL AUTO_INCREMENT,
  `R_TypeRef` ENUM('typeClient','productsKeywords') NOT NULL,
  `R_Seq` int(11) NOT NULL,
  PRIMARY KEY (`R_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Structure de la table `ReferencesIndex`
--

CREATE TABLE IF NOT EXISTS `ReferencesIndex` (
  `RI_RefId` int(11) NOT NULL,
  `RI_LanguageID` int(2) NOT NULL,
  `RI_Value` varchar(50) DEFAULT NULL,
  `RI_Seq` int(11) DEFAULT NULL,
  PRIMARY KEY (`RI_RefId`,`RI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `References_ListUsed`
--

CREATE TABLE IF NOT EXISTS `References_ListUsed` (
  `RLU_List` varchar(100) NOT NULL,
  `RLU_Table` varchar(100) NOT NULL,
  `RLU_Column` varchar(100) NOT NULL,
  PRIMARY KEY (`RLU_List`,`RLU_Table`,`RLU_Column`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Salutations`
--

CREATE TABLE IF NOT EXISTS `Salutations` (
  `S_ID` int(11) NOT NULL AUTO_INCREMENT,
  `S_StaticTitle` varchar(255) NOT NULL,
  PRIMARY KEY (`S_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `States`
--

CREATE TABLE IF NOT EXISTS `States` (
  `S_ID` int(11) NOT NULL,
  `S_CountryID` int(11) NOT NULL,
  `S_Identifier` varchar(2) NOT NULL,
  PRIMARY KEY (`S_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `StatesIndex`
--

CREATE TABLE IF NOT EXISTS `StatesIndex` (
  `SI_StateID` int(11) NOT NULL,
  `SI_LanguageID` int(11) NOT NULL,
  `SI_Name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`SI_StateID`,`SI_LanguageID`),
  KEY `FK_StateIndex_State1` (`SI_StateID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Static_Texts`
--

CREATE TABLE IF NOT EXISTS `Static_Texts` (
  `ST_Identifier` varchar(100) NOT NULL,
  `ST_LangID` int(11) NOT NULL DEFAULT '1',
  `ST_Value` text,
  `ST_Type` enum('cible','client') DEFAULT 'cible',
  `ST_Desc_backend` text NOT NULL,
  `ST_Editable` tinyint(4) NOT NULL DEFAULT '0',
  `ST_ModuleID` int(5) NOT NULL DEFAULT '0',
  `ST_ModifDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ST_RichText` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ST_Identifier`,`ST_LangID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `Status`
--

CREATE TABLE IF NOT EXISTS `Status` (
  `S_ID` int(11) NOT NULL AUTO_INCREMENT,
  `S_Code` varchar(55) DEFAULT NULL,
  PRIMARY KEY (`S_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `TextData`
--

CREATE TABLE IF NOT EXISTS `TextData` (
  `TD_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `TD_BlockID` int(10) NOT NULL,
  `TD_LanguageID` int(10) NOT NULL,
  `TD_OnlineTitle` varchar(255) DEFAULT NULL,
  `TD_OnlineText` text,
  `TD_DraftTitle` varchar(255) DEFAULT NULL,
  `TD_DraftText` text,
  `TD_ToApprove` int(11) NOT NULL,
  PRIMARY KEY (`TD_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=44 ;

-- --------------------------------------------------------

--
-- Structure de la table `Views`
--

CREATE TABLE IF NOT EXISTS `Views` (
  `V_ID` int(11) NOT NULL AUTO_INCREMENT,
  `V_Name` varchar(255) NOT NULL,
  `V_ZoneCount` int(11) NOT NULL DEFAULT '1',
  `V_Path` varchar(255) NOT NULL,
  `V_Image` varchar(255) NOT NULL,
  `V_SiteType` enum('s','m') NOT NULL DEFAULT 's',
  PRIMARY KEY (`V_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=7 ;


--
-- Dumping data for table `States`
--

INSERT INTO `States` (`S_ID`, `S_CountryID`, `S_Identifier`) VALUES
(1, 7, ''),
(2, 7, ''),
(3, 7, ''),
(4, 7, ''),
(5, 7, ''),
(6, 7, ''),
(7, 7, ''),
(8, 7, ''),
(9, 7, ''),
(10, 7, ''),
(11, 7, ''),
(12, 7, ''),
(13, 7, ''),
(14, 52, ''),
(15, 52, ''),
(16, 52, ''),
(17, 52, ''),
(18, 52, ''),
(19, 52, ''),
(20, 52, ''),
(21, 52, ''),
(22, 52, ''),
(23, 52, ''),
(24, 52, ''),
(25, 52, ''),
(26, 52, ''),
(27, 52, ''),
(28, 52, ''),
(29, 52, ''),
(30, 52, ''),
(31, 52, ''),
(32, 52, ''),
(33, 52, ''),
(34, 52, ''),
(35, 52, ''),
(36, 52, ''),
(37, 52, ''),
(38, 52, ''),
(39, 52, ''),
(40, 52, ''),
(41, 52, ''),
(42, 52, ''),
(43, 52, ''),
(44, 52, ''),
(45, 52, ''),
(46, 52, ''),
(47, 52, ''),
(48, 52, ''),
(49, 52, ''),
(50, 52, ''),
(51, 52, ''),
(52, 52, ''),
(53, 52, ''),
(54, 52, ''),
(55, 52, ''),
(56, 52, ''),
(57, 52, ''),
(58, 52, ''),
(59, 52, ''),
(60, 52, ''),
(61, 52, ''),
(62, 52, ''),
(63, 52, ''),
(64, 52, '');

-- --------------------------------------------------------

--
-- Dumping data for table `StatesIndex`
--

INSERT INTO `StatesIndex` (`SI_StateID`, `SI_LanguageID`, `SI_Name`) VALUES
(1, 1, 'Alberta'),
(1, 2, 'Alberta'),
(2, 1, 'Colombie-Britannique'),
(2, 2, 'British Columbia'),
(3, 1, 'Manitoba'),
(3, 2, 'Manitoba'),
(4, 1, 'Nouveau-Brunswick'),
(4, 2, 'New Brunswick'),
(5, 1, 'Terre-Neuve-et-Labrador'),
(5, 2, 'Newfoundland & Labrador'),
(6, 1, 'Nouvelle-Écosse'),
(6, 2, 'Nova Scotia'),
(7, 1, 'Territoires du Nord-Ouest'),
(7, 2, 'Northwest Territories'),
(8, 1, 'Nunavut'),
(8, 2, 'Nunavut'),
(9, 1, 'Ontario'),
(9, 2, 'Ontario'),
(10, 1, 'île-du-Prince-Édouard'),
(10, 2, 'Prince Edward Island'),
(11, 1, 'Québec'),
(11, 2, 'Quebec'),
(12, 1, 'Saskatchewan'),
(12, 2, 'Saskatchewan'),
(13, 1, 'Yukon'),
(13, 2, 'Yukon'),
(14, 1, 'Alaska'),
(14, 2, 'Alaska'),
(15, 1, 'Alabama'),
(15, 2, 'Alabama'),
(16, 1, 'Arkansas'),
(16, 2, 'Arkansas'),
(17, 1, 'Arizona'),
(17, 2, 'Arizona'),
(18, 1, 'Californie'),
(18, 2, 'California'),
(19, 1, 'Colorado'),
(19, 2, 'Colorado'),
(20, 1, 'Connecticut'),
(20, 2, 'Connecticut'),
(21, 1, 'District de Columbia'),
(21, 2, 'District of Columbia'),
(22, 1, 'Delaware'),
(22, 2, 'Delaware'),
(23, 1, 'Floride'),
(23, 2, 'Florida'),
(24, 1, 'Géorgie'),
(24, 2, 'Georgia'),
(25, 1, 'Hawaii'),
(25, 2, 'Hawaii'),
(26, 1, 'Iowa'),
(26, 2, 'Iowa'),
(27, 1, 'Idaho'),
(27, 2, 'Idaho'),
(28, 1, 'Illinois'),
(28, 2, 'Illinois'),
(29, 1, 'Indiana'),
(29, 2, 'Indiana'),
(30, 1, 'Kansas'),
(30, 2, 'Kansas'),
(31, 1, 'Kentucky'),
(31, 2, 'Kentucky'),
(32, 1, 'Louisiane'),
(32, 2, 'Louisiana'),
(33, 1, 'Massachusetts'),
(33, 2, 'Massachusetts'),
(34, 1, 'Maryland'),
(34, 2, 'Maryland'),
(35, 1, 'Maine'),
(35, 2, 'Maine'),
(36, 1, 'Michigan'),
(36, 2, 'Michigan'),
(37, 1, 'Minnesota'),
(37, 2, 'Minnesota'),
(38, 1, 'Missouri'),
(38, 2, 'Missouri'),
(39, 1, 'Mississippi'),
(39, 2, 'Mississippi'),
(40, 1, 'Montana'),
(40, 2, 'Montana'),
(41, 1, 'Caroline du Nord'),
(41, 2, 'North Carolina'),
(42, 1, 'Dakota du Nord'),
(42, 2, 'North Dakota'),
(43, 1, 'Nebraska'),
(43, 2, 'Nebraska'),
(44, 1, 'New Hampshire'),
(44, 2, 'New Hampshire'),
(45, 1, 'New Jersey'),
(45, 2, 'New Jersey'),
(46, 1, 'Nouveau-Mexique'),
(46, 2, 'New Mexico'),
(47, 1, 'Nevada'),
(47, 2, 'Nevada'),
(48, 1, 'New York'),
(48, 2, 'New York'),
(49, 1, 'Ohio'),
(49, 2, 'Ohio'),
(50, 1, 'Oklahoma'),
(50, 2, 'Oklahoma'),
(51, 1, 'Oregon'),
(51, 2, 'Oregon'),
(52, 1, 'Pennsylvanie'),
(52, 2, 'Pennsylvania'),
(53, 1, 'Rhode Island'),
(53, 2, 'Rhode Island'),
(54, 1, 'Caroline du Sud'),
(54, 2, 'South Carolina'),
(55, 1, 'Dakota du Sud'),
(55, 2, 'South Dakota'),
(56, 1, 'Tennessee'),
(56, 2, 'Tennessee'),
(57, 1, 'Texas'),
(57, 2, 'Texas'),
(58, 1, 'Utah'),
(58, 2, 'Utah'),
(59, 1, 'Virginie'),
(59, 2, 'Virginia'),
(60, 1, 'Vermont'),
(60, 2, 'Vermont'),
(61, 1, 'Washington'),
(61, 2, 'Washington'),
(62, 1, 'Wisconsin'),
(62, 2, 'Wisconsin'),
(63, 1, 'Virginie-Occidentale'),
(63, 2, 'West Virginia'),
(64, 1, 'Wyoming'),
(64, 2, 'Wyoming');

-- --------------------------------------------------------

--
-- Contenu de la table `References`
--

INSERT INTO `References` (`R_ID`, `R_TypeRef`, `R_Seq`) VALUES
(5, 'subscrArg', 101),
(4, 'subscrArg', 201),
(3, 'subscrArg', 1),
(6, 'unsubscrArg', 1),
(7, 'unsubscrArg', 2),
(8, 'typeClient', 10),
(9, 'typeClient', 20),
(10, 'typeClient', 30);

-- --------------------------------------------------------

--
-- Contenu de la table `ReferencesIndex`
--

INSERT INTO `ReferencesIndex` (`RI_RefId`, `RI_LanguageID`, `RI_Value`, `RI_Seq`) VALUES
(3, 1, 'Sur un site spécialisé', 1),
(3, 2, 'On a specialized site', 1),
(3, 3, 'Acerca de un sitio especializado', 1),
(4, 1, 'Dans la presse', 2),
(4, 2, 'In the media', 2),
(4, 3, 'En la prensa', 2),
(5, 1, 'Par un fournisseur', 3),
(5, 2, 'By a supplier', 3),
(5, 3, 'A través de un proveedor', 3),
(6, 1, 'Pas de contenu pertinent', 1),
(6, 2, 'No relevant content', 1),
(6, 3, 'Sin contenido pertinente', 1),
(7, 1, 'Trop d''envois par mois', 2),
(7, 2, 'Too many sending per month', 2),
(7, 3, 'Demasiados envois por mes', 2),
(8, 1, 'Prospect', 10),
(8, 2, 'Prospect', 10),
(8, 3, 'Cliente potential', 10),
(9, 1, 'Client', 20),
(9, 2, 'Customer', 20),
(9, 3, 'Cliente', 20),
(10, 1, 'Abonné', 30),
(10, 2, 'Subscriber', 30),
(10, 3, 'Suscriptor', 30);
-- --------------------------------------------------------

--
-- Dumping data for table `Extranet_Groups`
--

INSERT INTO `Extranet_Groups` (`EG_ID`, `EG_Status`) VALUES
(1, 'active'),
(2, 'active'),
(3, 'active');

-- --------------------------------------------------------


--
-- Dumping data for table `Extranet_GroupsIndex`
--

INSERT INTO `Extranet_GroupsIndex` (`EGI_GroupID`, `EGI_LanguageID`, `EGI_Name`, `EGI_Description`) VALUES
(1, 1, 'Super usager', 'Cet usager a tous les droits.'),
(1, 2, 'Super user', 'This user has all permission.'),
(2, 1, 'Administrateurs', "Ce groupe donne accès à tout ce qui est contrôlable dans l\'extranet. De la structure du site Web à la gestion des autres administrateurs."),
(2, 2, 'Administrators', 'This group provides access to all that is controllable in the extranet. The structure of the website to the management of other directors.'),
(3, 1, 'Rédacteurs', 'Ce groupe permet de créer, éditer et valider les données.'),
(3, 2, 'Redactors', 'This group allows to create, edit and validate data.');

-- --------------------------------------------------------

--
-- Table structure for table `Extranet_Groups_RolesResources`
--

CREATE TABLE IF NOT EXISTS `Extranet_Groups_RolesResources` (
  `EGRRP_GroupID` int(11) NOT NULL,
  `EGRRP_RoleResourceID` int(11) NOT NULL,
  `EGRRP_Access` enum('allow','deny') NOT NULL,
  PRIMARY KEY  (`EGRRP_GroupID`,`EGRRP_RoleResourceID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Extranet_Permissions`
--

INSERT INTO `Extranet_Permissions` (`EP_ID`, `EP_ControlName`) VALUES
(1, 'edit'),
(2, 'submit'),
(3, 'publish'),
(4, 'manage');

-- --------------------------------------------------------

--
-- Dumping data for table `Extranet_Resources`
--

INSERT INTO `Extranet_Resources` (`ER_ID`, `ER_ControlName`) VALUES
(1, 'text');

-- --------------------------------------------------------

--
-- Dumping data for table `Extranet_ResourcesIndex`
--

INSERT INTO `Extranet_ResourcesIndex` (`ERI_ResourceID`, `ERI_LanguageID`, `ERI_Name`) VALUES
(1, 1, 'Textes'),
(1, 2, 'Texts');

-- --------------------------------------------------------

--
-- Dumping data for table `Extranet_Roles`
--

INSERT INTO `Extranet_Roles` (`ER_ID`, `ER_ControlName`) VALUES
(1, 'editor'),
(2, 'reviser'),
(3, 'manager');

-- --------------------------------------------------------

--
-- Dumping data for table `Extranet_RolesResources`
--

INSERT INTO `Extranet_RolesResources` (`ERR_ID`, `ERR_RoleID`, `ERR_ResourceID`, `ERR_InheritedParentID`) VALUES
(1, 1, 1, 0),
(2, 2, 1, 1),
(3, 3, 1, 2);

-- --------------------------------------------------------

--
-- Dumping data for table `Extranet_RolesResourcesIndex`
--

INSERT INTO `Extranet_RolesResourcesIndex` (`ERRI_RoleResourceID`, `ERRI_LanguageID`, `ERRI_Name`, `ERRI_Description`) VALUES
(1, 1, 'Rédacteur de texte', 'Le rédacteur peut modifier tous les textes du site Web, mais ne peut pas les mettre en ligne'),
(2, 1, 'Réviseur de texte', 'Le réviseur de texte peut modifier tous les textes du site Web et aussi les mettre en ligne'),
(1, 2, 'Drafters', 'The editor can edit all texts from the website but can not put them online'),
(2, 2, 'Text editor', 'The text editor can edit all texts of this website and also put online');

-- --------------------------------------------------------

--
-- Dumping data for table `Extranet_RolesResourcesPermissions`
--

INSERT INTO `Extranet_RolesResourcesPermissions` (`ERRP_RoleResourceID`, `ERRP_PermissionID`) VALUES
(1, 1),
(1, 2),
(2, 3);

-- --------------------------------------------------------

--
-- Contenu de la table `Languages`
--

INSERT INTO `Languages` (`L_ID`, `L_Suffix`, `L_Title`, `L_ExtranetUI`, `L_Active`, `L_Seq`, `L_Local`) VALUES
(1, 'fr', 'Français', 1, 1, 1, 'fr_CA'),
(2, 'en', 'English', 0, 1, 2, 'en_CA'),
(3, 'es', 'Espa&ntilde;ol', 0, 0, 3, 'es_ES'),
(4, 'it', 'Italiano', 0, 0, 4, 'it_IT');


-- --------------------------------------------------------

--
-- Dumping data for table `Layouts`
--

INSERT INTO `Layouts` (`L_ID`, `L_Name`, `L_Path`, `L_Image`, `L_SiteType`) VALUES
-- (1, 'mainHome', 'mainHome.phtml', 'image.png', 's'),
(2, 'mainCommon', 'mainCommon.phtml', 'image.png', 's'),
(3 , 'mainMobile', 'mainMobile.phtml', '', 'm');

-- --------------------------------------------------------

--
-- Dumping data for table `Menus`
--

INSERT INTO `Menus` (`M_ID`, `M_Title`, `M_MenuType`, `M_FirstLevelNumberOfElements`, `M_MaxNesting`,`M_Seq`) VALUES
(10, 'principal', 1, 5, 3, 1),
(20, 'header', 2, -1, -1, 2),
(30, 'permanent', 2, 2, 2, 3),
(40, 'footer', 2, -1, -1, 4),
(50, 'reseaux', 2, -1, -1, 5),
(60, 'copyright', 2, -1, -1, 6);

INSERT INTO `Menus` (`M_ID`, `M_Title`, `M_MenuType`, `M_FirstLevelNumberOfElements`, `M_MaxNesting`, `M_Section`, `M_SiteType`, `M_Seq`) VALUES
(80, 'mobile', 1, 5, 3, 'mobile', 'm', 1);

-- --------------------------------------------------------

--
-- Dumping data for table `MenuTypes`
--

INSERT INTO `MenuTypes` (`MT_ID`, `MT_Title`) VALUES
(1, 'Hierarchical'),
(2, 'Simple');

-- --------------------------------------------------------

--
-- Dumping data for table `Modules_ControllersActionsPermissions`
--

INSERT INTO `Modules_ControllersActionsPermissions` (`MCAP_ModuleID`, `MCAP_ControllerTitle`, `MCAP_ControllerActionTitle`, `MCAP_PermissionTitle`, `MCAP_Position`) VALUES
(1, 'index', 'list', 'edit', 1),
(1, 'index', 'list-approbation-request', 'publish', 2),
(999, 'manage', 'structure', 'edit', 1),
(999, 'manage', 'menu', 'edit', 2);

-- --------------------------------------------------------

INSERT INTO `ModuleCategoryViewPage` (`MCVP_ModuleID`, `MCVP_CategoryID`, `MCVP_ViewID`, `MCVP_PageID`) VALUES
(10, 0, 10001, 5);
-- --------------------------------------------------------

INSERT INTO Modules (M_ID, M_Title, M_MVCModuleTitle, M_HasFrontEnd, M_SiteType, M_Indexation, M_Seq) VALUES
(1, 'Text', 'text', 1, 's,m', 'Text', 2),
(5, 'Site map', 'sitemap', 1, 's','', 2),
(10, 'Search', 'search', 1,'s','', 2),
(999, 'Pages', 'page', 0,'s,m', '', 1);

-- --------------------------------------------------------

--
-- Dumping data for table `Modules_ControllersActionsPermissions`
--

INSERT INTO ModuleViews (MV_ID, MV_Name, MV_ModuleID) VALUES
(10001, 'index', 10);

-- --------------------------------------------------------

--
-- Dumping data for table `Modules_ControllersActionsPermissions`
--

INSERT INTO ModuleViewsIndex (MVI_ModuleViewsID, MVI_LanguageID, MVI_ActionName) VALUES
(10001, 1, 'resultats'),
(10001, 2, 'results');

--
-- Dumping data for table `Pages`
--

INSERT INTO `Pages` (`P_ID`, `P_Position`, `P_ParentID`, `P_Home`, `P_LayoutID`, `P_ThemeID`, `P_ViewID`, `P_ShowSiteMap`, `P_ShowMenu`, `P_ShowTitle`) VALUES
(1, 1, 0, 1, 2, 1, 1, 1, 1, 1),
(404, 2, 0, 0, 2, 1, 2, 1, 1, 1),
(3, 3, 0, 0, 2, 1, 2, 1, 1, 1),
(4, 4, 0, 0, 2, 1, 2, 0, 1, 1),
(5, 5, 0, 0, 2, 1, 2, 0, 1, 1);

-- --------------------------------------------------------

--
-- Dumping data for table `Pages`
--

INSERT INTO `PagesIndex` (`PI_PageID`, `PI_LanguageID`, `PI_PageIndex`, `PI_PageIndexOtherLink`, `PI_PageTitle`, `PI_TitleImageSrc`, `PI_TitleImageAlt`, `PI_MetaTitle`, `PI_MetaDescription`, `PI_MetaKeywords`,`PI_MetaOther`, `PI_Status`, `PI_Secure`) VALUES
(1, 1, 'accueil', '', 'Accueil', '', '', '', '', '','', 1, 'non'),
(1, 2, 'home', '', 'Home', '', '', '', '', '','', 1, 'non'),
(404, 1, 'page-non-trouvee', '', 'Page non trouvée', '', '', '', 'page 404', '404','', 1, 'non'),
(404, 2, 'page-not-found', '', 'Page not found', '', '', '', 'page 404', '404','', 1, 'non'),
(3, 1, 'politique-de-confidentialite', '', 'Politique de confidentialité', '', '', '', '', '','', 1, 'non'),
(3, 2, 'privacy-policy', '', 'Privacy policy', '', '', '', '', '','', 1, 'non'),
(4, 1, 'plan-du-site', '', 'Plan du site', '', '', '', '', '','', 1, 'non'),
(4, 2, 'site-map', '', 'Sitemap', '', '', '', '', '','', 1, 'non'),
(5, 1, 'recherche', '', 'Recherche', '', '', '', '', '','', 1, 'non'),
(5, 2, 'search', '', 'Search', '', '', '', '', '','', 1, 'non');

-- --------------------------------------------------------

--
-- Dumping data for table `Page_Themes`
--

INSERT INTO `Page_Themes` (`PT_ID`, `PT_Name`, `PT_Folder`) VALUES
(1, 'Default', 'default');

-- --------------------------------------------------------

--
-- Dumping data for table `Salutations`
--

INSERT INTO `Salutations` (`S_ID`, `S_StaticTitle`) VALUES
(1, 'salutation_mr'),
(2, 'salutation_mrs');

-- --------------------------------------------------------

--
-- Dumping data for table `Static_Texts`
--

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('extranet', 1, 'Extranet', 'cible', '', 0, 0),
('form_title_login', 1, 'Identification', 'cible', '', 0, 0),
('OLD_login_username', 1, 'Nom d''usager', 'cible', '', 0, 0),
('form_label_password', 1, 'Mot de passe', 'cible', '', 0, 0),
('button_authenticate', 1, 'Connexion', 'cible', '', 0, 0),
('error_auth_failure', 1, 'Votre nom d''usager ou votre mot de passe sont incorrects.', 'cible', '', 0, 0),
('error_auth_failure', 2, 'Your user name and/or your password are incorrect.', 'cible', '', 0, 0),
('contact_salutation', 1, 'Salutations', 'cible', '', 0, 0),
('contact_madame', 1, 'Madame', 'cible', '', 0, 0),
('contact_monsieur', 1, 'Monsieur', 'cible', '', 0, 0),
('contact_fname', 1, 'Prénom', 'cible', '', 0, 0),
('contact_lname', 1, 'Nom', 'cible', '', 0, 0),
('contact_email', 1, 'Adresse courriel', 'cible', '', 0, 0),
('contact_address', 1, 'Adresse', 'cible', '', 0, 0),
('contact_province', 1, 'Province', 'cible', '', 0, 0),
('contact_country', 1, 'Pays', 'cible', '', 0, 0),
('contact_postalCode', 1, 'Code postal', 'cible', '', 0, 0),
('contact_phone', 1, 'Téléphone', 'cible', '', 0, 0),
('contact_fax', 1, 'Télécopieur', 'cible', '', 0, 0),
('contact_question', 1, 'Questions, suggestions, commentaires', 'cible', '', 0, 0),
('button_submit', 1, 'Soumettre', 'cible', '', 0, 0),
('button_reset', 1, 'Recommencer', 'cible', '', 0, 0),
('button_submit', 2, 'Submit', 'cible', '', 0, 0),
('button_reset', 2, 'Restart', 'cible', '', 0, 0),
('error_field_required', 1, 'Ce champ est obligatoire.', 'cible', '', 0, 0),
('error_field_required', 2, 'This field is required.', 'cible', '', 0, 0),
('button_refresh_captcha', 1, 'Rafraichir', 'cible', '', 0, 0),
('button_refresh_captcha', 2, 'Refresh', 'cible', '', 0, 0),
('module_blog', 1, 'Blogue', 'cible', '', 0, 0),
('module_achievement', 1, 'Réalisations', 'cible', '', 0, 0),
('module_contact', 1, 'Contact', 'cible', '', 0, 0),
('module_sitemap', 1, 'Plan de site', 'cible', '', 0, 0),
('module_text', 1, 'Texte', 'cible', '', 0, 0),
('page_add_sub_section', 1, 'Ajouter une sous-section', 'cible', '', 0, 0),
('error_message_password_notSame', 1, 'Le mot de passe confirmé doit être identique au nouveau mot de passe.', 'cible', '', 0, 0),
('error_message_password_isEmpty', 1, 'Veuillez confirmer le nouveau mot de passe.', 'cible', '', 0, 0),
('page_delete_message_confirmation', 1, 'Voulez-vous vraiment supprimer la page <em><b>%PAGE_TITLE%</b></em>, tous ces enfants, et ce, dans toutes les langues?', 'cible', '', 0, 0),
('button_yes', 1, 'Oui', 'cible', '', 0, 0),
('button_no', 1, 'Non', 'cible', '', 0, 0),
('page_delete_message_cant_find_page', 1, 'Impossible de trouver la page à supprimer', 'cible', '', 0, 0),
('page_action_delete', 1, 'Supprimer', 'cible', '', 0, 0),
('page_action_edit', 1, 'Éditer', 'cible', '', 0, 0),
('page_action_add', 1, 'Ajouter une sous-section à cette page', 'cible', '', 0, 0),
('page_form_edit_title', 1, 'Modification de l''information de la page', 'cible', '', 0, 0),
('page_error_message_no_block_found', 1, 'Il n''y a aucun bloc dans cette page.', 'cible', '', 0, 0),
('block_title_add_block', 1, 'Ajouter un bloc %BLOCK_TYPE%', 'cible', '', 0, 0),
('block_delete_message_confirmation', 1, 'Voulez-vous vraiment supprimer le bloc <em><b>%BLOCK_TITLE%</b></em>?', 'cible', '', 0, 0),
('block_delete_message_cant_find_block', 1, 'Impossible de trouver ce bloc', 'cible', '', 0, 0),
('button_return', 1, 'Retour', 'cible', '', 0, 0),
('button_return', 2, 'Go back', 'cible', '', 0, 0),
('block_title_edit_block', 1, 'Éditer le bloc %BLOCK_TYPE%', 'cible', '', 0, 0),
('button_edit', 1, 'Éditer', 'cible', '', 0, 0),
('button_delete', 1, 'Supprimer', 'cible', '', 0, 0),
('button_move_up', 1, 'Monter', 'cible', '', 0, 0),
('button_move_down', 1, 'Descendre', 'cible', '', 0, 0),
('cible_all_right_reserved', 1, '© CIBLE solutions d''affaires ##YEAR##  -- Tous droits réservés', 'cible', '', 0, 0),
('extranet_profile_modify_link', 1, 'Profil', 'cible', '', 0, 0),
('extranet_welcome_message', 1, 'Bienvenue dans l''extranet', 'cible', '', 0, 0),
('extranet_back_main_menu', 1, 'Tableau de bord', 'cible', '', 0, 0),
('extranet_logout_link', 1, 'Quitter', 'cible', '', 0, 0),
('extranet_cache_clear_client_texts', 1, 'Effacer la cache des textes clients', 'cible', '', 0, 0),
('extranet_cache_clear_cible_texts', 1, 'Effacer la cache des textes de base', 'cible', '', 0, 0),
('administrator_delete_message_confirmation', 1, 'Voulez-vous vraiment supprimer l''administrateur <em><b>%ADMINISTRATOR_NAME%</b></em>?', 'cible', '', 0, 0),
('page_error_message_no_block_found_in_zone', 1, 'Aucun bloc présentement', 'cible', '', 0, 0),
('administrator_group_delete_message_confirmation', 1, 'Voulez-vous vraiment supprimer le groupe d''administrateurs <b>%GROUP_NAME% </b>?', 'cible', '', 0, 0),
('status_online', 1, 'En ligne', 'cible', '', 0, 0),
('status_online', 2, 'Online', 'cible', '', 0, 0),
('status_offline', 1, 'Hors ligne', 'cible', '', 0, 0),
('status_offline', 2, 'Offline', 'cible', '', 0, 0),
('module_delete_message_confirmation', 1, 'Voulez-vous vraiment supprimer l''élément %ELEMENT_TITLE%?', 'cible', '', 0, 0),
('module_element_nout_found', 1, 'L''élément que vous tentez de supprimer est inexistant.', 'cible', '', 0, 0),
('validation_message_invalid_date', 1, 'Veuillez entrer une date valide.', 'cible', '', 0, 0),
('validation_message_invalid_text', 1, 'Veuillez saisir un texte.', 'cible', '', 0, 0),
('button_save', 1, 'Sauvegarder', 'cible', '', 0, 0),
('button_save', 2, 'Save', 'cible', '', 0, 0),
('button_save_close', 1, 'Sauvegarder et fermer', 'cible', '', 0, 0),
('button_save_close', 2, 'Save and Close', 'cible', '', 0, 0),
('button_save_publish', 1, 'Sauvegarder et mettre en ligne', 'cible', '', 0, 0),
('button_save_return_writing', 1, 'Sauvegarder et retourner en rédaction', 'cible', '', 0, 0),
('button_save_return_writing', 2, 'Save and return to writing', 'cible', '', 0, 0),
('button_save_submit_auditor', 1, 'Sauvegarder et soumettre au réviseur', 'cible', '', 0, 0),
('button_save_submit_auditor', 2, 'Save and submit to the auditor', 'cible', '', 0, 0),
('button_save_publish', 2, 'Save and publish', 'cible', '', 0, 0),
('button_cancel', 1, 'Annuler', 'cible', '', 0, 0),
('button_cancel', 2, 'Cancel', 'cible', '', 0, 0),
('validation_message_empty_daterange', 1, 'Veuillez entrer au moins une plage de dates valide.', 'cible', '', 0, 0),
('validation_message_invalid_daterange', 1, 'Veuillez vérifier que vos plages sont valides.', 'cible', '', 0, 0),
('validation_message_invalid_date_format', 1, 'Veuillez vérifier le format des dates (AAAA-MM-JJ).', 'cible', '', 0, 0),
('validation_message_endDate_earlier', 1, 'Veuillez vérifier que vos dates de fin sont supérieures à vos date de début.', 'cible', '', 0, 0),
('validation_message_empty_field', 1, 'Ce champ doit être rempli.', 'cible', '', 0, 0),
('validation_message_empty_field', 2, 'This field should not be empty', 'cible', '', 0, 0),
('form_label_title', 1, 'Titre', 'cible', '', 0, 0),
('form_label_text', 1, 'Texte', 'cible', '', 0, 0),
('form_label_title', 2, 'Title', 'cible', '', 0, 0),
('form_label_text', 2, 'Text', 'cible', '', 0, 0),
('extranet_welcome_message', 2, 'Welcome to the extranet', 'cible', '', 0, 0),
('extranet_profile_modify_link', 2, 'Edit your profile', 'cible', '', 0, 0),
('extranet_back_main_menu', 2, 'Dashboard', 'cible', '', 0, 0),
('extranet_logout_link', 2, 'Exit', 'cible', '', 0, 0),
('form_label_fname', 1, 'Prénom', 'cible', '', 0, 0),
('form_label_fname', 2, 'First name', 'cible', '', 0, 0),
('form_label_lname', 1, 'Nom', 'cible', '', 0, 0),
('form_label_lname', 2, 'Last name', 'cible', '', 0, 0),
('form_label_email', 1, 'Courriel', 'cible', '', 0, 0),
('form_label_email', 2, 'Email', 'cible', '', 0, 0),
('form_label_username', 1, 'Nom d''utilisateur', 'cible', '', 0, 0),
('form_label_username', 2, 'Username', 'cible', '', 0, 0),
('form_label_newPwd', 1, 'Nouveau mot de passe', 'cible', '', 0, 0),
('form_label_newPwd', 2, 'New password', 'cible', '', 0, 0),
('form_label_confirmNewPwd', 1, 'Confirmer le nouveau mot de passe', 'cible', '', 0, 0),
('form_label_confirmNewPwd', 2, 'Confirm new password', 'cible', '', 0, 0),
('validation_message_emailAddressInvalid', 1, '''%value%'' n''est pas une adresse courriel valide.', 'cible', '', 0, 0),
('validation_message_emailAddressInvalid', 2, '''%value%'' is not a valid email address', 'cible', '', 0, 0),
('error_message_password_isEmpty', 2, 'Please confirm the new password.', 'cible', '', 0, 0),
('error_message_password_notSame', 2, 'The confirmation of the password must be identical to the new password.', 'cible', '', 0, 0),
('form_label_name', 1, 'Nom', 'cible', '', 0, 0),
('form_label_name', 2, 'Name', 'cible', '', 0, 0),
('form_label_description', 1, 'Description', 'cible', '', 0, 0),
('form_label_description', 2, 'Description', 'cible', '', 0, 0),
('form_label_status', 1, 'Statut', 'cible', '', 0, 0),
('form_label_status', 2, 'Status', 'cible', '', 0, 0),
('form_label_noRight', 1, 'Aucun droit', 'cible', '', 0, 0),
('form_label_noRight', 2, 'No right', 'cible', '', 0, 0),
('error_message_permission', 1, 'Vous n''avez pas les droits nécessaire pour effectuer cette action.', 'cible', '', 0, 0),
('error_message_permission', 2, 'You do not have necessary permissions to perform this action.', 'cible', '', 0, 0),
('cible_all_right_reserved', 2, '© ##YEAR## Cible Solutions d''Affaires -- All Rights Reserved', 'cible', '', 0, 0),
('form_label_menu_link', 1, 'Lien *', 'cible', '', 0, 0),
('form_label_description_image', 1, 'Description de l''image', 'cible', '', 0, 0),
('form_label_description_image', 2, 'Image description', 'cible', '', 0, 0),
('form_label_short_text', 1, 'Texte bref', 'cible', '', 0, 0),
('form_label_short_text', 2, 'Short text', 'cible', '', 0, 0),
('form_label_company', 1, 'Nom de l''entreprise', 'cible', '', 0, 0),
('form_label_company', 2, 'Company name', 'cible', '', 0, 0),
('form_label_company_id', 1, 'Identifiant de l''entreprise', 'cible', '', 0, 0),
('form_label_company_id', 2, 'Company id', 'cible', '', 0, 0),
('form_label_city', 1, 'Ville', 'cible', '', 0, 0),
('form_label_city', 2, 'City', 'cible', '', 0, 0),
('form_label_address_more', 1, 'Complément d''adresse', 'cible', '', 0, 0),
('form_label_address_more', 2, 'Additional address', 'cible', '', 0, 0),
('form_label_state', 1, 'Province / Etat', 'cible', '', 0, 0),
('form_label_state', 2, 'Province / State', 'cible', '', 0, 0),
('profile_title_Members', 1, 'Client', 'cible', '', 0, 0),
('profile_title_Members', 2, 'Client', 'cible', '', 0, 0),
('form_label_zip_code', 1, 'Code postal', 'cible', '', 0, 0),
('form_label_zip_code', 2, 'Zip code', 'cible', '', 0, 0),
('form_label_country', 1, 'Pays', 'cible', '', 0, 0),
('form_label_country', 2, 'Country', 'cible', '', 0, 0),
('form_label_account_number', 1, 'Numéro de compte', 'cible', '', 0, 0),
('form_label_account_number', 2, 'Account number', 'cible', '', 0, 0),
('module_delete_message_confirmation ', 2, 'Are you sure you want to delete the item %ELEMENT_TITLE%?', 'cible', '', 0, 0),
('button_yes', 2, 'Yes', 'cible', '', 0, 0),
('button_no', 2, 'No', 'cible', '', 0, 0),
('form_title_login', 2, 'Identification', 'cible', '', 0, 0),
('form_label_password', 2, 'Password', 'cible', '', 0, 0),
('button_authenticate', 2, 'Connection', 'cible', '', 0, 0),
('block_title_add_block', 2, 'Add block %BLOCK_TYPE%', 'cible', '', 0, 0),
('block_form_label_showHeader', 1, 'Afficher l''en-tête et le titre du bloc sur le site Web.', 'cible', '', 0, 0),
('block_form_label_showHeader', 2, 'Display the header and title block in the website', 'cible', '', 0, 0),
('button_add', 1, 'Ajouter', 'cible', '', 0, 0),
('button_add', 2, 'Add', 'cible', '', 0, 0),
('form_label_zone', 1, 'Zone', 'cible', '', 0, 0),
('form_label_zone', 2, 'Zone', 'cible', '', 0, 0),
('form_label_position', 1, 'Position - Ordre d''affichage', 'cible', '', 0, 0),
('form_label_position', 2, 'Position', 'cible', '', 0, 0),
('form_label_language', 1, 'Langue', 'cible', '', 0, 0),
('module_text', 2, 'Text', 'cible', '', 0, 0),
('form_label_model', 1, 'Modèle', 'cible', '', 0, 0),
('form_label_model', 2, 'Model', 'cible', '', 0, 0),
('form_legend_settings', 1, 'Paramètres', 'cible', '', 0, 0),
('form_legend_settings', 2, 'Settings', 'cible', '', 0, 0),
('form_label_category', 1, 'Catégorie', 'cible', '', 0, 0),
('form_label_category', 2, 'Category', 'cible', '', 0, 0),
('form_label_showOnline', 1, 'Afficher sur le Web', 'cible', '', 0, 0),
('form_label_showOnline', 2, 'Show on the Web', 'cible', '', 0, 0),
('form_label_language', 2, 'Language', 'cible', '', 0, 0),
('error_page_selection_required', 1, 'Vous devez sélectionner une page de l''arborescence ci-dessus.', 'cible', '', 0, 0),
('error_invalid_url', 1, 'L''URL ''%value%'' est invalide.', 'cible', '', 0, 0),
('form_label_page_picker', 1, 'Choisir une page de l''arborescence *', 'cible', '', 0, 0),
('page_delete_item_message_confirmation', 1, 'Voulez-vous vraiment supprimer l''élément %ITEM_NAME%?', 'cible', '', 0, 0),
('page_delete_item_and_children_message_confirmation', 1, 'Voulez-vous vraiment supprimer l''élément %ITEM_NAME% ainsi que ses sous-éléments?', 'cible', '', 0, 0),
('validation_message_used_email', 1, 'Cette adresse courriel est déjà utilisée par une autre personne.', 'cible', '', 0, 0),
('validation_message_used_email', 2, 'This email is already used by another person', 'cible', '', 0, 0),
('newsletterMember_delete_message_confirmation', 1, 'Voulez-vous vraiment supprimer %MEMBER_NAME% de cette liste d''envoi?', 'cible', '', 0, 0),
('newsletterMember_delete_message_confirmation', 2, 'Are you sure you want to permanently delete %Member_Name% of the mailing list for this newsletter?', 'cible', '', 0, 0),
('dashboard_page_title', 1, 'Tableau de bord', 'cible', '', 0, 0),
('dashboard_page_title', 2, 'Dashboard', 'cible', '', 0, 0),
('dashboard_page_description', 1, '<b>Bienvenue sur votre tableau de bord</b><br><br>Toutes les fonctionnalités du système de gestion<br>de contenu sont accessibles par cette page.<br><br>Vous pouvez accéder en tout temps au tableau de<br>bord en cliquant sur l''onglet en en-tête.', 'cible', '', 0, 0),
('dashboard_data_management', 1, 'Gestion des données par module', 'cible', '', 0, 0),
('dashboard_reminder_title', 1, 'Activités à valider sur le site', 'cible', '', 0, 0),
('dashboard_administration_title', 1, 'Administration', 'cible', '', 0, 0),
('dashboard_administration_website_title', 1, 'Site Internet', 'cible', '', 0, 0),
('dashboard_administration_website_mobile_title', '1', 'Site Internet mobile', 'cible', '', '0', '0'),
('dashboard_administration_website_mobile_title', '2', 'Mobile Website', 'cible', '', '0', '0'),
('dashboard_administration_website_sitemap_management', 1, 'Arborescence', 'cible', '', 0, 0),
('dashboard_administration_website_page_structure', 1, 'Gestion de la structure des pages', 'cible', '', 0, 0),
('dashboard_administration_administrators_title', 1, 'Administrateurs', 'cible', '', 0, 0),
('dashboard_administration_administrators_groups_management', 1, 'Groupes et permissions', 'cible', '', 0, 0),
('dashboard_administration_administrators_administrator_management', 1, 'Administrateurs', 'cible', '', 0, 0),
('dashboard_administration_protected_sections_title', 1, 'Sections sécurisées', 'cible', '', 0, 0),
('dashboard_administration_protected_sections_management', 1, 'Gestion des sections sécurisées', 'cible', '', 0, 0),
('dashboard_administration_protected_sections_groups_management', 1, 'Gestion des groupes', 'cible', '', 0, 0),
('dashboard_administration_protected_sections_users_management', 1, 'Gestion des utilisateurs', 'cible', '', 0, 0),
('dashboard_administration_contents_indexing_title', 1, 'Indexation des contenus', 'cible', '', 0, 0),
('page_module_name', 1, 'Site internet', 'cible', '', 0, 999),
('page_module_name', 2, 'Website', 'cible', '', 0, 999),
('text_module_name', 1, 'Textes', 'cible', '', 0, 0),
('text_module_name', 2, 'Text', 'cible', '', 0, 0),
('contact_module_name', 1, 'Contact', 'cible', '', 0, 0),
('contact_module_name', 2, 'Contact', 'cible', '', 0, 0),
('achievement_module_name', 1, 'Réalisations', 'cible', '', 0, 0),
('achievement_module_name', 2, 'Achievements', 'cible', '', 0, 0),
('sitemap_module_name', 1, 'Plan du site', 'cible', '', 0, 0),
('sitemap_module_name', 2, 'Sitemap', 'cible', '', 0, 0),
('blog_module_name', 1, 'Blogue', 'cible', '', 0, 0),
('blog_module_name', 2, 'Blog', 'cible', '', 0, 0),
('dashboard_page_description', 2, 'Welcome on your instrument panel. Visualize and organize the actions to be carried out systematically', 'cible', '', 0, 0),
('dashboard_data_management', 2, 'Data management', 'cible', '', 0, 0),
('dashboard_reminder_title', 2, 'Activities to be validated on the site', 'cible', '', 0, 0),
('dashboard_administration_title', 2, 'Administration', 'cible', '', 0, 0),
('dashboard_administration_website_title', 2, 'Website', 'cible', '', 0, 0),
('dashboard_administration_website_sitemap_management', 2, 'Sitemap management', 'cible', '', 0, 0),
('dashboard_administration_website_page_structure', 2, 'Website page structure', 'cible', '', 0, 0),
('dashboard_administration_administrators_title', 2, 'Administrators', 'cible', '', 0, 0),
('dashboard_administration_administrators_groups_management', 2, 'Administrators groups management', 'cible', '', 0, 0),
('dashboard_administration_administrators_administrator_management', 2, 'Administrators management', 'cible', '', 0, 0),
('dashboard_administration_protected_sections_title', 2, 'Protected sections', 'cible', '', 0, 0),
('dashboard_administration_protected_sections_management', 2, 'Protected sections management', 'cible', '', 0, 0),
('dashboard_administration_protected_sections_groups_management', 2, 'Groups management', 'cible', '', 0, 0),
('dashboard_administration_protected_sections_users_management', 2, 'Users management', 'cible', '', 0, 0),
('dashboard_administration_contents_indexing_title', 2, 'Contents indexing', 'cible', '', 0, 0),
('treeview_contents_management_title', 1, 'Contenus par page', 'cible', '', 0, 0),
('treeview_contents_management_title', 2, 'Contents management', 'cible', '', 0, 0),
('treeview_data_management_title', 1, 'Gestion des données par module', 'cible', '', 0, 0),
('treeview_data_management_title', 2, 'Data management', 'cible', '', 0, 0),
('outil_gestion_title', 1, 'SYSTÈME DE GESTION DE CONTENU', 'cible', '', 0, 0),
('outil_gestion_title', 2, 'MANAGEMENT TOOLS', 'cible', '', 0, 0),
('manage_block_settings', 1, 'Paramètres', 'cible', '', 0, 0),
('manage_block_settings', 2, 'Settings', 'cible', '', 0, 0),
('manage_block_publish', 1, 'Publier', 'cible', '', 0, 0),
('manage_block_publish', 2, 'Publish', 'cible', '', 0, 0),
('manage_block_unpublish', 1, 'Mettre hors ligne', 'cible', '', 0, 0),
('manage_block_unpublish', 2, 'Unpublish', 'cible', '', 0, 0),
('manage_block_delete', 1, 'Supprimer', 'cible', '', 0, 0),
('manage_block_delete', 2, 'Delete', 'cible', '', 0, 0),
('page_website_sitemap_title', 1, 'Gestion de l''arborescence', 'cible', '', 0, 0),
('page_website_sitemap_description', 1, '<b>Faites glisser l''icône</b> <img src="/extranet/icons/file.png" align=middle> pour déplacer une page.<br><br>Vous pouvez <b>ajouter une page à la racine</b>.<br><br>Vous pouvez <b>modifier</b> ou <b>gérer la structure</b><br>en cliquant sur le nom de la page.', 'cible', '', 0, 0),
('form_parameters_fieldset', 1, 'Paramètres', 'cible', '', 0, 0),
('form_parameters_fieldset', 2, 'Parameters', 'cible', '', 0, 0),
('block_title_add_block_description', 1, 'Vous pouvez ajouter un bloc à cette page.', 'cible', '', 0, 0),
('block_title_add_block_description', 2, 'This page allows you to add a block to this page.', 'cible', '', 0, 0),
('form_label_menu_link', 2, 'Link *', 'cible', '', 0, 0),
('dashboard_administration_website_menu_structure', 1, 'Menus', 'cible', '', 0, 0),
('menu_treeview_add', 1, 'Ajouter', 'cible', '', 0, 0),
('menu_treeview_add', 2, 'Add', 'cible', '', 0, 0),
('menu_treeview_edit', 1, 'Modifier', 'cible', '', 0, 0),
('menu_treeview_edit', 2, 'Edit', 'cible', '', 0, 0),
('menu_treeview_delete', 1, 'Supprimer', 'cible', '', 0, 0),
('menu_treeview_delete', 2, 'Delete', 'cible', '', 0, 0),
('menu_treeview_homepage', 1, 'Définir comme accueil', 'cible', '', 0, 0),
('menu_treeview_homepage', 2, 'Set as home page', 'cible', '', 0, 0),
('menu_treeview_pagestructure', 1, 'Structure de la page', 'cible', '', 0, 0),
('menu_treeview_pagestructure', 2, 'Page structure', 'cible', '', 0, 0),
('menu_treeview_drag_item', 1, 'Déplacer cette page', 'cible', '', 0, 0),
('menu_treeview_drag_item', 2, 'Drag and drop this page', 'cible', '', 0, 0),
('menu_generate_from_structure', 1, 'Générer le menu à partir de l''arborescence', 'cible', '', 0, 0),
('menu_add_root_item', 1, 'Ajouter un élément à la racine', 'cible', '', 0, 0),
('menu_submenu_action_generate', 1, 'Générer automatiquement les sous-menus', 'cible', '', 0, 0),
('menu_submenu_action_add', 1, 'Ajouter un sous-menu', 'cible', '', 0, 0),
('menu_submenu_action_edit', 1, 'Modifier', 'cible', '', 0, 0),
('menu_submenu_action_delete', 1, 'Supprimer', 'cible', '', 0, 0),
('form_label_menu_title', 1, 'Libellé du menu', 'cible', '', 0, 0),
('form_label_menu_type', 1, 'Type de menu', 'cible', '', 0, 0),
('form_label_menu_load_image', 1, 'Utiliser une image (png transparent)', 'cible', '', 0, 0),
('form_label_menu_load_image', 2, 'Use an image', 'cible', '', 0, 0),
('form_label_menu_display_image_and_title', 1, 'Afficher le titre et l''image', 'cible', '', 0, 0),
('form_label_menu_display_image_and_title', 2, 'Display the title and the image', 'cible', '', 0, 0),
('dashboard_administration_administrators_administrator_management_description', 1, 'Cette page vous permet de gérer les administrateurs.', 'cible', '', 0, 0),
('dashboard_administration_administrators_administrator_management_description', 2, 'This page is to manage the administrators.', 'cible', '', 0, 0),
('dashboard_administration_administrators_groups_management_description', 1, 'Cette page vous permet de gérer les groupes d''administrateurs. ', 'cible', '', 0, 0),
('dashboard_administration_administrators_groups_management_description', 2, 'This page is to manage the administrators groups.', 'cible', '', 0, 0),
('dashboard_administration_website_menu_structure', 2, 'Menus management', 'cible', '', 0, 0),
('extranet_profile_modify_link_description', 1, '<b>Entrez les renseignements requis</b> pour votre profil.<br><br>Vous pouvez redéfinir votre mot de passe sur cette page.', 'cible', '', 0, 0),
('extranet_profile_modify_link_description', 2, 'This page is to modify your profile.', 'cible', '', 0, 0),
('button_search_label', 1, 'Rechercher', 'cible', '', 0, 0),
('search_searched_keyword', 1, 'Résultat(s) de recherche pour « <strong>%KEYWORD%</strong> »', 'cible', '', 0, 0),
('search_result_items_found', 1, 'Résultat(s) de recherche : <br />%ITEM_COUNT% élément(s) trouvé(s)', 'cible', '', 0, 0),
('search_empty_recordset', 1, 'Aucun résultat pour cette recherche. Essayez de nouveaux mots-clés ou cliquez sur <b>Voir la liste complète</b>.', 'cible', '', 0, 0),
('search_empty_recordset', 2, 'No result for this search. Try with new keywords or click on <b>See the complete list</b>.', 'cible', '', 0, 0),
('search_box_default_value', 1, 'Rechercher', 'client', 'Texte affiché pour le champ de recherche sur le site.', 0, 0),
('search_box_default_value', 2, 'Search', 'client', 'Text for the search box on the site.', 0, 0),
('button_add_administrators', 1, 'Ajouter un administrateur', 'cible', '', 0, 0),
('button_add_administrators', 2, 'Add an administrator', 'cible', '', 0, 0),
('button_add_administrators_group', 1, 'Ajouter un groupe d''administrateurs', 'cible', '', 0, 0),
('button_add_administrators_group', 2, 'Add an administrator group', 'cible', '', 0, 0),
('button_add_root_page', 1, 'Ajouter une page à la racine', 'cible', '', 0, 0),
('button_add_root_page', 2, 'Add a page at root', 'cible', '', 0, 0),
('page_add_page_title', 1, 'Ajout d''une page', 'cible', '', 0, 0),
('page_add_page_title', 2, 'Add a page', 'cible', '', 0, 0),
('page_add_page_description', 1, 'Cette section vous permet d''ajouter une nouvelle page à votre site Web.', 'cible', '', 0, 0),
('page_add_page_description', 2, 'This section is to add a new page to your Web site.', 'cible', '', 0, 0),
('page_edit_page_title', 1, 'Édition d''une page', 'cible', '', 0, 0),
('page_edit_page_title', 2, 'Edit a page', 'cible', '', 0, 0),
('page_edit_page_description', 1, 'Cette section vous permet d''éditer une page de votre site Web.', 'cible', '', 0, 0),
('page_edit_page_description', 2, 'This section is to edit a page of your Web site.', 'cible', '', 0, 0),
('page_add_page_root_breadcrumb', 1, 'Racine du site Web', 'cible', '', 0, 0),
('page_add_page_root_breadcrumb', 2, 'Root of the Web site', 'cible', '', 0, 0),
('list_column_PI_PageTitle', 1, 'Titre de la page', 'cible', '', 0, 0),
('list_column_PI_Status', 1, 'Statut', 'cible', '', 0, 0),
('list_column_action_panel', 1, 'Actions', 'cible', '', 0, 0),
('page_structure_management_title', 1, 'Gestion de la structure', 'cible', '', 0, 0),
('page_structure_management_title', 2, 'Page structure management', 'cible', '', 0, 0),
('page_structure_management_description', 1, '<b>Faites glisser l''icône</b> du bloc dans la zone de votre choix.<br><br>Vous pouvez <b>publier</b> sur le site, éditer les <b>paramètres</b><br>ou <b>supprimer</b> chacun des blocs.<br><br>Vous pouvez éditer le contenu des blocs en cliquant<br> sur le bouton <b>Gérer les contenus de cette page</b>.', 'cible', '', 0, 0),
('page_structure_management_description', 2, 'On this page, you can manage the structure of the pages by adding the desired blocks to it.', 'cible', '', 0, 0),
('block_edit_parameters_title', 1, 'Paramètres', 'cible', '', 0, 0),
('block_edit_parameters_title', 2, 'Block settings', 'cible', '', 0, 0),
('block_edit_parameters_description', 1, 'Cette page vous permet de gérer les paramètres du bloc.', 'cible', '', 0, 0),
('block_edit_parameters_description', 2, 'This page is to manage the settings of your block.', 'cible', '', 0, 0),
('breadcrumb_default_text', 1, 'Les pages actuelles de votre site', 'cible', '', 0, 0),
('breadcrumb_default_text', 2, 'Current pages of your Web site', 'cible', '', 0, 0),
('page_content_management_title', 1, 'Gestion de contenu', 'cible', '', 0, 0),
('page_content_management_title', 2, 'Management of the contents of the page', 'cible', '', 0, 0),
('page_content_management_description', 1, 'Cliquez sur <b>les boutons d''action</b> pour éditer les contenus<br>de chacun des blocs de la page %PAGE_NAME%.<br><br>Vous pouvez ajouter ou supprimer des blocs<br>en cliquant sur le bouton <b>Gérer la structure<br>de cette page</b>.', 'cible', '', 0, 0),
('page_content_management_description', 2, 'On this page, you can manage the contents of the blocks for the page %PAGE_NAME%.', 'cible', '', 0, 0),
('header_list_administrator_groups_text', 1, 'Gestion des groupes<br>et des permissions', 'cible', '', 0, 0),
('header_list_administrator_groups_text', 2, 'Administrators groups list', 'cible', '', 0, 0),
('header_list_administrator_groups_description', 1, '<b>Cliquez sur le bouton</b> <img src="/extranet/icons/icon-action-24x24.png" align=middle> pour éditer un groupe.<br><br>Vous pouvez ajouter un groupe et en exporter la liste<br>vers un fichier Excel.<br><br>Vous pouvez <b>rechercher par mots-clés</b> parmi la liste<br>des groupes. Pour revenir à la liste complète,<br>cliquez sur <b>Voir la liste complète</b>.', 'cible', '', 0, 0),
('header_list_administrator_groups_description', 2, 'This page is to consult the administrators groups list.', 'cible', '', 0, 0),
('header_list_administrator_text', 1, 'Liste des administrateurs', 'cible', '', 0, 0),
('header_list_administrator_text', 2, 'Administrators list', 'cible', '', 0, 0),
('header_list_administrator_description', 1, 'Cette page vous permet de consulter la liste des administrateurs.', 'cible', '', 0, 0),
('header_list_administrator_description', 2, 'This page is to consult the administrators list.', 'cible', '', 0, 0),
('header_add_administrator_text', 1, 'Ajout d''un administrateur', 'cible', '', 0, 0),
('header_add_administrator_text', 2, 'Add an administrator', 'cible', '', 0, 0),
('header_add_administrator_description', 1, 'Cette page vous permet d''ajouter un administrateur.', 'cible', '', 0, 0),
('header_add_administrator_description', 2, 'This page is to add an administrator.', 'cible', '', 0, 0),
('header_add_administrator_group_text', 1, 'Ajout d''un groupe d''administrateurs', 'cible', '', 0, 0),
('header_add_administrator_group_text', 2, 'Add an administrator group', 'cible', '', 0, 0),
('header_add_administrator_group_description', 1, 'Cette page vous permet d''ajouter un groupe d''administrateurs.', 'cible', '', 0, 0),
('header_add_administrator_group_description', 2, 'This page is to add an administrator group.', 'cible', '', 0, 0),
('header_edit_administrator_text', 1, 'Édition du profil d''un administrateur', 'cible', '', 0, 0),
('header_edit_administrator_text', 2, 'Administrator edit', 'cible', '', 0, 0);
REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('header_edit_administrator_description', 1, '<b>Entrez les renseignements requis</b><br>pour le profil de cet administrateur.<br><br>Vous pouvez redéfinir le mot de passe sur cette page.', 'cible', '', 0, 0),
('header_edit_administrator_description', 2, 'This page is to edit the information of an administrator.', 'cible', '', 0, 0),
('header_edit_administrator_group_text', 1, 'Édition d''un groupe', 'cible', '', 0, 0),
('header_edit_administrator_group_text', 2, 'Administratiors group edit', 'cible', '', 0, 0),
('header_edit_administrator_group_description', 1, '<b>Entrez les renseignements requis</b> pour éditer le profil<br>de ce groupe d''administrateurs.', 'cible', '', 0, 0),
('header_edit_administrator_group_description', 2, 'This page is to edit an administrators group.', 'cible', '', 0, 0),
('header_associate_administrator_text', 1, 'Droits d''administration', 'cible', '', 0, 0),
('header_associate_administrator_text', 2, 'Association des droits', 'cible', '', 0, 0),
('header_associate_administrator_description', 1, 'Cette page vous permet d''éditer les droits d''un administrateur.', 'cible', '', 0, 0),
('header_associate_administrator_description', 2, 'Cette page vous permert d''éditer les droits d''un administrateur.', 'cible', '', 0, 0),
('header_associate_administrator_group_text', 1, 'Droits d''administration', 'cible', '', 0, 0),
('header_associate_administrator_group_text', 2, 'Association des droits', 'cible', '', 0, 0),
('header_associate_administrator_group_description', 1, 'Cette page vous permet d''éditer les droits d''un groupe d''administrateurs.', 'cible', '', 0, 0),
('header_associate_administrator_group_description', 2, 'This page is to edit the administrators permissions.', 'cible', '', 0, 0),
('header_menu_structure_management_text', 1, 'Gestion des menus', 'cible', '', 0, 0),
('header_menu_structure_management_text', 2, 'Menus management', 'cible', '', 0, 0),
('header_menu_structure_management_description', 1, 'Cette page vous permet de gérer la structure des menus de votre site Web.', 'cible', '', 0, 0),
('header_menu_structure_management_description', 2, 'This page is to manage the menus structure of your Web site.', 'cible', '', 0, 0),
('search_list_all_items', 1, 'Voir la liste complète', 'cible', '', 0, 0),
('dashboard_administration_profil_list', 1, 'Inscription', 'cible', '', 0, 0),
('dashboard_administration_profil_list', 2, 'Subscriptions', 'cible', '', 0, 0),
('dashboard_administration_profil_list_description', 1, 'Banque d''usagers complète', 'cible', '', 0, 0),
('menu_submenu_action_add', 2, 'Add a sub-menu', 'cible', '', 0, 0),
('menu_submenu_action_delete', 2, 'Delete', 'cible', '', 0, 0),
('menu_submenu_action_edit', 2, 'Edit', 'cible', '', 0, 0),
('menu_submenu_action_generate', 2, 'Generate automatically the sub-menus', 'cible', '', 0, 0),
('list_column_lastName', 1, 'Nom de famille', 'cible', '', 0, 0),
('list_column_lastName', 2, 'Last Name', 'cible', '', 0, 0),
('list_column_firstName', 1, 'Prénom', 'cible', '', 0, 0),
('list_column_firstName', 2, 'First Name', 'cible', '', 0, 0),
('list_column_email', 1, 'Adresse courriel', 'cible', '', 0, 0),
('list_column_email', 2, 'Email', 'cible', '', 0, 0),
('list_column_action_panel', 2, 'Actions', 'cible', '', 0, 0),
('wait_while_loading', 1, 'Veuillez patienter...', 'cible', '', 0, 0),
('wait_while_loading', 2, 'Please wait...', 'cible', '', 0, 0),
('page_website_sitemap_description', 2, 'Sitemap management help goes here.', 'cible', '', 0, 0),
('page_website_sitemap_title', 2, 'Sitemap Management', 'cible', '', 0, 0),
('menu_generate_from_structure', 2, 'Generate menu from structure', 'cible', '', 0, 0),
('menu_add_root_item', 2, 'Add a menu item to the root', 'cible', '', 0, 0),
('export_to_excel', 1, 'Exporter vers Excel', 'cible', '', 0, 0),
('export_to_excel', 2, 'Export to Excel', 'cible', '', 0, 0),
('button_associate', 1, 'Association des droits', 'cible', '', 0, 0),
('button_associate', 2, 'Associate', 'cible', '', 0, 0),
('form_label_isMember', 1, 'Membre du Groupement des Chefs', 'cible', '', 0, 0),
('form_label_isMember', 2, 'Member of the "Groupement des Chefs"', 'cible', '', 0, 0),
('list_column_EGI_Name', 1, 'Nom', 'cible', '', 0, 0),
('list_column_EGI_Description', 1, 'Description', 'cible', '', 0, 0),
('list_column_EGI_Name', 2, 'Name', 'cible', '', 0, 0),
('list_column_EGI_Description', 2, 'Description', 'cible', '', 0, 0),
('list_column_EU_FName', 1, 'Prénom', 'cible', '', 0, 0),
('list_column_EU_LName', 1, 'Nom', 'cible', '', 0, 0),
('list_column_EU_Email', 1, 'Adresse courriel', 'cible', '', 0, 0),
('list_column_EU_FName', 2, 'First Name', 'cible', '', 0, 0),
('list_column_EU_LName', 2, 'Last Name', 'cible', '', 0, 0),
('list_column_EU_Email', 2, 'Email', 'cible', '', 0, 0),
('button_search_label', 2, 'Search', 'cible', '', 0, 0),
('form_paginator_previous', 1, 'Précédent', 'cible', '', 0, 0),
('form_paginator_previous', 2, 'Previous', 'cible', '', 0, 0),
('form_paginator_next', 1, 'Suivant', 'cible', '', 0, 0),
('form_paginator_next', 2, 'Next', 'cible', '', 0, 0),
('form_list_items_per_page_start', 1, 'Afficher', 'cible', '', 0, 0),
('form_list_items_per_page_start', 2, 'Show', 'cible', '', 0, 0),
('form_list_items_per_page_end', 1, 'Éléments par page', 'cible', '', 0, 0),
('form_list_items_per_page_end', 2, 'items per page', 'cible', '', 0, 0),
('form_paginator_searchbox_label', 1, 'Rechercher', 'cible', '', 0, 0),
('form_paginator_searchbox_label', 2, 'Search', 'cible', '', 0, 0),
('categories_page_title', 1, 'Gestion des catégories pour le module «%MODULE_NAME%»', 'cible', '', 0, 0),
('categories_page_description', 1, '<b>Remplissez les champs requis</b><br>pour éditer une catégorie.', 'cible', '', 0, 0),
('categories_page_title', 2, 'Categorie Management for %MODULE_NAME%', 'cible', '', 0, 0),
('categories_page_description', 2, 'Help on categorie management', 'cible', '', 0, 0),
('form_category_title_label', 1, 'Nom de la catégorie', 'cible', '', 0, 0),
('form_select_no_parent', 1, 'Aucun parent', 'cible', '', 0, 0),
('form_select_no_parent', 2, 'No parent', 'cible', '', 0, 0),
('form_category_parent_label', 1, 'Parent de cette catégorie', 'cible', '', 0, 0),
('profile_delete_message_confirmation', 1, 'Voulez-vous supprimer définitivement la fiche de cette personne?', 'cible', '', 0, 0),
('profile_delete_message_confirmation', 2, 'Are you sure you want to permanently remove that person from the system?', 'cible', '', 0, 0),
('form_category_view_all_label', 1, 'Description', 'cible', '', 0, 0),
('form_category_title_label', 2, 'Category Name:', 'cible', '', 0, 0),
('form_category_parent_label', 2, 'Parent Category:', 'cible', '', 0, 0),
('form_category_view_all_label', 2, 'Description:', 'cible', '', 0, 0),
('form_select_option_view_search_index', 1, 'Résultat de recherche', 'cible', '', 0, 0),
('form_select_option_view_search_index', 2, 'Search results', 'cible', '', 0, 0),
('list_column_NI_Title', 1, 'Titre', 'cible', '', 0, 0),
('list_column_NI_Title', 2, 'Title', 'cible', '', 0, 0),
('list_column_ND_ReleaseDate', 1, 'Date de parution', 'cible', '', 0, 0),
('list_column_ND_Status', 1, 'Statut', 'cible', '', 0, 0),
('list_column_ND_ReleaseDate', 2, 'Publication date', 'cible', '', 0, 0),
('list_column_ND_Status', 2, 'Status', 'cible', '', 0, 0),
('list_column_CI_Title', 1, 'Catégorie', 'cible', '', 0, 0),
('list_column_CI_WordingShowAllRecords', 1, 'Description', 'cible', '', 0, 0),
('list_column_CI_Title', 2, 'Category Name', 'cible', '', 0, 0),
('list_column_CI_WordingShowAllRecords', 2, 'Category Description', 'cible', '', 0, 0),
('button_add_category', 2, 'Add a category', 'cible', '', 0, 0),
('button_add_category', 1, 'Ajouter une catégorie', 'cible', '', 0, 0),
('form_category_associated_page_label', 1, 'Page associée', 'cible', '', 0, 0),
('form_category_associated_page_label', 2, 'Associated Page:', 'cible', '', 0, 0),
('categories_add_page_title', 1, 'Ajouter une catégorie', 'cible', '', 0, 0),
('categories_add_page_title', 2, 'Add a Category', 'cible', '', 0, 0),
('menu_treeview_content_management', 1, 'Gestion des contenus', 'cible', '', 0, 0),
('menu_treeview_content_management', 2, 'Contents management', 'cible', '', 0, 0),
('header_add_profile_text', 1, 'Ajout d''une personne', 'cible', '', 0, 0),
('header_add_profile_text', 2, 'Add a person', 'cible', '', 0, 0),
('header_add_profile_description', 1, 'Cette page permet d''ajouter une nouvelle personne dans le système en spécifiant les renseignements utiles.', 'cible', '', 0, 0),
('header_add_profile_description', 2, 'This page allows you to add a new person into the system by specifying the information on it.', 'cible', '', 0, 0),
('header_edit_profile_text', 1, 'Modification', 'cible', '', 0, 0),
('header_edit_profile_text', 2, 'Modification', 'cible', '', 0, 0),
('header_edit_profile_description', 1, 'Cette page permet de modifier les renseignements sur une personne.', 'cible', '', 0, 0),
('header_edit_profile_description', 2, 'This page allows you to edit information for a person', 'cible', '', 0, 0),
('header_delete_profile_text', 1, 'Suppression ', 'cible', '', 0, 0),
('header_delete_profile_text', 2, 'Remove', 'cible', '', 0, 0),
('header_delete_profile_description', 1, 'Cette page permet de supprimer définitivement la fiche d''une personne.', 'cible', '', 0, 0),
('menu_principal', 1, 'Menu principal', 'cible', '', 0, 0),
('profile_title_general_information', 1, 'Renseignements généraux', 'cible', '', 0, 0),
('profile_title_general_information', 2, 'General Information', 'cible', '', 0, 0),
('profile_title_newsletter', 1, 'Infolettre', 'cible', '', 0, 0),
('profile_title_newsletter', 2, 'Newsletter', 'cible', '', 0, 0),
('profile_title_groupementChefsMembers', 1, 'Membres du Groupement des Chefs', 'cible', '', 0, 0),
('profile_title_groupementChefsMembers', 2, 'Members of the "Groupement des Chefs"', 'cible', '', 0, 0),
('list_column_S_Code', 1, 'Statut', 'cible', '', 0, 0),
('list_column_S_Code', 2, 'Status', 'cible', '', 0, 0),
('button_add_profile', 2, 'Add new person', 'cible', '', 0, 0),
('button_add_profile', 1, 'Ajouter une nouvelle personne', 'cible', '', 0, 0),
('button_add_general', 1, 'Ajouter une nouvelle personne', 'cible', '', 0, 0),
('button_add_general', 2, 'Add new person', 'cible', '', 0, 0),
('header_list_profile_text', 1, 'Liste ', 'cible', '', 0, 0),
('header_list_profile_description', 1, 'Liste des personnes présentes dans le système', 'cible', '', 0, 0),
('form_label_account_status', 1, 'Status du compte', 'cible', '', 0, 20),
('form_label_account_status', 2, 'Account status', 'cible', '', 0, 20),
('search_result_items_found', 2, 'Your search generated<br />%ITEM_COUNT% items found', 'cible', '', 0, 0),
('search_list_all_items', 2, 'See the complete list »', 'cible', '', 0, 0),
('button_preview_close', 1, 'Fermer', 'cible', '', 0, 0),
('button_preview_close', 2, 'Close', 'cible', '', 0, 0),
('management_module_page_structure', 1, 'Arborescence', 'cible', '', 0, 999),
('management_module_page_structure', 2, 'Pages tree view', 'cible', '', 0, 999),
('management_module_page_menu', 1, 'Menus', 'cible', '', 0, 999),
('management_module_page_menu', 2, 'Menus', 'cible', '', 0, 999),
('text_draft_title', 1, 'Brouillon', 'cible', '', 0, 1),
('text_draft_title', 2, 'Draft', 'cible', '', 0, 1),
('text_online_title', 1, 'En ligne', 'cible', '', 0, 1),
('text_online_title', 2, 'Online', 'cible', '', 0, 1),
('list_column_EI_Title', 1, 'Titre', 'cible', '', 0, 1),
('list_column_EI_Title', 2, 'Title', 'cible', '', 0, 1),
('header_edit_text_text', 1, 'Édition d''un texte', 'cible', '', 0, 1),
('header_edit_text_text', 2, 'Text edit', 'cible', '', 0, 1),
('header_edit_text_description', 1, 'Cette page vous permet d''éditer un texte.', 'cible', '', 0, 1),
('header_edit_text_description', 2, 'This page is to edit a text.', 'cible', '', 0, 1),
('management_module_text_list', 1, 'Blocs textes', 'cible', '', 0, 1),
('management_module_text_list', 2, 'Texts', 'cible', '', 0, 1),
('management_module_text_list_approbation_request', 1, 'Approbation de textes', 'cible', '', 0, 1),
('management_module_text_list_approbation_request', 2, 'Texts to be approved', 'cible', '', 0, 1);
REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, `ST_ModuleID`) VALUES
('form_legend_infoPage', 1, 'Données de la page', 'cible', '', 0, 1),
('form_legend_infoPage', 2, 'Page data', 'cible', '', 0, 1),
('form_legend_blockData', 1, 'Données du bloc', 'cible', '', 0, 1),
('form_legend_blockData', 2, 'Block Data', 'cible', '', 0, 1),
('management_module_blog_list', 1, 'Édition des nouvelles', 'cible', '', 0, 0),
('management_module_blog_list', 2, 'Blog', 'cible', '', 0, 0),
('list_column_title', 1, 'Titre', 'cible', '', 0, 0),
('action', 1, 'Actions', 'cible', '', 0, 0),
('list_column_action', 1, 'Actions', 'cible', '', 0, 0),
('categories_edit_page_title', 1, 'Édition d''une catégorie', 'cible', '', 0, 0),
('header_list_text_approbation_title', 1, 'Textes à approuver', 'cible', '', 0, 0),
('header_list_text_approbation_description', 1, '<b>Cliquez sur le titre du texte</b> que vous souhaitez approuver.<br><br>Vous pouvez <b>rechercher par mots-clés</b> parmi la liste<br>des textes à approuver. Pour revenir à la liste complète,<br>cliquez sur <b>Voir la liste complète</b>.<br><br>Vous pouvez <b>modifier ou supprimer le texte</b><br>en cliquant sur l''icône <img src="/extranet/icons/icon-action-24x24.png" align=middle>.', 'cible', '', 0, 0),
('list_column_TD_DraftTitle', 1, 'Titre du texte', 'cible', '', 0, 1),
('dictionnary_add_code_snippet', 1, 'Ajouter une balise d''aide', 'cible', '', 0, 0),
('dictionnary_add_code_snippet', 2, 'Insert an help code snippet', 'cible', '', 0, 0),
('button_all', 1, 'TOUS', 'cible', '', 0, 0),
('button_all', 2, 'ALL', 'cible', '', 0, 0),
('filter_empty_category', 1, 'Filtrer par catégories', 'cible', '', 0, 0),
('filter_empty_category', 2, 'Filter by categories', 'cible', '', 0, 0),
('filter_empty_language', 1, 'Filtrer par langues', 'cible', '', 0, 0),
('filter_empty_language', 2, 'Filtrer par langues', 'cible', '', 0, 0),
('header_list_arbo_text_text', 1, 'Liste des textes', 'cible', '', 0, 0),
('header_list_arbo_text_description', 1, '<b>Cliquez sur le nom de la page</b> dans la liste suivante<br>pour modifier les textes de votre site.<br><br>Vous pouvez <b>déployer les onglets</b> pour accéder aux sous-sections.', 'cible', '', 0, 0),
('header_list_arbo_text_text', 2, 'List texts of the Web site', 'cible', '', 0, 0),
('header_list_arbo_text_description', 2, 'This page is to manage the texts of your Web site', 'cible', '', 0, 0),
('filter_empty_status', 1, 'Filtrer par statut', 'cible', '', 0, 0),
('filter_empty_status', 2, 'Filter by status', 'cible', '', 0, 0),
('filter_empty_label', 1, 'Choisir une valeur', 'cible', '', 0, 0),
('filter_empty_label', 2, 'Select a value', 'cible', '', 0, 0),
('form_paginator_filters_label', 1, 'Filtres', 'cible', '', 0, 0),
('form_paginator_filters_label', 2, 'Filters', 'cible', '', 0, 0),
('list_return_to_list_link', 1, 'Revenir à la liste', 'cible', '', 0, 0),
('categories_delete_page_description', 1, 'Aide pour la suppression des catégories', 'cible', '', 0, 0),
('categories_delete_page_title', 1, 'Suppression de catégorie', 'cible', '', 0, 0),
('categories_delete_message_confirmation', 1, 'Voulez-vous vraiment supprimer la catégorie « %CATEGORY_NAME% »?', 'cible', '', 0, 0),
('list_column_7_CI_Title', 1, 'Catégories', 'cible', '', 0, 0),
('list_column_8_CI_Title', 1, 'Infolettre', 'cible', '', 0, 0),
('list_column_2_CI_Title', 1, 'Catégories', 'cible', '', 0, 0),
('form_category_select_a_view_label', 1, 'Choisir une vue', 'cible', '', 0, 0),
('menu_topRight', 1, 'Menu <i>Nous joindre</i>', 'cible', '', 0, 0),
('menu_topLeft', 1, 'Menu <i>Le Groupement à l''international</i>', 'cible', '', 0, 0),
('list_column_NR_Title', 1, 'Titre', 'cible', '', 0, 0),
('list_column_NR_Date', 1, 'Date de parution', 'cible', '', 0, 0),
('list_column_NR_Online', 1, 'En ligne', 'cible', '', 0, 0),
('list_column_NR_Status', 1, 'Envoyée', 'cible', '', 0, 0),
('filter_empty_send', 1, 'Filter par envoyée', 'cible', '', 0, 0),
('filter_empty_send', 2, 'Filter par envoyée', 'cible', '', 0, 0),
('salutation_mr', 1, 'M.', 'cible', '', 0, 0),
('salutation_mr', 2, 'Mr', 'cible', '', 0, 0),
('salutation_mrs', 1, 'Mme', 'cible', '', 0, 0),
('salutation_mrs', 2, 'Mrs', 'cible', '', 0, 0),
('send_0', 1, 'Non envoyée', 'cible', '', 0, 0),
('send_0', 2, 'Not sent', 'cible', '', 0, 0),
('send_1', 1, 'Envoyée', 'cible', '', 0, 0),
('send_1', 2, 'Sent', 'cible', '', 0, 0),
('send_2', 1, 'Non envoyée', 'cible', '', 0, 0),
('send_2', 2, 'Not sent', 'cible', '', 0, 0),
('send_3', 1, 'À envoyer à tous', 'cible', '', 0, 0),
('send_3', 2, 'To send to everyone', 'cible', '', 0, 0),
('form_label_view_module', 1, 'Vue du module', 'cible', '', 0, 0),
('form_select_option_view_module_inscription', 1, 'Inscription', 'cible', '', 0, 0),
('form_select_option_view_module_inscription', 2, 'Registration', 'cible', '', 0, 0),
('footer_copyright', 1, '<p>&copy; %%%GET_DATE_YEAR%%%, %%%SITE_NAME%%% - Tous droits r&eacute;serv&eacute;s </p>', 'client', 'Texte du pied de page', 1, 0),
('footer_copyright', 2, '<p>&copy; %%%GET_DATE_YEAR%%%, %%%SITE_NAME%%% - All right reserved</p>', 'client', 'Texte du pied de page', 1, 0),
('footer_cible_realisation', 1, 'Conception Web : <a href="http://www.ciblesolutions.com" class="cibleLink" target="_blank">CIBLE</a>', 'client', '', 0, 0),
('footer_cible_realisation', 2, 'Web Design : <a href="http://www.ciblesolutions.com" class="cibleLink" target="_blank">CIBLE</a>', 'client', '', 0, 0),
('module_search', 1, 'Rechercher', 'cible', '', 0, 0),
('module_search', 2, 'Search', 'cible', '', 0, 0),
('search_module_name', 1, 'Recherche', 'cible', '', 0, 0),
('search_module_name', 2, 'Rechercher', 'cible', '', 0, 0),
('form_select_option_view_default_index', 1, 'Défaut', 'cible', '', 0, 0),
('form_select_label_associated_view', 1, 'Vue du module', 'cible', '', 0, 0),
('items_under_menu', 1, '<div class="clear_both spacerXLarge"></div>\n        <div id="under-menu-content">\n            <div id="box-membres-partenaires">\n                <div id="box-conditions-formulaire"><a href="%BASE_URL%/%BECOME_MEMBER_URL%">DEVENIR MEMBRE</a></div>\n                \n                <div class="clear_both spacerSmall"></div>\n                <div id="box-conditions-formulaire"><a href="%BASE_URL%/%CONDITIONS_URL%">Conditions d''admissibilité</a></div>\n            </div>\n        </div>\n        \n        <div class="clear_both spacerMedium"></div>\n        <div id="box-gallery-photos">\n            <a href="%BASE_URL%/%GALLERY_URL%">GALERIE PHOTOS</a>\n            <div><a href="%BASE_URL%/%GALLERY_URL%"><img src="%BASE_URL%/themes/default/images/common/under-menu-gallery-photo.jpg" border="0" alt="" /></a></div>\n            <div id="box-see-gallery"><a href="%BASE_URL%/%GALLERY_URL%">Voir les galeries photos</a></div>\n            \n            <div class="clear_both spacerSmall"></div>\n            <div id="under-menu-spacer-dot-share" class="clear_both"></div>\n        </div>\n\n <div class="clear_both spacerMedium"></div>\n        <div id="box-newsletter">\n            <a href="%BASE_URL%/%NEWSLETTER_URL%">INFOLETTRE</a>\n            <div class="links"><a href="%BASE_URL%/%NEWSLETTER_URL%">Inscription</a></div>\n            \n            <div class="clear_both spacerSmall"></div>\n            <div id="under-menu-spacer-dot-share" class="clear_both"></div>\n        </div>', 'client', '', 0, 0),
('button_save_draft', 1, 'Sauvegarder le brouillon', 'cible', '', 0, 0),
('form_extranet_group_status_active', 1, 'Ce groupe est actif.', 'cible', '', 0, 0),
('form_extranet_group_status_active', 2, 'This group is active', 'cible', '', 0, 0),
('forms_become_partner_label_name', 1, 'Nom', 'client', '', 0, 0),
('forms_become_partner_label_enterprise', 1, 'Entreprise', 'client', '', 0, 0),
('forms_become_partner_label_service', 1, 'Service', 'client', '', 0, 0),
('forms_become_partner_label_phone', 1, 'Téléphone', 'client', '', 0, 0),
('forms_become_partner_label_email', 1, 'Adresse courriel', 'client', '', 0, 0),
('forms_become_partner_label_conditions', 1, 'Je confirme avoir lu les conditions d''admissibilité.', 'client', '', 0, 0),
('form_select_option_view_forms_become_partner', 1, 'Devenir partenaire', 'cible', '', 0, 0),
('become_member_thank_you', 1, 'Merci', 'client', '', 0, 0),
('become_partner_thank_you', 1, 'Merci', 'client', '', 0, 0),
('validation_message_confirmation', 1, 'Vous devez confirmer que vous avez bien lu les conditions d''admissibilité.', 'cible', '', 0, 0),
('share_print_text', 1, 'Imprimer', 'client', '', 0, 0),
('share_print_text', 2, 'Print', 'client', '', 0, 0),
('share_share_text', 1, 'Partager', 'client', '', 0, 0),
('share_share_text', 2, 'Share', 'client', '', 0, 0),
('form_check_label_online', 1, 'Cocher pour mettre en ligne', 'cible', '', 0, 0),
('form_check_label_online', 2, 'Check to put online', 'cible', '', 0, 0),
('form_extranet_group_status_inactive', 1, 'Ce groupe est inactif.', 'cible', '', 0, 0),
('form_extranet_group_status_inactive', 2, 'This group is inactive', 'cible', '', 0, 0),
('dashboard_administration_profil_list_description', 2, 'Management of the registered users', 'cible', '', 0, 0),
('form_label_text_draft', 1, 'Texte version brouillon', 'cible', '', 0, 0),
('form_label_text_draft', 2, 'Draft text version', 'cible', '', 0, 0),
('button_compare_text', 1, 'Comparer brouillon/en ligne', 'cible', '', 0, 0),
('button_compare_text', 2, 'Compare draft / online', 'cible', '', 0, 0),
('button_preview_text', 1, 'Prévisualiser le brouillon', 'cible', '', 0, 0),
('button_preview_text', 2, 'Preview draft', 'cible', '', 0, 0),
('manage_block_contents', 1, 'Mettre à jour le contenu de ce bloc', 'cible', '', 0, 0),
('manage_block_contents', 2, 'Update the contents of this block', 'cible', '', 0, 0),
('list_column_BI_BlockTitle', 1, 'Titre du texte', 'cible', '', 0, 0),
('text_manage_block_contents', 1, 'Modifier le texte', 'cible', '', 0, 0),
('text_manage_block_contents', 2, 'Modify the text', 'cible', '', 0, 0),
('button_save_draft', 2, 'Save draft', 'cible', '', 0, 0),
('manage_content', 1, 'Gérer les contenus de cette page', 'cible', '', 0, 0),
('manage_content', 2, 'Manage page content', 'cible', '', 0, 0),
('manage_structure', 1, 'Gérer la structure de cette page', 'cible', '', 0, 0),
('manage_structure', 2, 'Manage page structure', 'cible', '', 0, 0),
('manage_block_online_status', 1, 'En ligne', 'cible', '', 0, 0),
('manage_block_online_status', 2, 'Online', 'cible', '', 0, 0),
('button_modify_existing_Profile', 1, 'Modifier le profil de l''usager existant', 'cible', '', 0, 0),
('button_search_another_profile_email', 1, 'Recommencer avec une autre adresse courriel', 'cible', '', 0, 0),
('profile_message_email_already_exists', 1, 'Cette adresse courriel est déjà utilisée par un usager.', 'cible', '', 0, 0),
('button_continue', 1, 'Continuer', 'cible', '', 0, 0),
('form_label_date', 1, 'Date', 'cible', '', 0, 0),
('form_label_date', 2, 'Date', 'cible', '', 0, 0),
('dashboard_administration_utilities_googleAnalytics', 1, 'Statistiques d''audience Internet', 'cible', '', 0, 0),
('dashboard_administration_website_reindexing', 1, 'Réindexation des contenus', 'cible', '', 0, 0),
('utilities_googleAnalytics_account', 1, 'Google Analytics', 'cible', '', 0, 0),
('utilities_googleAnalytics_username', 1, 'Nom d''utilisateur', 'cible', '', 0, 0),
('utilities_googleAnalytics_password', 1, 'Mot de passe', 'cible', '', 0, 0);

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('utilities_googleAnalytics_access_link', 1, 'Accéder à Google Analytics maintenant', 'cible', '', 0, 0),
('utilities_googleAnalytics_title', 1, 'Statistiques d''audience Internet', 'cible', '', 0, 0),
('utilities_googleAnalytics_description', 1, 'Cette page vous permet d''accéder à votre outil de statistiques d''audience Internet.', 'cible', '', 0, 0),
('header_reindexing_text', 1, 'Réindexation des contenus', 'cible', '', 0, 0),
('header_reindexing_description', 1, 'Cette page vous permet de réindexer tous les contenus texte de votre site Internet. Lors de la réindexation, il est important de ne pas quitter cette page avant la fin de l''exécution.', 'cible', '', 0, 0),
('reindexing_message_confirmation', 1, 'Désirez-vous faire la réindexation des contenus de votre site Web?', 'cible', '', 0, 0),
('list_column_ND_Date', 1, 'Date', 'cible', '', 0, 0),
('form_label_menu_type_page', 1, 'Page', 'cible', '', 0, 0),
('form_label_menu_type_placeholder', 1, 'Menu déroulant', 'cible', '', 0, 0),
('form_label_menu_type_external', 1, 'Lien externe', 'cible', '', 0, 0),
('menu_bottom', 1, 'Menu du bas', 'cible', '', 0, 0),
('validation_message_int_field', 2, 'This field must contain a numeric value', 'cible', '', 0, 0),
('forms_module_name', 1, 'Formulaire de contact', 'cible', '', 0, 0),
('forms_module_name', 2, 'Contact Form', 'cible', '', 0, 0),
('form_select_option_view_forms_become_member', 1, 'Devenir membre', 'cible', '', 0, 0),
('forms_become_member_heard_ad', 1, 'publicité', 'client', '', 0, 0),
('forms_become_member_heard_bank', 1, 'par mon banquier', 'client', '', 0, 0),
('forms_become_member_heard_friend', 1, 'par un ami/une connaissance/un membre de la famille', 'client', '', 0, 0),
('forms_become_member_heard_other', 1, 'Autre', 'client', '', 0, 0),
('forms_become_member_heard_website', 1, 'site Internet', 'client', '', 0, 0),
('forms_become_member_label_become_member', 1, 'Je suis intéressé à devenir membre :', 'client', '', 0, 0),
('forms_become_member_label_enterprise', 1, 'Entreprise :', 'client', '', 0, 0),
('forms_become_member_label_heard_from', 1, 'Comment avez-vous entendu parler du Groupement? :', 'client', '', 0, 0),
('forms_become_member_label_message', 1, 'Message (facultatif) :', 'cible', '', 0, 0),
('forms_become_member_label_more_info', 1, 'J''aimerais obtenir plus d''information :', 'client', '', 0, 0),
('forms_become_member_label_name', 1, 'Nom', 'client', '', 0, 0),
('forms_become_member_label_other', 1, 'Préciser :', 'client', '', 0, 0),
('forms_become_member_label_phone', 1, 'Téléphone :', 'client', '', 0, 0),
('forms_become_member_label_region', 1, 'Régions :', 'client', '', 0, 0),
('forms_become_member_regions_abitibi_temiscamingue', 1, 'Abitibi-Témiscamingue', 'client', '', 0, 0),
('forms_become_member_regions_autre', 1, 'Autre', 'client', '', 0, 0),
('forms_become_member_regions_bas_saint_laurent', 1, 'Bas Saint-Laurent', 'client', '', 0, 0),
('forms_become_member_regions_capitale_nationale', 1, 'Capitale-Nationale', 'client', '', 0, 0),
('forms_become_member_regions_centre_du_quebec', 1, 'Centre-du-Québec', 'client', '', 0, 0),
('forms_become_member_regions_chaudiere_appalaches', 1, 'Chaudière-Appalaches', 'client', '', 0, 0),
('forms_become_member_regions_cote_nord', 1, 'Côte-Nord', 'client', '', 0, 0),
('forms_become_member_regions_estrie', 1, 'Estrie', 'client', '', 0, 0),
('forms_become_member_regions_europe', 1, 'Europe', 'client', '', 0, 0),
('forms_become_member_regions_gaspesie_iles_de_la_madeleine', 1, 'Gaspésie?Îles-de-la-Madeleine', 'client', '', 0, 0),
('forms_become_member_regions_lanaudiere', 1, 'Lanaudière', 'client', '', 0, 0),
('forms_become_member_regions_laurentides', 1, 'Laurentides', 'client', '', 0, 0),
('forms_become_member_regions_laval', 1, 'Laval', 'client', '', 0, 0),
('forms_become_member_regions_mauricie', 1, 'Mauricie', 'client', '', 0, 0),
('forms_become_member_regions_monteregie', 1, 'Montérégie', 'client', '', 0, 0),
('forms_become_member_regions_montreal', 1, 'Montréal', 'client', '', 0, 0),
('forms_become_member_regions_nord_du_quebec', 1, 'Nord-du-Québec', 'client', '', 0, 0),
('forms_become_member_regions_nouveau_brunswick', 1, 'Nouveau-Brunswick', 'client', '', 0, 0),
('forms_become_member_regions_outaouais', 1, 'Outaouais', 'client', '', 0, 0),
('forms_become_member_regions_saguenay_lac_saint_jean', 1, 'Saguenay?Lac-Saint-Jean', 'client', '', 0, 0),
('validation_message_int_field', 1, 'Ce champ doit contenir une valeur numérique.', 'cible', '', 0, 0),
('categories_2_delete_errror_message', 1, 'Vous ne pouvez supprimer la catégorie ''%CATEGORY_NAME%'', car elle est encore utilisée.', 'cible', '', 0, 0),
('categories_2_delete_page_title', 1, 'Suppression d''une catégorie de nouvelles', 'cible', '', 0, 0),
('categories_categories_2_delete_page_description', 1, 'Cette page vous permet de supprimer la catégorie sélectionnée.', 'cible', '', 0, 0),
('categories_2_delete_message_confirmation', 1, 'Voulez-vous vraiment supprimer la catégorie ''%CATEGORY_NAME%''?', 'cible', '', 0, 0),
('categories_7_delete_page_title', 1, 'Suppression d''une catégorie d''événements', 'cible', '', 0, 0),
('categories_categories_7_delete_page_description', 1, 'Cette page vous permet de supprimer la catégorie sélectionnée.', 'cible', '', 0, 0),
('reindexing_message_execution1', 1, 'Suppression de l''indexation en cours...', 'cible', '', 0, 0),
('reindexing_message_execution1', 2, 'Remove indexing in progress ...', 'cible', '', 0, 0),
('form_label_menu_destination_link', 1, 'Destination (URL) ', 'cible', '', 0, 0),
('form_label_menu_destination_page', 1, 'Destination (sélectionnez la page) ', 'cible', '', 0, 0),
('reindexing_message_execution2', 1, 'Suppression terminée', 'cible', '', 0, 0),
('reindexing_message_execution2', 2, 'Complete', 'cible', '', 0, 0),
('reindexing_message_execution3', 1, 'Réindexation en cours... Cette opération peut prendre quelques minutes.', 'cible', '', 0, 0),
('reindexing_message_execution3', 2, 'Re-indexing in progress ... (this may take a few minutes)', 'cible', '', 0, 0),
('reindexing_message_execution4', 1, 'Réindexation terminée', 'cible', '', 0, 0),
('reindexing_message_execution4', 2, 'Re-indexing is complete', 'cible', '', 0, 0),
('page_menu_title', 1, 'Ajout/modification d''un élément de menu', 'cible', '', 0, 0),
('page_menu_description', 1, 'Cette page vous permet d''ajouter ou de modifier un élément de menu.', 'cible', '', 0, 0),
('reindexing_message_inprogress', 1, 'Réindexation en cours... Ne pas quitter cette page avant l''apparition du bouton <b>Retour</b>.', 'cible', '', 0, 0),
('reindexing_message_inprogress', 2, 'Re-indexing in progress (do not leave this page before the release of the "back" button)', 'cible', '', 0, 0),
('categories_7_delete_errror_message', 1, 'Vous ne pouvez supprimer la catégorie ''%CATEGORY_NAME%'', car elle est encore utilisée.', 'cible', '', 0, 0),
('categories_7_delete_message_confirmation', 1, 'Voulez-vous vraiment supprimer la catégorie ''%CATEGORY_NAME%''?', 'cible', '', 0, 0),
('categories_8_delete_page_title', 1, 'Suppression d''une infolettre', 'cible', '', 0, 0),
('categories_categories_8_delete_page_description', 1, 'Cette page vous permet de supprimer l''infolettre sélectionnée.', 'cible', '', 0, 0),
('categories_8_delete_errror_message', 1, 'Vous ne pouvez supprimer l''infolettre ''%CATEGORY_NAME%'', car elle est encore utilisée.', 'cible', '', 0, 0),
('categories_8_delete_message_confirmation', 1, 'Voulez-vous vraiment supprimer l''infolettre ''%CATEGORY_NAME%''?', 'cible', '', 0, 0),
('form_check_label_show_title', 1, 'Afficher le titre de la page', 'cible', '', 0, 0),
('form_select_option_pageLayouts_1', 1, 'Accueil', 'cible', '', 0, 0),
('form_select_option_pageLayouts_1', 2, 'Home', 'cible', '', 0, 0),
('form_select_option_pageLayouts_2', 1, 'Commun', 'cible', '', 0, 0),
('form_select_option_pageLayouts_2', 2, 'Common', 'cible', '', 0, 0),
('form_select_option_pageLayouts_3', 1, 'Mobile', 'cible', '', 0, 0),
('form_select_option_pageLayouts_3', 2, 'Mobile', 'cible', '', 0, 0),
('form_select_option_zoneViews_1', 1, 'Accueil', 'cible', '', 0, 0),
('form_select_option_zoneViews_2', 1, 'Commun', 'cible', '', 0, 0),
('menu_header', 1, 'Menu haut, Principal', 'cible', '', 0, 0),
('home_page_link_title', 1, '%%%SITE_NAME%%%', 'client', 'Attribut "Alt" du logo principal', 1, 0),
('home_page_link_title', 2, '%%%SITE_NAME%%%', 'client', 'Attribut "Alt" du logo principal', 1, 0),
('button_add_collection', 1, 'Ajouter une collection', 'cible', '', 0, 0),
('button_add_collection', 2, 'Add a collection', 'cible', '', 0, 0),
('dashboard_administration_static_text_title', 1, 'Textes statiques', 'cible', '', 0, 0),
('dashboard_administration_static_text_title', 2, 'Static texts', 'cible', '', 0, 0),
('dashboard_administration_static_text_description', 1, 'Libellés et autres textes', 'cible', '', 0, 0),
('dashboard_administration_static_text_description', 2, 'Management of the static texts', 'cible', '', 0, 0),
('dashboard_administration_references_description', 1, 'Listes de valeurs', 'cible', '', 0, 0),
('dashboard_administration_references_description', 2, 'References texts', 'cible', '', 0, 0),
('header_list_static_text_text', 1, 'Liste des textes statiques en français', 'cible', '', 0, 0),
('header_list_static_text_text', 2, 'List of the English static text', 'cible', '', 0, 0),
('header_list_static_text_description', 1, 'Cette page vous permet de consulter la liste des textes statiques français.<br /><br />Vous pouvez éditer la valeur française et anglaise.', 'cible', '', 0, 0),
('header_list_static_text_description', 2, 'This page gives a list of static texts English. <br /> <br /> You can edit the value of French and English.', 'cible', '', 0, 0),
('list_column_ST_Desc_backend', 1, 'Nom du texte statique', 'cible', '', 0, 0),
('list_column_ST_Desc_backend', 2, 'Name of the static text', 'cible', '', 0, 0),
('list_column_ST_Value', 1, 'Valeur du texte statique', 'cible', '', 0, 0),
('list_column_ST_Value', 2, 'Static text value', 'cible', '', 0, 0),
('header_edit_static_text_text', 1, 'Édition d''un texte statique', 'cible', '', 0, 0),
('header_edit_static_text_text', 2, 'Static text edition', 'cible', '', 0, 0),
('header_edit_static_text_description', 1, 'Cette page vous permet de modifier la valeur d''un texte statique.', 'cible', '', 0, 0),
('header_edit_static_text_description', 2, 'This page allows you to modify the value of the static text', 'cible', '', 0, 0),
('list_column_ST_LangID', 1, 'Langue du texte statique', 'cible', '', 0, 0),
('list_column_ST_LangID', 2, 'Static text language', 'cible', '', 0, 0),
('form_select_option_view_forms_forms_contact', 1, 'Formulaire - Nous joindre', 'cible', '', 0, 0),
('form_select_option_view_forms_forms_demandes_informations', 1, 'Formulaire - Demande d''information', 'cible', '', 0, 0),
('form_select_option_view_forms_forms_dons', 1, 'Formulaire - Dons', 'cible', '', 0, 0),
('form_select_option_view_forms_forms_inscriptions_ateliers', 1, 'Formulaire - Inscription aux ateliers', 'cible', '', 0, 0),
('form_select_option_position_below', 1, 'En-dessous de < %TEXT% >', 'cible', '', 0, 0),
('form_select_option_position_below', 2, 'Below < %TEXT% >', 'cible', '', 0, 0),
('form_label_phone_1', 1, 'Téléphone', 'cible', '', 0, 0),
('form_label_phone_1', 2, 'Phone number', 'cible', '', 0, 0),
('form_label_questions', 1, 'Message', 'cible', '', 0, 0),
('form_label_questions', 2, 'Message', 'cible', '', 0, 0),
('form_field_required_label', 1, 'Champs requis', 'cible', '', 0, 0),
('form_field_required_label', 2, 'Required fields', 'cible', '', 0, 0),
('form_label_explain_captcha', 1, 'Pour des raisons de sécurité, veuillez entrer les caractères alphanumériques de l''image dans l''espace ci-dessous.', 'cible', '', 0, 0),
('form_label_explain_captcha', 2, 'For safety measures, please enter the image alphanumeric characters (6) in the space below.', 'cible', '', 0, 0),
('validation_message_captcha_error', 1, 'Veuillez saisir la chaîne ci-dessus correctement.', 'cible', '', 0, 0),
('validation_message_captcha_error', 2, 'Captcha value is wrong.', 'cible', '', 0, 0),
('form_contact_sent_tank_you_message', 1, 'Votre message a été envoyé avec succès.<br />Merci de votre intérêt!', 'client', '', 0, 0),
('form_contact_sent_tank_you_message', 2, 'Your message was sent successfully.<br />Thank you!', 'client', '', 0, 0),
('form_label_phone_work', 1, 'Téléphone travail', 'cible', '', 0, 0),
('form_label_phone_home', 1, 'Téléphone résidence', 'cible', '', 0, 0),
('form_label_phone_home', 2, 'Téléphone résidence', 'cible', '', 0, 0),
('form_label_phone_work', 2, 'Téléphone travail', 'cible', '', 0, 0),
('form_label_address', 1, 'Adresse', 'cible', '', 0, 0),
('form_label_address', 2, 'Adresse', 'cible', '', 0, 0),
('form_label_code_postal', 1, 'Code postal', 'cible', '', 0, 0),
('form_label_code_postal', 2, 'Code postal', 'cible', '', 0, 0),
('form_label_fname_lname', 1, 'Nom et prénom', 'cible', '', 0, 0),
('form_label_fname_lname', 2, 'Nom et prénom', 'cible', '', 0, 0),
('form_label_ville', 1, 'Ville', 'cible', '', 0, 0),
('form_label_ville', 2, 'Ville', 'cible', '', 0, 0),
('form_label_province', 1, 'Province', 'cible', '', 0, 0),
('form_label_province', 2, 'Province', 'cible', '', 0, 0),
('module_forms', 1, 'Formulaire', 'cible', '', 0, 0),
('module_forms', 2, 'Formulaire', 'cible', '', 0, 0),
('login_form_email_label', 1, 'Courriel', 'client', '', 0, 0),
('login_form_email_label', 2, 'Email', 'client', '', 0, 0),
('login_form_password_label', 1, 'Mot de passe', 'client', '', 0, 0),
('login_form_password_label', 2, 'Password', 'client', '', 0, 0),
('login_form_stayOn_label', 1, 'Rester connecté', 'cible', '', 0, 0),
('login_form_auth_fail_error', 1, 'Votre courriel ou votre mot de passe sont incorrects.', 'client', '', 0, 0),
('login_form_auth_fail_error', 2, 'Your email or password is incorrect.', 'client', '', 0, 0),
('login_form_stayOn_label', 2, 'Stay signed in', 'cible', '', 0, 0),
('lost_password_link', 1, 'Mot de passe oublié?', 'client', '', 0, 0),
('lost_password_link', 2, 'Forgot password?', 'client', '', 0, 0),
('login_form_create_account_title', 1, 'Vous n?êtes pas encore inscrit?', 'client', '', 0, 0),
('login_form_create_account_title', 2, 'Not yet registered?', 'client', '', 0, 0),
('login_form_create_account_button', 1, 'Inscrivez-vous', 'client', '', 0, 0),
('login_form_create_account_button', 2, 'Click here to register', 'client', '', 0, 0),
('login_form_login_title', 1, 'Vous avez déjà un compte', 'client', '', 0, 0),
('login_form_login_title', 2, 'You already have an account', 'client', '', 0, 0),
('button_edit', 2, 'Edit', 'cible', '', 0, 0),
('button_delete', 2, 'Delete', 'cible', '', 0, 0),
('menu_footer', 1, 'Menu de bas de page', 'cible', '', 0, 0),
('menu_footer', 2, 'Footer menu', 'cible', '', 0, 0),
('menu_permanent', 1, 'Menu permanent', 'cible', '', 0, 0),
('menu_permanent', 2, 'Permanent menu', 'cible', '', 0, 0),
('account_logout_link', 1, 'Déconnexion', 'client', '', 0, 0),
('account_logout_link', 2, 'Logout', 'client', '', 0, 0),
('account_modify_page_title', 1, 'Modifier votre compte', 'client', '', 0, 0),
('account_modify_page_title', 2, 'Modify your account', 'client', '', 0, 0),
('list_column_date', 1, 'Date', 'cible', '', 0, 0),
('list_column_date', 2, 'Date', 'cible', '', 0, 0),
('list_column_status', 1, 'Statut', 'cible', '', 0, 0),
('list_column_status', 2, 'Status', 'cible', '', 0, 0);
REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('form_label_subscribe', 1, 'Restez au courant! Inscrivez-vous pour recevoir l''information sur nos produits, nos événements et nos nouvelles. Nous respectons vos <a href="%URL_PRIVACY_POLICY%" target="_blank">renseignements personnels</a> et vous pouvez vous désabonner en tout temps.', 'cible', '', 0, 0),
('form_label_subscribe', 2, 'Stay in the loop. Sign up to receive the latest info on XXXXXXXXXX products, news and happenings. We strongly respect your <a href="%URL_PRIVACY_POLICY%" target="_blank">privacy</a> and you may unsubscribe at any time.', 'cible', '', 0, 0),
('form_label_salutation', 1, 'Salutation', 'cible', '', 0, 0),
('form_label_salutation', 2, 'Gender', 'cible', '', 0, 0),
('form_label_terms_agreement', 1, 'J''accepte la totalité de ces <a href="%URL_TERMS_CONDITIONS%" target="_blank">conditions</a>', 'client', '', 0, 0),
('form_label_terms_agreement', 2, 'J''accepte la totalité de ces <a href="%URL_TERMS_CONDITIONS%" target="_blank">conditions</a>', 'client', '', 0, 0),
('frontend_language_switcher_fr', 1, 'Français', 'client', 'Texte pour la langue fr', 0, 0),
('frontend_language_switcher_fr', 2, 'Français', 'client', 'Text for the fr language', 0, 0),
('frontend_language_switcher_en', 1, 'English', 'client', 'Texte pour la langue en', 0, 0),
('frontend_language_switcher_en', 2, 'English', 'client', 'Text for the en language', 0, 0),
('validated_notification_client_email_message', 1, '', 'client', 'Notification de validation de courriel envoyé à l''utilisateur qui se crée un compte', 0, 0),
('validated_notification_client_email_message', 2, '', 'client', 'Notification of validation email sent to the user who created an account', 0, 0),
('revalidated_notification_client_email_message', 1, '', 'client', 'Courriel de renvoi de confirmation', 0, 0),
('revalidated_notification_client_email_message', 2, '', 'client', 'Text for the Email adress confirmation', 0, 0),
('need_confirm_email_text', 1, '<p> En quelques minutes, vous recevrez un message électronique contenant un lien à utiliser pour confirmer votre adresse e-mail et activer votre compte. </ p><p>Remarques : Vous pouvez vérifier le dossier spam de votre compte de messagerie. De temps en temps, filtres anti-spam classent de façon erronée les emails valides comme spam. </ P>', 'client', 'Texte pour indiquer à l''utilisateur qu''il va recevoir un courriel', 0, 0),
('need_confirm_email_text', 2, '<p>In a few minutes, you will receive an email message containing a link to use to confirm your email address and activate your account.</p>\n <p>Notes : You may check the spam folder in your email account. Occasionally, email spam filters incorrectly classify valid emails as spam.</p>', 'client', 'Text for the user notification email', 0, 0),
('cart_need_confirm_email_text', 1, 'Confirmer votre courriel.', 'client', 'Texte indiquant au client de confirmer son courriel', 0, 0),
('cart_need_confirm_email_text', 2, 'Please confirm your email.', 'client', 'Text indicating the customer to confirm his email ', 0, 0),
('terms_agreement_error_message', 1, '* Vous devez accepter les conditions', 'client', 'Courriel de renvoi de confirmation', 0, 0),
('terms_agreement_error_message', 2, '* You must agree with the terms.', 'client', 'Text for the Email adress confirmation', 0, 0),
('validation_message_email_already_exists', 1, 'Un compte existe déjà pour ce courriel.', 'client', 'Courriel existe déjà', 0, 0),
('validation_message_email_already_exists', 2, 'There is already an account registered for this email address.', 'client', 'Email already exists', 0, 0),
('dashboard_administration_website_reindexing', 2, 'Content reindexing', 'cible', '', 0, 0),
('dashboard_administration_utilities_googleAnalytics', 2, 'Content reindexing', 'cible', '', 0, 0),
('menu_footer_simple', 1, 'Menu pied de page [1 niveau]', 'cible', '', 0, 0),
('menu_footer_simple', 2, 'Menu pied de page [1 niveau]', 'cible', '', 0, 0),
('menu_footer_double', 1, 'Menu pied de page [2 niveau]', 'cible', '', 0, 0),
('menu_footer_double', 2, 'Menu pied de page [2 niveau]', 'cible', '', 0, 0),
('form_select_default_label', 1, '-- Sélectionner --', 'cible', '', 0, 0),
('form_select_default_label', 2, '-- Select --', 'cible', '', 0, 0),
('dashboard_administration_utilities_references', 1, 'Données de références', 'cible', '', 0, 0),
('dashboard_administration_utilities_references', 2, 'Refernces data', 'cible', '', 0, 0),
('search_multiple_results_text', 1, 'résultats trouvés pour ', 'cible', '', 0, 0),
('search_multiple_results_text', 2, 'results found for ', 'cible', '', 0, 0),
('search_single_result_text', 1, 'résultat trouvé pour ', 'cible', '', 0, 0),
('search_single_result_text', 2, 'result found for ', 'cible', '', 0, 0),
('manage_block_secured_status', 1, 'Affiché le bloc', 'cible', '', 0, 0),
('manage_block_secured_status', 2, 'Display block', 'cible', '', 0, 0),
('manage_block_secured_menu_status', 1, 'Affiché seulement si connecté', 'cible', '', 0, 0),
('manage_block_secured_menu_status', 2, 'Display only if logged in', 'cible', '', 0, 0),
('manage_block_secured_none', 1, 'Toujours', 'cible', '', 0, 0),
('manage_block_secured_none', 2, 'Always', 'cible', '', 0, 0),
('manage_block_secured_logged', 1, 'Si connecté', 'cible', '', 0, 0),
('manage_block_secured_logged', 2, 'if logged', 'cible', '', 0, 0),
('manage_block_secured_notlog', 1, 'si non connecté', 'cible', '', 0, 0),
('manage_block_secured_notlog', 2, 'if not logged', 'cible', '', 0, 0),
('email_notification_footer', 1, 'Ce courriel vous a été envoyé par ##SITE-NAME##', 'client', 'Notification email: footer en français.', 1, 0),
('email_notification_footer', 2, 'This email was send to you by ##SITE-NAME##.', 'client', 'Notification email: footer en anglais.', 1, 0),
('label_number_to_show', 1, 'Nombre à afficher :', 'cible', '', 0, 0),
('label_number_to_show', 2, 'Number to show : ', 'cible', '', 0, 0),
('label_view', 1, 'Vue : ', 'cible', '', 0, 0),
('label_view', 2, 'View : ', 'cible', '', 0, 0),
('label_show_brief_text', 1, 'Afficher le texte bref', 'cible', '', 0, 0),
('label_show_brief_text', 2, 'Show brief text', 'cible', '', 0, 0),
('label_order_display', 1, 'Ordre d''affichage', 'cible', '', 0, 0),
('label_order_display', 2, 'Display view', 'cible', '', 0, 0),
('label_date_desc', 1, 'Dates décroissantes', 'cible', '', 0, 0),
('label_date_desc', 2, 'Date descending', 'cible', '', 0, 0),
('label_date_asc', 1, 'Dates croissantes', 'cible', '', 0, 0),
('label_date_asc', 2, 'Date ascending', 'cible', '', 0, 0),
('label_alpha_asc', 1, 'Titre alphabéthique', 'cible', '', 0, 0),
('label_alpha_asc', 2, 'Title alphabetical', 'cible', '', 0, 0),
('label_administrator_actives', 1, 'Groupes d''administrateurs actifs', 'cible', '', 0, 0),
('label_administrator_actives', 2, 'Actives administrators group', 'cible', '', 0, 0),
('label_static_text_edition', 1, 'Édition d''un texte statique', 'cible', '', 0, 0),
('label_static_text_edition', 2, 'Static text edition', 'cible', '', 0, 0),
('label_titre_page', 1, 'Titre de la page (balise H1)', 'cible', '', 0, 0),
('label_titre_page', 2, 'Page title (tag H1)', 'cible', '', 0, 0),
('label_index_already_exists', 1, 'Cet index existe déjà.', 'cible', '', 0, 0),
('label_index_already_exists', 2, 'This index already exists', 'cible', '', 0, 0),
('label_index_reserved', 1, 'Cet index est réservé.', 'cible', '', 0, 0),
('label_index_reserved', 2, 'This index already existed', 'cible', '', 0, 0),
('label_name_controller', 1, 'Nom du contrôleur (doit être unique)', 'cible', '', 0, 0),
('label_name_controller', 2, 'Name of the controller (must be unique)', 'cible', '', 0, 0),
('label_index_more_char', 1, 'L''index doit contenir plus de %min% charactères', 'cible', '', 0, 0),
('label_index_more_char', 2, 'The index must contains at least %min% characters', 'cible', '', 0, 0),
('label_index_less_char', 1, 'L''index doit contenir moins de %max% charactères', 'cible', '', 0, 0),
('label_index_less_char', 2, 'The index cannot be longer than %max% characters', 'cible', '', 0, 0),
('label_description_meta', 1, 'Description (meta)', 'cible', '', 0, 0),
('label_description_meta', 2, 'Description (meta)', 'cible', '', 0, 0),
('label_keywords_meta', 1, 'Mots-clés (meta)', 'cible', '', 0, 0),
('label_keywords_meta', 2, 'Keywords (meta)', 'cible', '', 0, 0),
('label_other_meta', 1, 'Autres (meta)', 'cible', '', 0, 0),
('label_other_meta', 2, 'Others (meta)', 'cible', '', 0, 0),
('label_layout_page', 1, 'Layout de la page', 'cible', '', 0, 0),
('label_layout_page', 2, 'Layout of the page', 'cible', '', 0, 0),
('label_model_page', 1, 'Modèle de la page', 'cible', '', 0, 0),
('label_model_page', 2, 'Model of the page', 'cible', '', 0, 0),
('label_only_character_allowed', 1, 'L''index peut contenir seulement des caractères alphabétiques (a-z) et numéric (0-9). Utiliser le caractère ''-'' pour séparer 2 mots', 'cible', '', 0, 0),
('label_only_character_allowed', 2, 'The index can contain only alphabetic characters (az) and numeric (0-9). Use the ''-'' character to separate 2 words', 'cible', '', 0, 0),
('label_gestion_contents', 1, 'Gestion du contenu des pages (cocher les pages dont l''administrateur peut faire la gestion du contenu)', 'cible', '', 0, 0),
('label_gestion_contents', 2, 'Management structure of the pages (check the page to allow the administrator to modify its contents)', 'cible', '', 0, 0),
('label_gestion_structure', 1, 'Gestion de la structure des pages (cocher les pages dont l''administrateur peut faire la gestion de la structure)', 'cible', '', 0, 0),
('label_gestion_structure', 2, 'Management structure of the pages (check the page to allow the administrator to modify its structure)', 'cible', '', 0, 0),
('label_category', 1, 'Catégorie : ', 'cible', '', 0, 0),
('label_category', 2, 'Category : ', 'cible', '', 0, 0),
('menu_footerTwo', 1, 'Menu bas de page (colonne 2)', 'cible', '', 0, 0),
('menu_footerTwo', 2, 'Footer menu (column 2)', 'cible', '', 0, 0),
('menu_footerThree', 1, 'Menu bas de page (colonne 3)', 'cible', '', 0, 0),
('menu_footerThree', 2, 'Footer menu (column 3)', 'cible', '', 0, 0),
('menu_footerFour', 1, 'Menu bas de page (colonne 4)', 'cible', '', 0, 0),
('menu_footerFour', 2, 'Footer menu (column 4)', 'cible', '', 0, 0),
('header_list_header_image_text', 1, 'Liste des images d''entête', 'cible', '', 0, 0),
('header_list_header_image_text', 2, 'List of header images', 'cible', '', 0, 0),
('header_list_header_image_description', 1, 'Cette page vous permet de consulter la liste des images d''entête.', 'cible', '', 0, 0),
('header_list_header_image_description', 2, 'This page lists the header images.', 'cible', '', 0, 0),
('header_edit_header_image_text', 1, 'Édition d''une image d''entête', 'cible', '', 0, 0),
('header_edit_header_image_text', 2, 'Header image edition', 'cible', '', 0, 0),
('header_edit_header_image_description', 1, 'Cette page vous permet de modifier une image d''entête', 'cible', '', 0, 0),
('header_edit_header_image_description', 2, 'This page allows the modification of an header image', 'cible', '', 0, 0),
('dashboard_administration_header_image_title', 1, 'Images', 'cible', '', 0, 0),
('dashboard_administration_header_image_title', 2, 'Images', 'cible', '', 0, 0),
('dashboard_administration_header_image', 1, 'Images d''entête', 'cible', '', 0, 0),
('dashboard_administration_header_image', 2, 'Header Images', 'cible', '', 0, 0),
('search_in_catalog', 1, 'Afficher la liste de prduits correspondants.', 'cible', '', 0, 0),
('search_in_catalog', 2, 'Display the corresponding products list. ', 'cible', '', 0, 0),
('search_no_result_text', 1, 'Aucun résultat trouvé pour ', 'client', '', 0, 0),
('search_no_result_text', 2, 'No result found for ', 'client', '', 0, 0),
('form_label_menu_title_style', 1, 'Style spécifique pour ce menu', 'cible', '', 0, 0),
('form_label_menu_title_style', 2, 'Specific style for this item', 'cible', '', 0, 0),
('form_module_name', 1, 'Formulaires', 'cible', '', 0, 0),
('form_module_name', 2, 'Form', 'cible', '', 0, 0),
('management_module_form_list', 1, 'Liste des formulaires', 'cible', '', 0, 0),
('management_module_form_list', 2, 'Form list', 'cible', '', 0, 0);

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('dashboard_administration_utilities_references', 1, 'Données de références', 'cible', '', 0, 0),
('dashboard_administration_utilities_references', 2, 'Refernces data', 'cible', '', 0, 0),
('button_add_references', 1, 'Ajouter', 'cible', '', 0, 0),
('button_add_references', 2, 'Add', 'cible', '', 0, 0),
('form_reference_value_label', 1, 'Valeur', 'cible', '', 0, 0),
('form_reference_value_label', 2, 'Value', 'cible', '', 0, 0),
('form_reference_type_label', 1, 'Type', 'cible', '', 0, 0),
('form_reference_type_label', 2, 'Type', 'cible', '', 0, 0),
('form_reference_seq_label', 1, 'Séquence', 'cible', '', 0, 0),
('form_reference_seq_label', 2, 'Sequence', 'cible', '', 0, 0),
('header_list_references_text', 1, 'Liste des references', 'cible', '', 0, 0),
('header_list_references_text', 2, 'References list', 'cible', '', 0, 0),
('header_list_references_description', 1, 'Cette page permet de gérer la liste des references.', 'cible', '', 0, 0),
('header_list_references_description', 2, 'This page is to manage references', 'cible', '', 0, 0),
('header_add_references_text', 1, 'Ajouter', 'cible', '', 0, 0),
('header_add_references_text', 2, 'Add', 'cible', '', 0, 0),
('header_edit_references_text', 1, 'Modification', 'cible', '', 0, 0),
('header_edit_references_text', 2, 'Edit', 'cible', '', 0, 0),
('header_edit_references_description', 1, 'Cette page permet de modifier des informations de la référence sélectionnée.', 'cible', '', 0, 0),
('header_edit_references_description', 2, 'This page is to edit data of the selected reference.', 'cible', '', 0, 0),
('header_delete_references_text', 1, 'Suppression d''une référence', 'cible', '', 0, 0),
('header_delete_references_text', 2, 'Deletion of a reference', 'cible', '', 0, 0),
('header_add_references_description', 1, 'Renseigner les champs du formulaire.', 'cible', '', 0, 0),
('header_add_references_description', 2, 'Fill the form to add data.', 'cible', '', 0, 0),
('list_column_R_ID', 1, 'Id', 'cible', '', 0, 0),
('list_column_R_ID', 2, 'Id', 'cible', '', 0, 0),
('list_column_R_TypeRef', 1, 'Type', 'cible', '', 0, 0),
('list_column_R_TypeRef', 2, 'Type', 'cible', '', 0, 0),
('list_column_RI_Value', 1, 'Valeur', 'cible', '', 0, 0),
('list_column_RI_Value', 2, 'Value', 'cible', '', 0, 0),
('list_column_R_Seq', 1, "Ordre d'affichage", "cible", "", 0, 0),
('list_column_R_Seq', 2, "Display order", "cible", "", 0, 0),
('form_enum_subscrArg', 1, 'Abonnement', 'cible', '', 0, 0),
('form_enum_subscrArg', 2, 'Subscribe', 'cible', '', 0, 0),
('form_label_menu_show_item', '1', 'Afficher cet item du menu', 'cible', '', '0', '0'),
('form_label_menu_show_item', '2', 'Show this menu item', 'cible', '', '0', '0'),
('form_label_menu_show_item_in_sitemap', '1', 'Afficher cet item du menu dans le plan du site', 'cible', '', '0', '0'),
('form_label_menu_show_item_in_sitemap', '2', 'Show this menu item in sitemap', 'cible', '', '0', '0'),
('form_enum_unsubscrArg', 1, 'Raison désabonnement', 'cible', '', 0, 0),
('form_enum_unsubscrArg', 2, 'Unsubscription', 'cible', '', 0, 0),
('label_altFirstImage', '1', 'ALT de la première image', 'cible', '', '0', '0'),
('label_altFirstImage', '2', 'ALT of the first image', 'cible', '', '0', '0'),
('profile_tab_title_general', '1', 'Général', 'cible', '', '0', '0'),
('profile_tab_title_general', '2', 'General', 'cible', '', '0', '0'),
('profile_tab_title_log', '1', 'Journal', 'cible', '', '0', '0'),
('profile_tab_title_log', '2', 'Log', 'cible', '', '0', '0'),
('profile_delete_alert_existing_profiles', '1', "Cet utilsateur possède ##NBPROFILE## profil(s) actif(s). <br />
La suppression de l'utilisateur est <strong>DÉFINITIVE</strong>. <strong>Toutes les données relatives aux profils seront perdues.</strong> <br /><br />
Profil(s) associé(s):<br />
##PROFILESLIST##<br />
Pour supprimer un profil en particulier, passez par l'édition des profil.", 'cible', '', '0', '20'),
('profile_delete_alert_existing_profiles', '2', "This user has ##NBPROFILE## active profile(s). <br />
The removal of the user is <strong>FINAL</strong>. <strong>All profile data will be lost.</strong> <br />
Releated profile(s):<br />
##PROFILESLIST##<br />
To delete a profile, in particular, go through the profile editing.", 'cible', '', '0', '20'),
('form_legend_infoPage', '1', 'Info page', 'cible', '', '0', '1'),
('form_legend_infoPage', '2', 'Page info', 'cible', '', '0', '1'),
('form_legend_blockData', '1', 'Texte', 'cible', '', '0', '1'),
('form_legend_blockData', '2', 'Text', 'cible', '', '0', '1'),
('list_column_GP_LastName', '1', 'Nom', 'cible', '', '0', '0'),
('list_column_GP_LastName', '2', 'Name', 'cible', '', '0', '0'),
('list_column_GP_FirstName', '1', 'Prénom', 'cible', '', '0', '0'),
('list_column_GP_FirstName', '2', 'Firstname', 'cible', '', '0', '0'),
('list_column_GP_Email', '1', 'Courriel', 'cible', '', '0', '0'),
('list_column_GP_Email', '2', 'Email', 'cible', '', '0', '0'),
('captcha_label', 1, "Pour des raisons de sécurité, veuillez entrer les caractères alphanumériques de l'image dans l'espace ci-dessous.", 'cible', '', 0, 0),
('captcha_label', 2, 'For security reasons, please enter the alphanumeric characters from the image into the space below.', 'cible', '', 0, 0);

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('dashboard_administration_video_description', '1', 'Liste de vidéos', 'cible', '', '0', 21),
('dashboard_administration_video_description', '2', 'List of videos', 'cible', '', '0', 21),
('list_column_V_Alias', '1', 'Alias', 'cible', '', '0', 21),
('list_column_V_Alias', '2', 'Alias', 'cible', '', '0', 21),
('list_column_VI_Description', '1', 'Description', 'cible', '', '0', 21),
('list_column_VI_Description', '2', 'Description', 'cible', '', '0', 21),
('header_list_video_text', '1', 'Liste des vidéos', 'cible', '', '0', 21),
('header_list_video_text', '2', 'List of video', 'cible', '', '0', 21),
('header_list_video_description', '1', 'Cliquez sur Ajouter une video pour
créer une nouvelle.<br />
Vous pouvez rechercher par mots-clés,
la liste des videos. Pour revenir à la liste complète,
cliquez sur Voir la liste complète. <br />
Vous pouvez modifier ou supprimer une
video. ', 'cible', '', '0', 21),
('header_list_video_description', '2', 'Cliquez sur Ajouter une video pour
créer une nouvelle.<br />
Vous pouvez rechercher par mots-clés,
la liste des videos. Pour revenir à la liste complète,
cliquez sur Voir la liste complète.<br />
Vous pouvez modifier ou supprimer une
video. ', 'cible', '', '0', 21),
('dashboard_administration_video', '1', 'Vidéos', 'cible', '', '0', 21),
('dashboard_administration_video', '2', 'Videos', 'cible', '', '0', 21),
('header_add_video_description', '1', 'Cette page vous permet d''ajouter une vidéo ', 'cible', '', '0', 21),
('header_add_video_description', '2', 'Add a video to the video library. ', 'cible', '', '0', 21),
('extranet_video_autoPlay', '1', 'Autoplay', 'cible', '', '0', 21),
('extranet_video_autoPlay', '2', 'Autoplay', 'cible', '', '0', 21),
('form_label_video_autoplay', '1', 'Autoplay', 'cible', '', '0', 21),
('form_label_video_autoplay', '2', 'Autoplay', 'cible', '', '0', 21),
('button_add_video', '1', 'Ajouter une vidéo', 'cible', '', '0', 21),
('button_add_video', '2', 'Add a video', 'cible', '', '0', 21),
('form_label_video_alias', '1', 'Alias', 'cible', '', '0', 21),
('form_label_video_alias', '2', 'Alias', 'cible', '', '0', 21),
('form_label_video_name', '1', 'Nom', 'cible', '', '0', 21),
('form_label_video_name', '2', 'Name', 'cible', '', '0', 21),
('form_label_video_width', '1', 'Largeur', 'cible', '', '0', 21),
('form_label_video_width', '2', 'Width', 'cible', '', '0', 21),
('form_label_video_height', '1', 'Hauteur', 'cible', '', '0', 21),
('form_label_video_height', '2', 'Height', 'cible', '', '0', 21),
('form_label_video_description', '1', 'Description', 'cible', '', '0', 21),
('form_label_video_description', '2', 'Description', 'cible', '', '0', 21),
('form_label_video_poster', '1', 'Poster', 'cible', '', '0', 21),
('form_label_video_poster', '2', 'Poster', 'cible', '', '0', 21),
('form_label_video_VI_MP4', '1', 'Vidéo MP4', 'cible', '', '0', 21),
('form_label_video_VI_MP4', '2', 'Video MP4', 'cible', '', '0', 21),
('form_label_video_VI_WEBM', '1', 'Vidéo WEBM', 'cible', '', '0', 21),
('form_label_video_VI_WEBM', '2', 'Video WEBM', 'cible', '', '0', 21),
('form_label_video_VI_OGG', '1', 'Vidéo OGG', 'cible', '', '0', 21),
('form_label_video_VI_OGG', '2', 'Video OGG', 'cible', '', '0', 21),
('header_delete_video_text', '1', 'Suppression d''une vidéo', 'cible', '', '0', 21),
('header_delete_video_text', '2', 'Delete a video', 'cible', '', '0', 21),
('header_edit_video_text', '1', 'Edition d''une vidéo', 'cible', '', '0', 21),
('header_edit_video_text', '2', 'Edit a video', 'cible', '', '0', 21),
('header_edit_video_description', '1', 'Cette page permet d''éditer une vidéo', 'cible', '', '0', 21),
('header_edit_video_description', '2', 'This page allows the edition of a video', 'cible', '', '0', 21),
('video_module_name', '1', 'Vidéos', 'cible', '', '0', 21),
('video_module_name', '2', 'Videos', 'cible', '', '0', 21),
('management_module_video_list', '1', 'Liste des vidéos', 'cible', '', '0', 21),
('management_module_video_list', '2', 'Videos'' list', 'cible', '', '0', 21),
('header_add_video_text', '1', 'Ajouter une vidéo', 'cible', '', '0', 21),
('header_add_video_text', '2', 'Add a video', 'cible', '', '0', 21),
('label_imageReplaceVideo', '1', 'Image du milieu dans l''entête', 'cible', '', '0', '0'),
('label_imageReplaceVideo', '2', 'Header middle image', 'cible', '', '0', '0'),
('extranet_imageNone', '1', 'Sans image', 'cible', '', '0', '0'),
('extranet_imageNone', '2', 'Without image', 'cible', '', '0', '0'),
('label_headerVideo', '1', 'Vidéo dans l''entête', 'cible', '', '0', '0'),
('label_headerVideo', '2', 'Header video', 'cible', '', '0', '0'),
('label_imageEntete', '1', 'Image de l''entête', 'cible', '', '0', '0'),
('label_imageEntete', '2', 'Header image', 'cible', '', '0', '0');
REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, `ST_ModuleID`) VALUES
('label_homepage', 1, 'Accueil', 'cible', '', 0, 0),
('label_homepage', 2, 'Home', 'cible', '', 0, 0),
('label_site_type', 1, 'Page pour un site', 'cible', '', 0, 0),
('label_site_type', 2, 'Page to display on', 'cible', '', 0, 0),
('label_site_type_s', 1, 'Site standard', 'cible', '', 0, 0),
('label_site_type_s', 2, 'Standard website', 'cible', '', 0, 0),
('label_site_type_m', 1, 'Site mobile', 'cible', '', 0, 0),
('label_site_type_m', 2, 'Mobile website', 'cible', '', 0, 0),
('form_profile_delete_dialog_text', 1, 'Êtes-vous sûr de vouloir supprimer ce profil définitivement?', 'cible', '', 0, 20),
('form_profile_delete_dialog_text', 2, 'Are you sure you want to permanently delete this profile?', 'cible', '', 0, 20),
('form_profile_delete_dialog_title', 1, 'Confirmer la suppression?', 'cible', '', 0, 20),
('form_profile_delete_dialog_title', 2, 'Confirm delete action', 'cible', '', 0, 20);

REPLACE INTO `Static_Texts` (`ST_Identifier` ,`ST_LangID` ,`ST_Value` ,`ST_Type` ,`ST_Desc_backend` ,`ST_Editable` ,`ST_ModuleID`) VALUES
('form_label_firstAddress', 1, 'Adresse', 'cible', '', 0, 0),
('form_label_firstAddress', 2, 'Address', 'cible', '', 0, 0),
('form_label_secondAddress', 1, 'Adresse', 'cible', '', 0, 0),
('form_label_secondAddress', 2, 'Address', 'cible', '', 0, 0),
('form_label_zipCode', 1, 'Code postal', 'cible', '', 0, 0),
('form_label_zipCode', 2, 'Zip code', 'cible', '', 0, 0),
('form_label_fax', 1, 'Fax', 'cible', '', 0, 0),
('form_label_fax', 2, 'Fax', 'cible', '', 0, 0),
('form_label_webSite', 1, 'Site internet', 'cible', '', 0, 0),
('form_label_webSite', 2, 'Website', 'cible', '', 0, 0),
('form_label_firstTel', 1, 'Téléphone 1', 'cible', '', 0, 0),
('form_label_firstTel', 2, 'Phone 1', 'cible', '', 0, 0),
('form_label_secondTel', 1, 'Téléphone 2', 'cible', '', 0, 0),
('form_label_secondTel', 2, 'Phone 2', 'cible', '', 0, 0),
('lost_password_button', 1, 'Récupérer', 'client', '', 0,0),
('lost_password_button', 2, 'Retrieve', 'client', '', 0,0),
('form_account_subform_identification_legend', 1, 'Identification', 'cible', '', 0,  0),
('form_account_subform_identification_legend', 2, 'Authentication', 'cible', '', 0,  0),
('form_account_subform_addBilling_legend', 1, "Facturation", 'cible', '', 0,  0),
('form_account_subform_addBilling_legend', 2, 'Billing', 'cible', '', 0,  0),
('form_account_subform_addShipping_legend', 1, "Livraison", 'cible', '', 0,  0),
('form_account_subform_addShipping_legend', 2, 'Delivery', 'cible', '', 0,  0),
('form_label_select_city', 1, 'S&eacute;lectionnez une ville', 'cible', '', 0,  0),
('form_label_select_city', 2, 'Choose a city', 'cible', '', 0,  0),
('form_label_select_state', 1, 'S&eacute;lectionnez une province', 'cible', '', 0,  0),
('form_label_select_state', 2, 'Choose a province', 'cible', '', 0,  0),
('form_account_button_submit', 1, 'Créer mon compte', 'cible', '', 0,  0),
('form_account_button_submit', 2, 'Create may account', 'cible', '', 0,  0),
('form_label_confirmPwd', 1, 'Confirmer le mot de passe', 'cible', '', 0,  0),
('form_label_confirmPwd', 2, 'Confirm password', 'cible', '', 0,  0),
('lost_password_sent', 1, "Un nouveau mot de passe vous a été envoyé.", 'client', '', 0,  0),
('lost_password_sent', 2, 'We sent you a new password.', 'client', '', 0,  0),
('lost_password_email_not_found', 1, "Aucun compte n'est associé à ce courriel, désolé.", 'client', '', 0,  0),
('lost_password_email_not_found', 2, 'No account is associated with this email, sorry.', 'client', '', 0,  0),
('lost_password_email_validation_error', 1, 'Votre courriel est invalide', 'client', '', 0, 0),
('lost_password_email_validation_error', 2, 'Your email is invalid', 'client', '', 0, 0),
('validate_notification_client_email_title', 1, 'Confirmation de courriel', 'client', 'Message de notification: Création de compte (titre)', 0, 0),
('validate_notification_client_email_title', 2, 'Email confirm', 'client', 'Notification message : Create account (title)', 0, 0),
('account_notification_client_email_title', 1, '##--TO UPDATE--##', 'client', 'Message de notification: Création de compte', 0, 0),
('account_notification_client_email_title', 2, '##--TO UPDATE--##', 'client', 'Notification message : Create account', 0, 0),
('account_notification_client_email_message', 1, "<p>Bienvenue chez ##siteName##!</p><p>Votre nouveau compte vous donne acc&egrave;s &agrave; tous les &eacute;l&eacute;ments de notre site web et vous permet de placer les produits de votre choix dans votre propre panier virtuel jusqu'&agrave; ce que vous soyez pr&ecirc;t &agrave; paaser commande.</p><p>Merci de l'int&eacute;r&ecirc;t que vous portez à nos produits</p>", 'client', 'Message de notification: Bienvenue', 0, 0),
('account_notification_client_email_message', 2, "<p>Welcome to ##siteName##!</p><p>Your new account gives you full access to every feature on our website as well as your own cart that keeps your selections until you are ready to order.</p><p>Thank you for your interest towards our products</p>", 'client', 'Notification message: Welcome', 0, 0),
('form_account_duplicate_address_label', 1, "Même que l'adresse de facturation", 'cible', '', 0, 0),
('form_account_duplicate_address_label', 2, 'Same as the billing address.', 'cible', '', 0, 0);

REPLACE INTO `Static_Texts` (`ST_Identifier` ,`ST_LangID` ,`ST_Value` ,`ST_Type` ,`ST_Desc_backend` ,`ST_Editable` ,`ST_ModuleID`) VALUES
('revalidate_notification_client_email_message', 1, '<p>Bonjour ##firstName## ##lastName##</p>\r\n<p>Pour activer votre compte et v&eacute;rifier votre adresse de courriel, cliquez sur le lien ci-dessous : <br /> ##validated_email_link##</p>\r\n<p>Si le lien ne fonctionne pas, copiez ce texte dans la barre d''adresse de votre navigateur Internet et appuyez sur la touche Entr&eacute;e de votre clavier.</p>\r\n<p>Nous vous remercions de l''int&eacute;r&ecirc;t que vous portez &agrave; notre site et &agrave; nos produits.</p>\r\n<p>Pour toutes questions ou renseignements, veuillez communiquer avec nous.</p>', 'client', 'Message de notification : renvoi de confirmation courriel', 0, 0),
('revalidate_notification_client_email_message', 2, 'Hello ##firstName## ##lastName##<br /><br />To activate your account and verify your email address, you must click on the link below : <br /> <br /> <a href="##validated_email_link##">##validated_email_link##</a><br /><br />If that link does not work by clicking, please copy and paste the link into your browsers URL bar.Thank you for your interest in our web site and our products.<br />If you have any questions or for more information, please feel free to contact us.', 'client', 'Notification message: send confirmation email', 0, 0),
('validate_notification_client_email_message', 1, 'Bonjour ##firstName## ##lastName##<br /><br />Nous vous confirmons votre inscription sur le site <a href=\"##siteDomain##\">##siteDomain##</a>
<br /><br />Pour activer votre compte et v&eacute;rifier votre adresse de courriel, cliquez sur le lien ci-dessous : <br /> <br /> <a href="##validated_email_link##" >##validated_email_link##</a>
<br><br />
Si le lien ne fonctionne pas, copiez ce texte dans la barre d''adresse de votre navigateur Internet et appuyez sur la touche Entr&eacute;e de votre clavier.
<br />Pour ouvrir une session personnalis&eacute;e sur le site ##siteName##, identifiez-vous en entrant les informations suivantes:
<br /><br />
Courriel : ##email##<br />Mot de passe : ##password##
<br /><br />
Nous vous remercions de l''int&eacute;r&ecirc;t que vous portez &agrave; notre site et &agrave; nos produits.
<br /><br />Pour toutes questions ou renseignements, veuillez communiquer avec nous.', 'client', 'Message de notification: validation de courriel/ouverture de compte (message)', 0, 0),
('validate_notification_client_email_message', 2, 'Welcome ##firstName## ##lastName##<br /><br />Thank you for registering at <a href=\"##siteDomain##\">##siteDomain##</a><br />
To activate your account and verify your email address, you must click on the link below : <br /> <br /> <a href="##validated_email_link##">##validated_email_link##</a><br /><br />
If that link does not work by clicking, please copy and paste the link into your browsers URL bar.
<br /><br />You may log in the ##siteName## website by entering the following:
<br /><br />Email : ##email##<br />Password : ##password##
<br /><br />Thank you for your interest in our web site and our products.
<br />If you have any questions or for more information, please feel free to contact us.', 'client', 'Notification message: validation email content', 0, 0),
('account_created_admin_notification_message', 1, 'Création d''un nouveau compte sur le site. <br /> <br /> Prénom : ##firstname## <br />Nom : ##lastname## <br /> Courriel : ##email## <br /> <br />\n
Utiliser l''adresse  suivante pour accéder aux informations de ce compte: <br />
<a href="##siteDomain##/extranet/users/index/general/actionKey/edit/id/##NEWID##">##siteDomain##/extranet/users/index/general/actionKey/edit/id/##NEWID##</a>\n<br />(Identification requise)', 'client', 'Message notification admin : nouveau compte', 0, 0),
('account_created_admin_notification_message', 2, 'New account created on the website. <br /> <br /> Firstname : ##firstname## <br />Lastname: ##lastname## <br /> Email: ##email## <br /> <br />\n
Click on the address  below to manage the users account data: <br />
<a href="##siteDomain##/extranet/users/index/general/actionKey/edit/id/##NEWID##">##siteDomain##/extranet/users/index/general/actionKey/edit/id/##NEWID##</a>\n<br />(Authentication required)', 'client', 'Notification message admin : new account',0, 0),
('account_created_admin_notification_title', 1, 'Création d''un nouveau compte', 'client', 'Message notification admin : nouveau compte (titre)', 0, 0),
('account_created_admin_notification_title', 2, 'New account created', 'client', 'Notification message admin: new account (title)', 0, 0),
('account_validated_notification_client_email_title', 1, 'Activation de votre compte.',  'client', 'Activation compte: notification client (titre)', 0, 0),
('account_validated_notification_client_email_title', '2', 'Account activation', 'client', 'Activation compte: notification client (titre)', '0', 0),
('account_validated_notification_client_email_message', '2', 'Bonjour ##salutation## ##firstName## ##lastName##, <br />
Votre compte a été validé. Vous pouvez désormais vous connecter et passer vos commandes.<br /><br />
Cliquez sur le lien ci-dessous : <br /> <br />
<a href="##validated_account_link##">##validated_account_link##</a><br />
<br>
Si le lien ne fonctionne pas, copiez ce texte dans la barre d''adresse de votre navigateur Internet et appuyez sur la touche Entr&eacute;e de votre clavier.', 'client', 'Activation compte: notification client (message)', '0', 0),
('account_validated_notification_client_email_message', '2', 'Hello ##salutation## ##firstName## ##lastName##, <br />
Your account has been validated.  You can now connect and place your order online.<br /><br />
Click this link : <br /> <br />
<a href="##validated_account_link##">##validated_account_link##</a><br />
<br>
If the link is not clickable, copy it and paste it into your browser navigation bar and press enter on your keyboard.', 'client', 'Activation compte: notification client (message)', '0', 0),
('form_account_modified_message',1,'Votre compte a été modifié.','cible','',0, 0),
('form_account_modified_message',2,'Your account has been updated','cible','',0, 0),
('lost_password_notification_message', 1, "Votre nouveau mot de passe est : ##PASSWORD##", 'client', '', 0, 0),
('lost_password_notification_message', 2, 'Your new password is: ##PASSWORD##', 'client', '', 0, 0),
('lost_password_notification_email_title', '1', 'Votre nouveau mot de passe', 'client', '', 0, 0),
('lost_password_notification_email_title', '2', 'Your new password', 'client', '', 0, 0);

REPLACE INTO `Static_Texts` (`ST_Identifier` ,`ST_LangID` ,`ST_Value` ,`ST_Type` ,`ST_Desc_backend` ,`ST_Editable` ,`ST_ModuleID`) VALUES
('references_delete_inuse_alert', 1, 'Cette valeur est encore utilisée et ne peut être supprimée!',  'cible', '', 0, 0),
('references_delete_inuse_alert', 2, 'This value is still in use ans it can''t be deleted!', 'cible', '', 0, 0),
('button_add_references_values', 1, 'Ajouter une valeur',  'cible', '', 0, 1001),
('button_add_references_values', 2, 'Add a value', 'cible', '', 0, 1001),
('references_value_fr', 1, 'Français',  'cible', '', 0, 1001),	('references_value_fr', 2, 'French', 'cible', '', 0, 1001),
('references_value_en', 1, 'Anglais',  'cible', '', 0, 1001),	('references_value_en', 2, 'English', 'cible', '', 0, 1001),
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
('form_label_stateTxt', 1, 'Province / État',  'cible', '', 0, 0),
('form_label_stateTxt', 2, 'Province / State', 'cible', '', 0, 0),
('form_select_option_pageLayouts_3', 1, 'Mobile', 'cible', '', 0, 0),
('form_select_option_pageLayouts_3', 2, 'Mobile', 'cible', '', 0, 0),
('form_label_B_ShowHeader', 1, "Afficher le titre du bloc", "cible", "", 0, 0),
('form_label_B_ShowHeader', 2, "Display block title", "cible", "", 0, 0);
-- --------------------------------------------------------

--
-- Dumping data for table `Status`
--

INSERT INTO `Status` (`S_ID`, `S_Code`) VALUES
(1, 'online'),
(2, 'offline');

-- --------------------------------------------------------

--
-- Table structure for table `Videos`
--

DROP TABLE IF EXISTS `Videos`;
CREATE TABLE IF NOT EXISTS `Videos` (
  `V_ID` int(11) NOT NULL auto_increment,
  `V_Alias` varchar(255) NOT NULL,
  `V_Width` int(11) NOT NULL,
  `V_Height` int(11) NOT NULL,
  `V_Autoplay` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`V_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `VideosIndex`
--

DROP TABLE IF EXISTS `VideosIndex`;
CREATE TABLE IF NOT EXISTS `VideosIndex` (
  `VI_ID` int(11) NOT NULL auto_increment,
  `VI_LanguageID` int(11) NOT NULL,
  `VI_Description` text NOT NULL,
  `VI_Name` varchar(255) NOT NULL,
  `VI_Poster` varchar(255) NOT NULL,
  `VI_MP4` varchar(255) NOT NULL,
  `VI_WEBM` varchar(255) NOT NULL,
  `VI_OGG` varchar(255) NOT NULL,
  PRIMARY KEY  (`VI_ID`,`VI_LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Dumping data for table `Views`
--

INSERT INTO `Views` (`V_ID`, `V_Name`, `V_ZoneCount`, `V_Path`, `V_Image`, `V_SiteType`) VALUES
(1, 'Home', 2, 'template/home.phtml', 'image.png', 's'),
(2, 'Common', 2, 'template/common.phtml', 'image.png', 's'),
(3 , 'Common Mobile', '2', 'template/commonMobile.phtml', 'image.png', 'm');

INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES
('tinyMCE_color', '1', 'Couleur de fond (prévisualisation seulement) : ', 'cible', '', '0', '0', CURRENT_TIMESTAMP),
('tinyMCE_color', '2', 'Background color (Preview only) :', 'cible', '', '0', '0', CURRENT_TIMESTAMP);


INSERT INTO `Extranet_Resources` (`ER_ID`, `ER_ControlName`) VALUES
('100', 'users'),
(101, 'utilities'),
(10, 'search'),
(999, 'page');
INSERT INTO Extranet_ResourcesIndex (ERI_ResourceID, ERI_LanguageID, ERI_Name) VALUES
(100, 1, 'Usagers'),
(100, 2, 'Users'),
(10, 1, 'Recherche'),
(10, 2, 'Search'),
(999, 1, 'Pages'),
(999, 2, 'Pages'),
(101, 1, 'Utilities');
INSERT INTO Extranet_RolesResources (ERR_ID, ERR_RoleID, ERR_ResourceID, ERR_InheritedParentID) VALUES
(10001,1, 100, 0),
(999001,3, 999, 0);
INSERT INTO Extranet_RolesResourcesIndex (ERRI_RoleResourceID, ERRI_LanguageID, ERRI_Name, ERRI_Description) VALUES
(10001,1, 'Gestionnaire des usagers', 'Tous les droits'),
(10001,2, 'Users manager', 'All access'),
(999001,1, 'Gestionnaire', 'A les droits adminstrateur complets.'),
(999001,2, 'Manager', 'Has all adminstrators access.');
INSERT INTO Extranet_RolesResourcesPermissions (ERRP_RoleResourceID, ERRP_PermissionID) VALUES
(10001, 1),
(999001, 1);

INSERT INTO Extranet_RolesResources (ERR_ID, ERR_RoleID, ERR_ResourceID, ERR_InheritedParentID) VALUES
(10002,1, 101, 0);
INSERT INTO Extranet_RolesResourcesIndex (ERRI_RoleResourceID, ERRI_LanguageID, ERRI_Name, ERRI_Description) VALUES
(10002,1, 'Gestionnaire des utilités', 'Tous les droits'),
(10002,2, 'Utilities manager', 'All access'),
(10101, 2, 'Utilities manager', 'All access');
INSERT INTO Extranet_RolesResourcesPermissions (ERRP_RoleResourceID, ERRP_PermissionID) VALUES
(10002, 1);


INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('dashboard_administration_images', '1', 'Administration des images', 'cible', '', '0', '0'),
('dashboard_administration_images', '2', 'Images management', 'cible', '', '0', '0'),
('dashboard_administration_background_images_description', '1', 'Liste des images de fond d''écran', 'cible', '', '0', '0'),
 ('dashboard_administration_background_images_description', '2', 'List of background image', 'cible', '', '0', '0'),
('dashboard_administration_header_images_description', '1', 'Liste des images d''entête', 'cible', '', '0', '0'),
('dashboard_administration_header_images_description', '2', 'List of header''s images', 'cible', '', '0', '0');

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('hide_super_user_error', '1', 'Ne pas montrer les erreurs sur le site.', 'cible', '', '0', '0'),
('hide_super_user_error', '2', 'Don''t show errors on this site', 'cible', '', '0', '0'),
('show_super_user_error', '1', 'Afficher les erreurs détaillées sur le site.', 'cible', '', '0', '0'),
('show_super_user_error', '2', 'Show details errors on this site', 'cible', '', '0', '0'),
('form_return_view_all_label', '1', 'Message à afficher pour le retour a la liste (s''il y a lieu)', 'cible', '', '0', '0'),
('form_return_view_all_label', '2', 'Message  to display to go back to the list (if needed)', 'cible', '', '0', '0'),
('delete', '1', 'Supprimer', 'cible', '', '0', '0'),
('delete', '2', 'Delete', 'cible', '', '0', '0'),
('modify', '1', 'Modifier', 'cible', '', '0', '0'),
('modify', '2', 'Modify', 'cible', '', '0', '0'),
('add_image_header', '1', 'Ajouter une image d''entête', 'cible', '', '0', '0'),
('add_image_header', '2', 'Add a header image', 'cible', '', '0', '0'),
('add_image_background', '1', 'Ajouter une image de fond', 'cible', '', '0', '0'),
('add_image_background', '2', 'Add a background image', 'cible', '', '0', '0'),
('header_add_header_text', '1', 'Ajout d''une image d''entête', 'cible', '', '0', '0'),
('header_add_header_text', '2', 'Add an header image', 'cible', '', '0', '0'),
('header_modify_header_text', '1', 'Modifier une image d''entête', 'cible', '', '0', '0'),
('header_modify_header_text', '2', 'Modify an header image', 'cible', '', '0', '0'),
('header_modify_header_description', '1', 'Cette page permet de modifier une image d''entête.', 'cible', '', '0', '0'),
('header_modify_header_description', '2', 'This page allows the modification of an header image.', 'cible', '', '0', '0'),
('header_add_background_text', '1', 'Ajout d''une image de fond', 'cible', '', '0', '0'),
('header_add_background_text', '2', 'Add a background image', 'cible', '', '0', '0'),
('header_add_background_description', '1', 'Cette page permet l''ajout d''une image de fond.', 'cible', '', '0', '0'),
('header_add_background_description', '2', 'This page allows the addition of a background image.', 'cible', '', '0', '0'),
('header_modify_background_text', '1', 'Modifier une image de fond', 'cible', '', '0', '0'),
('header_modify_background_text', '2', 'Modify a background image', 'cible', '', '0', '0'),
('form_label_image_title', '1', 'Nom de l''image', 'cible', '', '0', '0'),
('form_label_image_title', '2', 'Name of the image', 'cible', '', '0', '0'),
('header_add_header_description', '1', 'Cette page vous permet d''ajouter une image d''entête', 'cible', '', '0', '0'),
('header_add_header_description', '2', 'This pag allows the addition of an header image', 'cible', '', '0', '0'),
('returnToTreeList', '1', 'Retour à l''arborescence', 'cible', '', '0', '0'),
('returnToTreeList', '2', 'Return to tree list', 'cible', '', '0', '0');

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('numberOfPages', 1, 'Nombre de pages qui utilisent cette image:', 'cible', '', 0, 0),
('numberOfPages', 2, 'Number of pages using this image:', 'cible', '', 0, 0),
('listPage', 1, 'Page', 'cible', '', 0, 0),
('listPage', 2, 'Page', 'cible', '', 0, 0),
('listPages', '1', 'Pages', 'cible', '', 0, 0),
('listPages', '2', 'Pages', 'cible', '', 0, 0),
('modify', 1, 'Modifier', 'cible', '', 0, 0),
('modify', 2, 'Modify', 'cible', '', 0, 0),
('header_add_header_description', 1, 'Cette page vous permet d''ajouter une image d''entête', 'cible', '', 0, 0),
('header_add_header_description', 2, 'Cette page vous permet d''ajouter une image d''entête', 'cible', '', 0, 0),
('header_crop_header_description', '1', 'Cette page permet de rogner un image', 'cible', '', '0', '0'),
('header_crop_header_description', '2', 'This page allows the crop of an image', 'cible', '', '0', '0'),
('header_add_header_text', 1, 'AJOUTER UNE IMAGE D''ENTÊTE', 'cible', '', 0, 0),
('header_add_header_text', 2, 'AJOUTER UNE IMAGE D''ENTÊTE', 'cible', '', 0, 0),
('add_image_header', 1, 'Ajouter une image d''entête', 'cible', '', 0, 0),
('add_image_header', 2, 'Add a header image', 'cible', '', 0, 0),
('returnToTreeList', 1, 'Retour à l''arborescence', 'cible', '', 0, 0),
('returnToTreeList', 2, 'Return to tree''s list', 'cible', '', 0, 0),
('dashboard_administration_header_images_description', 1, 'Gestion des images d''entête', 'cible', '', 0, 0),
('dashboard_administration_header_images_description', 2, 'Header''s images management', 'cible', '', 0, 0),
('crop', '1', 'Rogner', 'cible', '', '0', '0'),
('crop', '2', 'Crop', 'cible', '', '0', '0'),
('dashboard_administration_images', 1, 'Gestion des images pour les pages', 'cible', '', 0, 0),
('dashboard_administration_images', 2, 'Pages'' images management', 'cible', '', 0, 0),
('header_crop_header_text', '1', 'Rogner une image	', 'cible', '', '0', '0'),
('header_crop_header_text', '2', 'Crop an image', 'cible', '', '0', '0'),
('list_column_language', '1', 'Langue', 'cible', '', '0', '0'),
('list_column_language', '2', 'Language', 'cible', '', '0', '0'),
('page_delete_message_cant_find_administrator', '1', 'Cet administrateur n''existe pas.', 'cible', '', '0', '0'),
('page_delete_message_cant_find_administrator', '2', 'This administrator doesn''t exist.', 'cible', '', '0', '0'),
('form_label_menu_show_item_in_sitemap', '1', 'Afficher cet item du menu dans le plan du site', 'cible', '', '0', '0'),
('form_label_menu_show_item_in_sitemap', '2', 'Show this item in the sitemap', 'cible', '', '0', '0'),
('dashboard_administration_images', '1', 'Gestion des images pour les pages', 'cible', '', '0', '0'),
('dashboard_administration_images', '2', 'Pages'' images management', 'cible', '', '0', '0'),
('dashboard_administration_header_images_description', '1', 'Gestion des images d''entête', 'cible', '', '0', '0'),
('dashboard_administration_header_images_description', '2', 'Header''s images management', 'cible', '', '0', '0'),
('dashboard_administration_background_images_description', '1', 'Gestion des images de fond', 'cible', '', '0', '0'),
('dashboard_administration_background_images_description', '2', 'Background''s image management', 'cible', '', '0', '0'),
('returnToTreeList', 1, 'Retour à l''arborescence', 'cible', '', 0, 0),
('returnToTreeList', 2, 'Return to tree''s list', 'cible', '', 0, 0),
('add_image_header', 1, 'Ajouter une image d''entête', 'cible', '', 0, 0),
('add_image_header', 2, 'Add a header image', 'cible', '', 0, 0),
('header_add_header_text', 1, 'AJOUTER UNE IMAGE D''ENTÊTE', 'cible', '', 0, 0),
('header_add_header_text', 2, 'AJOUTER UNE IMAGE D''ENTÊTE', 'cible', '', 0, 0),
('header_add_header_description', 1, 'Cette page vous permet d''ajouter une image d''entête', 'cible', '', 0, 0),
('header_add_header_description', 2, 'Cette page vous permet d''ajouter une image d''entête', 'cible', '', 0, 0),
('header_modify_header_text', '1', 'MODIFIER UNE IMAGE D''''ENTÊTE', 'cible', '', '0', '0'),
('header_modify_header_text', '2', 'AJOUTER UNE IMAGE D''''ENTÊTE', 'cible', '', '0', '0'),
('header_modify_header_description', '1', 'Cette page vous permet de modifier une image d''''entête', 'cible', '', '0', '0'),
('header_modify_header_description', '2', 'Cette page vous permet de modifier une image d''''entête', 'cible', '', '0', '0'),
('listPage', 1, 'Page', 'cible', '', 0, 0),
('listPage', 2, 'Page', 'cible', '', 0, 0),
('add_image_background', '1', 'Ajouter une image de fond', 'cible', '', '0', '0'),
('add_image_background', '2', 'Add a background picture', 'cible', '', '0', '0'),
('label_alt_image', '1', 'Alt de l''image', 'cible', '', '0', '0'),
('label_alt_image', '2', 'Image Alt', 'cible', '', '0', '0');

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID, ST_RichText) VALUES
('site_label_edith', 1, "EDITH", "cible", "", 0, 0, 1),
('site_label_edith', 2, "EDITH", "cible", "", 0, 0, 1),
('form_label_EU_DefaultSite', 1, "Site par défault", "cible", "", 0, 0, 1),
('form_label_EU_DefaultSite', 2, "Default website", "cible", "", 0, 0, 1),
('form_label_sitesList_description', 1, "Liste des sites à gérer", "cible", "", 0, 0, 1),
('form_label_sitesList_description', 2, "Website list to manage", "cible", "", 0, 0, 1),
('validation_message_username_already_exists', 1, "Un autre utilisateur possède déjà ce nom d'utilisateur.", "cible", "", 0, 0, 1),
('validation_message_username_already_exists', 2, "This username is already assigned.", "cible", "", 0, 0, 1),
('form_label_P_ThemeID', 1, "Thème de la page", "cible", "", 0, 0, 1),
('form_label_P_ThemeID', 2, "Page theme", "cible", "", 0, 0, 1),
('form_select_option_theme_Default', 1, "Défault", "cible", "", 0, 0, 1),
('form_select_option_theme_Default', 2, "Default", "cible", "", 0, 0, 1),
('form_label_advance_words', 1, "Mots à rechercher", "client", "", 0, 10, 0),
('form_label_advance_words', 2, "Words to search for", "client", "", 0, 10, 0),
('form_label_advance_options', 1, "Rechercher", "client", "", 0, 10, 0),
('form_label_advance_options', 2, "Find", "client", "", 0, 10, 0),
('form_label_advance_option_one', 1, "au moins un mot", "client", "", 0, 10, 0),
('form_label_advance_option_one', 2, "at least one of the words", "client", "", 0, 10, 0),
('form_label_advance_option_all', 1, "tous les mots", "client", "", 0, 10, 0),
('form_label_advance_option_all', 2, "all words", "client", "", 0, 10, 0),
('form_label_advance_option_exact', 1, "la phrase exacte", "client", "", 0, 10, 0),
('form_label_advance_option_exact', 2, "the exact phrase", "client", "", 0, 10, 0),
('form_label_advance_sites', 1, "Chercher dans ", "client", "", 0, 10, 0),
('form_label_advance_sites', 2, "Search into", "client", "", 0, 10, 0),
('form_label_advance_allsites', 1, "Tous les sites", "client", "", 0, 10, 0),
('form_label_advance_allsites', 2, "all websites", "client", "", 0, 10, 0),
('dashboard_administration_website_reindexing_pdf', 1, "Ré-indexation des fichiers PDF", "cible", "", 0, 10, 0),
('dashboard_administration_website_reindexing_pdf', 2, "Re-indexing PDF files", "cible", "", 0, 10, 0),
('header_reindexing_pdf_text', 1, "Indexation des fichiers PDF", "cible", "", 0, 10, 0),
('header_reindexing_pdf_text', 2, "Indexing PDF files", "cible", "", 0, 10, 0),
('header_reindexing_pdf_description', 1, "Cette page permet d'indexer les fichiers PDF pour la recherche. L'indexation de contenu peut être relativement longue. C'est pour cette raison que cette action est séparée des autres contenus.", "cible", "", 0, 10, 0),
('header_reindexing_pdf_description', 2, "", "cible", "", 0, 10, 0),
('empty_data_disclaimer', 1, "Certaines sections de ce site ne sont pas encore complétée.<br />Merci de votre compréhension.", "client", "", 0, 0, 1),
('empty_data_disclaimer', 2, "Only certain sections of this site have been translated. The content of this page is available in French only.<br />
To consult the French version of this page, <a href='##HREF##' >click here</a>.<br />
We apologize for any inconvenience.", "client", "", 0, 0, 1),
('form_label_sitesList_page', 1, "Lier à une page du site", "cible", "", 0, 0, 1),
('form_label_sitesList_page', 2, "Link to a page from", "cible", "", 0, 0, 1),
('form_label_pageToLink', 1, "Page liée", "cible", "", 0, 0, 1),
('form_label_pageToLink', 2, "Linked page", "cible", "", 0, 0, 1),
('display_blocks_related_page', 1, "Afficher les blocs de la page liée", "cible", "", 0, 0, 1),
('display_blocks_related_page', 2, "Display blocks from related page", "cible", "", 0, 0, 1),
('form_label_blocks', 1, "Sélectionner les blocs à dupliquer", "cible", "", 0, 0, 1),
('form_label_blocks', 2, "Select blocks to copu", "cible", "", 0, 0, 1),
('form_fieldset_legend_linkBlocks', 1, "Liste des blocs de la page liée", "cible", "", 0, 0, 1),
('form_fieldset_legend_linkBlocks', 2, "Blocks list from the related page", "cible", "", 0, 0, 1),
('form_label_blocks_empty', 1, "Il n'y a aucun bloc de ce module dans la page liée.", "cible", "", 0, 0, 1),
('form_label_blocks_empty', 2, "There is no block of this module in the related page.", "cible", "", 0, 0, 1),
('form_fieldset_legend_linkBlocks_empty', 1, "", "cible", "", 0, 0, 1),
('form_fieldset_legend_linkBlocks_empty', 2, "", "cible", "", 0, 0, 1),
('confirm_load_bloc_parameters', 1, "Voulez-vous charger les paramètres du bloc?", "cible", "", 0, 0, 1),
('confirm_load_bloc_parameters', 2, "", "cible", "", 0, 0, 1),
('alert_still_linked_blocks', 1, "<p>Il existe des blocs de la page liée dans la page en cours.</p><p>Si vous voulez modifier la page liée, vous devez supprimer tous les blocs issus de cette page.</p>", "cible", "", 0, 0, 1),
('alert_still_linked_blocks', 2, "<p>There's still, in the current page, some blocks from this related page.</p><p>If you want to modify this parameter, please, delete all these related blocks before.</p>", "cible", "", 0, 0, 1),
('cant_delete', 1, "Modification non-autorisée", "cible", "", 0, 0, 1),
('cant_delete', 2, "Mocification not allowed", "cible", "", 0, 0, 1),
('form_label_noValue', 1, "Aucun", "cible", "", 0, 0, 1),
('form_label_noValue', 2, "None", "cible", "", 0, 0, 1),
('form_label_load', 1, "Charger", "cible", "", 0, 0, 1),
('form_label_load', 2, "Load", "cible", "", 0, 0, 1),
('form_label_showFilter', 1, "Afficher la liste déroulante des dates", "cible", "", 0, 0, 1),
('form_label_showFilter', 2, "Show the date list dropdown", "cible", "", 0, 0, 1),
('form_label_canonical_link', '1', 'Lien canonique (url absolue : http://www.domaine.com/finUrl)', 'cible', '', '0', '999', '0'),
('form_label_canonical_link', '2', 'Canonical link (Absolute Url : http://www.domain.com/path...)', 'cible', '', '0', '999', '0'),
('list_column_id', 1, "ID", "cible", "", 0, 0, 0),
('list_column_id', 2, "ID", "cible", "", 0, 0, 0)
;
REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID, ST_RichText) VALUES
('error_system_admin', 1, "Une erreur système s'est produit lors de la connexion. <br />Merci de contacter un administrateur.", "cible", "", 0, 0, 0),
('error_system_admin', 2, "A system error occurs. <br />Please, contact an administrator.", "cible", "", 0, 0, 0),
('form_label_content_from_duplicated_blocks', 1, 'Le contenu de ce bloc est issu de la page liée.', 'cible', '', 0, 0, 1),
('form_label_content_from_duplicated_blocks', 2, 'The content of this block is from the linked page.', 'cible', '', 0, 0, 1),
('footer_copyright_mobile', 1, '<p>&copy; %%%GET_DATE_YEAR%%%, %%%SITE_NAME%%%.<br /> Tous droits r&eacute;serv&eacute;s. </p>', 'client', 'Texte du pied de page', 1, 0, 1),
('footer_copyright_mobile', 2, '<p>&copy; %%%GET_DATE_YEAR%%%, %%%SITE_NAME%%%.<br /> All right reserved.</p>', 'client', 'Texte du pied de page', 1, 0, 1),
('see_details_repertoire_text', 1, 'En savoir plus', 'client', '', 0, 20, 1),
('see_details_news_text', 1, 'En savoir plus', 'client', '', 0, 2, 1),
('footer_cible_propulse', 1, 'Propulsé par ##edith##', 'cible', '', 0, 0, 1),
('footer_cible_propulse', 2, 'Propelled by ##edith##', 'cible', '', 0, 0, 1),
('footer_site_standard', 1, 'Accéder au site complet', 'cible', '', 0, 0, 1),
('footer_site_standard', 2, 'View Full Site', 'cible', '', 0, 0, 1),
('menu_reseaux', 1, 'Réseaux sociaux', 'cible', '', 0, 0, 1),
('menu_reseaux', 2, 'Social Networks', 'cible', '', 0, 0, 1),
('fontIcons_Label', 1, "Icônes", "cible", "", 0, 999, 0),
('fontIcons_Label', 2, "Icons", "cible", "", 0, 999, 0),
('form_label_entities_associate', 1, 'Accès aux sites et groupes d''administration.', 'cible', '', 0, 0, 0),
('form_label_entities_associate', 2, 'Access to web sites and administrative groups.', 'cible', '', 0, 0, 0),
('or', 1, "ou", "cible", "", 0, 0, 0),
('or', 2, "or", "cible", "", 0, 0, 0),
('validation_message_passwordInvalid', 1, "Le mot de passe doit contenir entre 8 et 255 caractères, au moins une lettre minuscule, au moins une lettre majuscule, au moins un chiffre et un des caractères suivant : #-+!*$@%_", "cible", "", 0, 0, 0),
('validation_message_passwordInvalid', 2, "The password must contain between 8 and 255 characters, at least one lowercase letter, at least one uppercase letter, at least one digit and one of the following characters: # - +!", "cible", "", 0, 0, 0),
('validation_message_no_empty_sites', 1, 'Il faut associer le profil à au moins une entité.', 'cible', '', 0, 0, 0),
('validation_message_no_empty_sites', 2, 'Please select at least one web site', 'cible', '', 0, 0, 0),
('counter_characters_label', 1, ' (<span class="counter">##MAX##</span> / ##MAX##)', "cible", "", 0, 0, 0),
('counter_characters_label', 2, ' (<span class="counter">##MAX##</span> / ##MAX##)', "cible", "", 0, 0, 0),
('entity_label', 1, 'Entité >', 'cible', '', 0, 0, 0),
('entity_label', 2, 'Entity >', 'cible', '', 0, 0, 0),
('form_label_parcourir', 1, "Parcourir", "cible", "", 0, 0, 0),
('form_label_parcourir', 2, "Search", "cible", "", 0, 0, 0),
('header_edit_pageheader_text', 1, 'Image en en-tête de page', 'cible', '0', 0, 0, 0),
('header_edit_pageheader_text', 2, 'Page header image', 'cible', '0', 0, 0, 0),
('crop_maximum_height', 1, 'Hauteur maximum', 'cible', '0', 0, 0, 0),
('crop_maximum_height', 2, 'Maximum height', 'cible', '0', 0, 0, 0),
('crop_maximum_width', 1, 'Largeur maximum', 'cible', '0', 0, 0, 0),
('crop_maximum_width', 2, 'Maximum width', 'cible', '0', 0, 0, 0),
('crop_selection_height', 1, 'Hauteur de la sélection', 'cible', '0', 0, 0, 0),
('crop_selection_height', 2, 'Selection height', 'cible', '0', 0, 0, 0),
('crop_selection_width', 1, 'Largeur de la sélection', 'cible', '0', 0, 0, 0),
('crop_selection_width', 2, 'Selection width', 'cible', '0', 0, 0, 0)
;




/*
espagnol
UPDATE `Static_Texts` SET  `ST_Value` =  'Boletin' WHERE  `Static_Texts`.`ST_Identifier` =  'newsletter_button_newsletter' AND  `Static_Texts`.`ST_LangID` =3;
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('newsletter_button_subscribe', '3', 'Abonarse', 'cible', '', '0', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('newsletter_button_unsubscribe', '3', 'Desabonarse', 'cible', '', '0', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('newsletter_button_archives', '3', 'Archivos', 'cible', '', '0', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('newsletter_button_return_to_newsletter', '3', 'Regresar al boletin', 'cible', '', '0', '8', CURRENT_TIMESTAMP);
UPDATE `Static_Texts` SET `ST_Value` = 'Back to the newsletter',`ST_ModifDate` = NOW( ) WHERE CONVERT( `Static_Texts`.`ST_Identifier` USING utf8 ) = 'newsletter_button_return_to_newsletter' AND `Static_Texts`.`ST_LangID` =2 LIMIT 1 ;
UPDATE `Static_Texts` SET `ST_Value` = 'Regresar al boletin',`ST_ModifDate` = NOW( ) WHERE CONVERT( `Static_Texts`.`ST_Identifier` USING utf8 ) = 'newsletter_button_return_to_newsletter' AND `Static_Texts`.`ST_LangID` =3 LIMIT 1 ;
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('news_no_news', '3', 'Actualmente no hay ninguna novedad', 'cible', '', '0', '2', CURRENT_TIMESTAMP);
UPDATE `Static_Texts` SET  `ST_Value` =  'Regresar a la lista' WHERE  `Static_Texts`.`ST_Identifier` =  'see_all_jobs' AND  `Static_Texts`.`ST_LangID` =3;
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('forms_label_name', '3', 'Apellido', 'cible', '', '0', '11', CURRENT_TIMESTAMP);
UPDATE `Static_Texts` SET  `ST_Value` =  'This email ''%value%'' is not valid.' WHERE  `Static_Texts`.`ST_Identifier` =  'validation_message_emailAddressInvalid' AND  `Static_Texts`.`ST_LangID` =2;
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('validation_message_emailAddressInvalid', '3', 'Este correo no es válido ''%value%''.', 'cible', '', '0', '0', CURRENT_TIMESTAMP);
UPDATE `Static_Texts` SET `ST_Value` = '<br /><br />Por motivos de seguridad, ingrese, por favor, los caracteres alfanuméricos de la imagen en el espacio a continuación.',`ST_ModifDate` = NOW( ) WHERE CONVERT( `Static_Texts`.`ST_Identifier` USING utf8 ) = 'newsletter_captcha_label' AND `Static_Texts`.`ST_LangID` =3 LIMIT 1;
UPDATE `Static_Texts` SET  `ST_Value` =  'Recargar' WHERE  `Static_Texts`.`ST_Identifier` =  'button_captcha_refresh' AND  `Static_Texts`.`ST_LangID` =3;
UPDATE `Static_Texts` SET  `ST_Value` =  'Enviar' WHERE  `Static_Texts`.`ST_Identifier` =  'button_submit' AND  `Static_Texts`.`ST_LangID` =3;
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('newsletter_fo_form_label_fName', '3', 'Nombre', 'cible', '', '0', '0', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('validation_message_empty_field', '3', 'Debe llenar este campo.', 'cible', '', '0', '0', CURRENT_TIMESTAMP);
UPDATE `Static_Texts` SET  `ST_Value` =  'Surname' WHERE  `Static_Texts`.`ST_Identifier` =  'newsletter_fo_form_label_lName' AND  `Static_Texts`.`ST_LangID` =2;
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('newsletter_fo_form_label_lName', '3', 'Apellido', 'cible', '', '0', '0', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('newsletter_fo_form_label_email', '3', 'Correo electrónico', 'cible', '', '0', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('newsletter_fo_form_label_securityCaptcha_newsletter', '3', 'Por motivos de seguridad, ingrese, por favor, los caracteres alfanuméricos de la imagen en el espacio a continuación.', 'cible', '', '0', '8', CURRENT_TIMESTAMP);
UPDATE `Static_Texts` SET  `ST_Value` =  'Recargar' WHERE  `Static_Texts`.`ST_Identifier` =  'button_refresh_captcha' AND  `Static_Texts`.`ST_LangID` =3;
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('form_field_required_label', '3', 'Campos obligatorios', 'cible', '', '0', '0', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('form_label_email', '3', 'Correo electrónico', 'cible', '', '0', '0', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('form_label_unsubscribe_reason', '3', 'Razón', 'cible', '', '0', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('newsletter_title_desabonnement_text', '3', 'Desinscripción', 'cible', '', '0', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('message_contact_succeed', '3', 'Su mensaje ha sido enviado.  Gracias por su interés.', 'cible', '', '0', '11', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('news_latest_temoignages_text', '3', 'Testimonios', 'cible', '', '0', '0', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('see_all_temoignages_text', '3', 'Todos los testimonios', 'cible', '', '0', '0', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('joindre_fo_form_label_confident_joindre', '3', '<a class="confidentialiteDD" href="../politique-de-confidentialite-es" target="_blank">Política de confidencialidad</a>', 'cible', '', '0', '0', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('newsletter_subscribe_confirmation_message2', '3', 'Su inscripción a este boletín ha sido registrada', 'cible', '', '0', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('newsletter_subscribe_confirmation_message1', '3', 'Su inscripción al boletín ha sido completada.<br /> Gracias por su interés en KNS.', 'cible', '', '0', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('newsletter_unsubscribe_confirmation_message', '3', 'Su desinscripción al boletín se ha completado.', 'cible', '', '0', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('newsletter_title_archives_text', '3', 'Archivos', 'cible', '', '0', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('newsletter_title_abonnement_text', '3', 'En la prensa', 'cible', '', '0', '0', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('Ya existe una cuenta para este correo.', 'client', 'Email already exists', 1, 0, CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('email_notification_footer', '3', '##SITE-NAME##  le ha enviado un correo', 'client', 'Notification email: footer.', '1', '0', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('see_all_news_text', '3', 'Todas las novedades >', 'client', 'Retour à la liste de toutes les nouvelles', '1', '2', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('see_details_news_text', '3', 'Más detalles', 'client', 'Message pour se rendre a la parution d'une infolettre', '1', '2', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('news_latest_news_text', '3', 'Actualidades', 'client', 'Titre des nouvelles dans la page d'accueil', '1', '2', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('see_details_newsletter_text_1', '3', 'Detalles >', 'client', 'Voir une parution d'une infolettre', '1', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('newsletter_no_archives_client', '3', 'No hay ningún archivo.', 'client', 'Message indiquant au client qu'il n'y a aucune archive d'infolettre.', '1', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('infolettre_text_salutation', '3', '<p class="salutation_infolettre">Buenos días, ##prenom##</p>', 'client', '', '1', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('footer_text_facebook', '3', 'Únase a nosotros en Facebook', 'client', '', '1', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ( 'see_details_newsletter_text_2', '3', 'Seguir leyendo >', 'client', '', '1', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ( 'see_details_newsletter_text_3', '3', 'Postular', 'client', '', '1', '8', CURRENT_TIMESTAMP);
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('contact_form_notification_admin_message', '3', 'Le enviamos un mensaje a través de su sitio Web ##siteDomain##<br /><br />De:##firstName## ##lastName##<br /><br />Correo electrónico:##email##<br /><br />Mensaje:comments##', 'client', '', '1', '11', NOW());
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('contact_form_notification_admin_title', '3', 'Unirse a nosotros', 'client', 'Message notification admin: Formulaire de contact (titre)', '1', '11', NOW());
INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('footer_sansFrais', '3', '<p>Teléfono: 819 822-4028</p> <p>Sin cargo: 1 855 252-0808</p>', 'client', 'Message notification admin: Formulaire de contact (titre)', '1', '11', NOW());

INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`) VALUES ('see_details_newsletter_text', '3', 'Dans la liste des infolettres, envoie vers un article de l'infolettre.', '1', '11', NOW());



INSERT INTO `Modules` (`M_ID`, `M_Title`, `M_MVCModuleTitle`, `M_UseProfile`, `M_NeedAuth`, `M_HasFrontEnd`, `M_SiteType`, `M_Indexation`) VALUES
(8, 'Infolettres', 'newsletter', 1, 0, 1, 's', 'NewsletterRelease');


*/


INSERT INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`, `ST_ModifDate`, `ST_RichText`) VALUES
('forms_label_placeholder_surname', '1', 'Prénom', 'cible', '', '0', '0', CURRENT_TIMESTAMP, '1'),
('forms_label_placeholder_surname', '2', 'Surname', 'cible', '', '0', '0', CURRENT_TIMESTAMP, '1'),
('forms_label_placeholder_name', '1', 'Nom', 'cible', '', '0', '0', CURRENT_TIMESTAMP, '1'),
('forms_label_placeholder_name', '2', 'Name', 'cible', '', '0', '0', CURRENT_TIMESTAMP, '1'),
('forms_label_placeholder_email', '1', 'adresse@courriel.com', 'cible', '', '0', '0', CURRENT_TIMESTAMP, '1'),
('forms_label_placeholder_email', '2', 'address@email.com', 'cible', '', '0', '0', CURRENT_TIMESTAMP, '1'),
('forms_label_placeholder_comments', '1', 'Un court message', 'cible', '', '0', '0', CURRENT_TIMESTAMP, '1'),
('forms_label_placeholder_comments', '2', 'A short message', 'cible', '', '0', '0', CURRENT_TIMESTAMP, '1');


INSERT INTO `Blocks` (`B_ID`, `B_PageID`, `B_ModuleID`, `B_ZoneID`, `B_Position`, `B_ShowHeader`, `B_Draft`, `B_Online`, `B_Secured`, `B_FromSite`, `B_DuplicateId`, `B_LastModified`) VALUES
(91, 3, 1, 1, 1, 0, 0, 1, 0, NULL, 0, '2014-10-03 21:05:30');

INSERT INTO `BlocksIndex` (`BI_BlockID`, `BI_LanguageID`, `BI_BlockTitle`) VALUES
(91, 1, 'Politique de confidentialité'),
(91, 2, 'Privacy politics');

INSERT INTO `TextData` (`TD_ID`, `TD_BlockID`, `TD_LanguageID`, `TD_OnlineTitle`, `TD_OnlineText`, `TD_DraftTitle`, `TD_DraftText`, `TD_ToApprove`) VALUES
(52, 91, 1, '', '<hr />\r\n<p class="titre_titre_20">&nbsp;</p>\r\n<p class="titre_titre_20">T&eacute;moins (Cookies)</p>\r\n<p style="text-align: justify;">Un t&eacute;moin, couramment appel&eacute; un &laquo; cookie &raquo;, est une petite quantit&eacute; d''information qui est enregistr&eacute;e sur le disque dur de l''usager et contient des donn&eacute;es sur l''usager. L''utilisation d''un t&eacute;moin, sur notre site, n''est aucunement li&eacute;e &agrave; des informations personnelles qui sont confidentielles. Aussit&ocirc;t que l''utilisateur ferme son navigateur, le t&eacute;moin s''efface simplement. En fait, en utilisant le t&eacute;moin, nous permettons que l''utilisateur n''entre son nom d''usager et son mot de passe qu''une seule fois, ce qui lui &eacute;pargne du temps. Si l''utilisateur refuse l''utilisation du t&eacute;moin, il peut quand m&ecirc;me utiliser notre site. Toutefois, la navigation sera limit&eacute;e dans certaines sections du site.</p>\r\n<p style="text-align: justify;">Nous utilisons les t&eacute;moins pour faciliter votre pr&eacute;sente visite sur le site ainsi que votre prochaine visite, car les t&eacute;moins gardent en m&eacute;moire votre langue et le pays de votre localit&eacute;.</p>\r\n<p>&nbsp;</p>\r\n<hr />\r\n<p>&nbsp;</p>\r\n<p class="titre_titre_20">S&eacute;curit&eacute;</p>\r\n<p style="text-align: justify;">Ce site Web est con&ccedil;u pour prendre toutes les pr&eacute;cautions n&eacute;cessaires afin de prot&eacute;ger les informations des utilisateurs. Quand l''utilisateur soumet de l''information confidentielle sur le site, les informations sont prot&eacute;g&eacute;es, qu''elles soient en ligne ou non.</p>\r\n<p style="text-align: justify;">Nous faisons tout notre possible pour prot&eacute;ger les informations des usagers. Toutes les informations que nous recueillons, et non seulement les informations confidentielles, sont assujetties &agrave; des restrictions dans nos bureaux. Seuls les employ&eacute;s qui ont besoin de l''information pour faire une t&acirc;che sp&eacute;cifique (par exemple, le service &agrave; la client&egrave;le) ont acc&egrave;s aux informations confidentielles du client.&nbsp;</p>\r\n<p style="text-align: justify;">Votre adresse courriel est gard&eacute;e confidentielle et ne sera jamais vendue ou partag&eacute;e avec une autre organisation.</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>&nbsp;</p>', '', '<table style="width: 960px;" border="0">\r\n<tbody>\r\n<tr>\r\n<td width="960"><hr />\r\n<p class="titre_titre_20">&nbsp;</p>\r\n<p class="titre_titre_20">T&eacute;moins (Cookies)</p>\r\n<p style="text-align: justify;">Un t&eacute;moin, couramment appel&eacute; un &laquo; cookie &raquo;, est une petite quantit&eacute; d''information qui est enregistr&eacute;e sur le disque dur de l''usager et contient des donn&eacute;es sur l''usager. L''utilisation d''un t&eacute;moin, sur notre site, n''est aucunement li&eacute;e &agrave; des informations personnelles qui sont confidentielles. Aussit&ocirc;t que l''utilisateur ferme son navigateur, le t&eacute;moin s''efface simplement. En fait, en utilisant le t&eacute;moin, nous permettons que l''utilisateur n''entre son nom d''usager et son mot de passe qu''une seule fois, ce qui lui &eacute;pargne du temps. Si l''utilisateur refuse l''utilisation du t&eacute;moin, il peut quand m&ecirc;me utiliser notre site. Toutefois, la navigation sera limit&eacute;e dans certaines sections du site.</p>\r\n<p style="text-align: justify;">Nous utilisons les t&eacute;moins pour faciliter votre pr&eacute;sente visite sur le site ainsi que votre prochaine visite, car les t&eacute;moins gardent en m&eacute;moire votre langue et le pays de votre localit&eacute;.</p>\r\n<p>&nbsp;</p>\r\n<hr />\r\n<p>&nbsp;</p>\r\n<p class="titre_titre_20">S&eacute;curit&eacute;</p>\r\n<p style="text-align: justify;">Ce site Web est con&ccedil;u pour prendre toutes les pr&eacute;cautions n&eacute;cessaires afin de prot&eacute;ger les informations des utilisateurs. Quand l''utilisateur soumet de l''information confidentielle sur le site, les informations sont prot&eacute;g&eacute;es, qu''elles soient en ligne ou non.</p>\r\n<p style="text-align: justify;">Nous faisons tout notre possible pour prot&eacute;ger les informations des usagers. Toutes les informations que nous recueillons, et non seulement les informations confidentielles, sont assujetties &agrave; des restrictions dans nos bureaux. Seuls les employ&eacute;s qui ont besoin de l''information pour faire une t&acirc;che sp&eacute;cifique (par exemple, le service &agrave; la client&egrave;le) ont acc&egrave;s aux informations confidentielles du client.&nbsp;</p>\r\n<p style="text-align: justify;">Votre adresse courriel est gard&eacute;e confidentielle et ne sera jamais vendue ou partag&eacute;e avec une autre organisation.</p>\r\n<p>&nbsp;</p>', 0),
(53, 91, 2, '', '<hr />\r\n<p>&nbsp;</p>\r\n<p>Cookies</p>\r\n<p>A "cookie" is a small amount of information that is stored on the hard drive of the user and provides information on the user. Using a cookie on our site is in no way linked to personal information that is confidential. As soon as the user closes their browser, the cookie simply disappears. In fact , by using the cookie, we allow the user to enter their username and password once, saving time while navigating the site. If the user declines the use of cookie, they may still use our site. However, the navigation will be limited in some areas of the site.&nbsp;We use cookies to facilitate your visit and next visits to this website by keeping your language and country in memory.<br />&nbsp;</p>\r\n<hr />\r\n<p>&nbsp;</p>\r\n<p>Security</p>\r\n<p>This website is designed to take all necessary precautions to protect our users'' information . When the user submits confidential information on the website, information is protected , whether online or not.</p>\r\n<p>We do everything we can to protect user information . All information we collect , not just the sensitive information, is restricted in our offices. Only employees who need the information to do a specific job ( for example, customer service ) are granted access to confidential customer information.</p>\r\n<p><br />Your email address is kept confidential and will never be sold or shared with any other organization.</p>', '', '<hr />\r\n<p>&nbsp;</p>\r\n<p>Cookies</p>\r\n<p>A "cookie" is a small amount of information that is stored on the hard drive of the user and provides information on the user. Using a cookie on our site is in no way linked to personal information that is confidential. As soon as the user closes their browser, the cookie simply disappears. In fact , by using the cookie, we allow the user to enter their username and password once, saving time while navigating the site. If the user declines the use of cookie, they may still use our site. However, the navigation will be limited in some areas of the site.&nbsp;We use cookies to facilitate your visit and next visits to this website by keeping your language and country in memory.<br />&nbsp;</p>\r\n<hr />\r\n<p>&nbsp;</p>\r\n<p>Security</p>\r\n<p>This website is designed to take all necessary precautions to protect our users'' information . When the user submits confidential information on the website, information is protected , whether online or not.</p>\r\n<p>We do everything we can to protect user information . All information we collect , not just the sensitive information, is restricted in our offices. Only employees who need the information to do a specific job ( for example, customer service ) are granted access to confidential customer information.</p>\r\n<p><br />Your email address is kept confidential and will never be sold or shared with any other organization.</p>', 0);
