-- Version SVN: $Id: moduleFormCreate.sql 1841 2016-01-13 19:21:26Z ssoares $

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

--
-- Données pour activer module et les liens dans le back end
--

INSERT INTO Modules (M_ID, M_Title, M_MVCModuleTitle) VALUES (13, 'Formulaires', 'form');

INSERT INTO Modules_ControllersActionsPermissions (MCAP_ModuleID, MCAP_ControllerTitle, MCAP_ControllerActionTitle, MCAP_PermissionTitle, MCAP_Position) VALUES
(13, 'index', 'list', 'edit', 1);

INSERT INTO ModuleViews (MV_Name, MV_ModuleID) VALUES
('form', 13),
('detail', 13);

INSERT INTO Extranet_Resources (ER_ID, ER_ControlName) VALUES (13, 'form');

INSERT INTO Extranet_ResourcesIndex (ERI_ResourceID, ERI_LanguageID, ERI_Name) VALUES
(13, 1, 'Formulaire'),
(13, 2, 'Form');

INSERT INTO Extranet_RolesResources (ERR_ID, ERR_RoleID, ERR_ResourceID, ERR_InheritedParentID) VALUES
(1301,1, 13, 0);

INSERT INTO Extranet_RolesResourcesIndex (ERRI_RoleResourceID, ERRI_LanguageID, ERRI_Name, ERRI_Description) VALUES
(1301,1, 'Gestionnaire de formulaire', ''),
(1301,2, 'Form manager', '');

INSERT INTO Extranet_RolesResourcesPermissions (ERRP_RoleResourceID, ERRP_PermissionID) VALUES
(1301, 1);

-- -----------------------------------------------------
-- Table `Form`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form` ;

CREATE  TABLE IF NOT EXISTS `Form` (
  `F_ID` INT(11) NOT NULL AUTO_INCREMENT ,
  `F_Notification` INT(2) NULL ,
  `F_Profil` INT(2) NULL ,
  `F_Captcha` INT(2) NULL ,
  PRIMARY KEY (`F_ID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_Respondent`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_Respondent` ;

CREATE  TABLE IF NOT EXISTS `Form_Respondent` (
  `FR_ID` INT(11) NOT NULL AUTO_INCREMENT ,
  `FR_FormID` INT(11) NULL ,
  `FR_ProfilID` INT(11) NULL ,
  `FR_StartDateTime` DATETIME NULL ,
  `FR_EndDateTime` DATETIME NULL ,
  `FR_Complete` INT(2) NULL ,
  PRIMARY KEY (`FR_ID`) ,
  INDEX `FK_FR_Form` (`FR_FormID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_RespondentResponse`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Form_RespondentResponse` (
  `FRR_RespondentID` int(11) NOT NULL,
  `FRR_QuestionID` int(11) NOT NULL DEFAULT '0',
  `FRR_Response` varchar(255) NOT NULL DEFAULT '',
  `FRR_SubResponse` varchar(255) DEFAULT NULL,
  `FRR_LanguageID` int(11) NOT NULL,
  PRIMARY KEY (`FRR_RespondentID`,`FRR_QuestionID`,`FRR_Response`,`FRR_LanguageID`),
  KEY `FK_FRR_FR` (`FRR_RespondentID`)
) ENGINE=MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_Section`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_Section` ;

CREATE  TABLE IF NOT EXISTS `Form_Section` (
  `FS_ID` INT(11) NOT NULL AUTO_INCREMENT ,
  `FS_FormID` INT(11) NULL ,
  `FS_Seq` INT(2) NULL ,
  `FS_Repeat` INT(2) NULL ,
  `FS_RepeatMin` INT(11) NULL ,
  `FS_RepeatMax` INT(11) NULL ,
  `FS_ShowTitle` INT(2) NULL ,
  `FS_PageBreak` INT(2) NULL ,
  PRIMARY KEY (`FS_ID`) ,
  INDEX `FK_FS_Form` (`FS_FormID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci
COMMENT = '		';


-- -----------------------------------------------------
-- Table `Form_SectionIndex`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_SectionIndex` ;

CREATE  TABLE IF NOT EXISTS `Form_SectionIndex` (
  `FSI_SectionID` INT(11) NOT NULL ,
  `FSI_LanguageID` INT(11) NOT NULL ,
  `FSI_Title` VARCHAR(255) NULL ,
  PRIMARY KEY (`FSI_SectionID`, `FSI_LanguageID`) ,
  INDEX `FK_FSI_FS` (`FSI_SectionID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_Element`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_Element` ;

CREATE  TABLE IF NOT EXISTS `Form_Element` (
  `FE_ID` INT(11) NOT NULL AUTO_INCREMENT ,
  `FE_SectionID` INT(11) NULL ,
  `FE_TypeID` INT(11) NULL ,
  `FE_Seq` INT(11) NULL ,
  PRIMARY KEY (`FE_ID`) ,
  INDEX `FK_FE_FS` (`FE_SectionID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_ElementType`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_ElementType` ;

CREATE  TABLE IF NOT EXISTS `Form_ElementType` (
  `FET_ID` INT(11) NOT NULL AUTO_INCREMENT ,
  `FET_Title` VARCHAR(255) NULL ,
  PRIMARY KEY (`FET_ID`) ,
  INDEX `FK_FE_FET` (`FET_ID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;

INSERT INTO `Form_ElementType` (`FET_ID`, `FET_Title`) VALUES (1, 'Zone Text');
INSERT INTO `Form_ElementType` (`FET_ID`, `FET_Title`) VALUES (2, 'Question');

-- -----------------------------------------------------
-- Table `Form_Text`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_Text` ;

CREATE  TABLE IF NOT EXISTS `Form_Text` (
  `FT_ID` INT(11) NOT NULL AUTO_INCREMENT ,
  `FT_ElementID` INT(11) NULL ,
  PRIMARY KEY (`FT_ID`) ,
  INDEX `FK_FT_FE` (`FT_ElementID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_TextIndex`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_TextIndex` ;

CREATE  TABLE IF NOT EXISTS `Form_TextIndex` (
  `FTI_FormTextID` INT(11) NOT NULL ,
  `FTI_LanguageID` INT(11) NULL ,
  `FTI_Text` LONGTEXT NULL ,
  PRIMARY KEY (`FTI_FormTextID`, `FTI_LanguageID`) ,
  INDEX `FK_FTI_FT` (`FTI_FormTextID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_Question`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_Question` ;

CREATE  TABLE IF NOT EXISTS `Form_Question` (
  `FQ_ID` INT(11) NOT NULL AUTO_INCREMENT,
  `FQ_ElementID` INT(11) NULL ,
  `FQ_TypeID` INT(11) NULL ,
  PRIMARY KEY (`FQ_ID`) ,
  INDEX `FK_FQ_FE` (`FQ_ElementID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_QuestionType`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_QuestionType` ;

CREATE  TABLE IF NOT EXISTS `Form_QuestionType` (
  `FQT_ID` INT(11) NOT NULL ,
  `FQT_TypeName` VARCHAR(20) NOT NULL ,
  `FQT_ImageLink` VARCHAR(255) NULL ,
  INDEX `FK_FQT_FQ` (`FQT_ID` ASC) ,
  PRIMARY KEY (`FQT_ID`) ,
  INDEX `FQT_TypeName` (`FQT_TypeName` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;

INSERT INTO `Form_QuestionType` (`FQT_ID`, `FQT_TypeName`, `FQT_ImageLink`) VALUES
(1, 'Text','/form/index/get-icon/format/48x48/prefix/icon-text/ext/png'),
(2, 'MultiLine','/form/index/get-icon/format/48x48/prefix/icon-multiline/ext/png'),
(3, 'Select','/form/index/get-icon/format/48x48/prefix/icon-select/ext/png'),
(4, 'SingleChoice','/form/index/get-icon/format/48x48/prefix/icon-singlechoice/ext/png'),
(5, 'MultiChoice','/form/index/get-icon/format/48x48/prefix/icon-multichoice/ext/png'),
(6, 'Date','/form/index/get-icon/format/48x48/prefix/icon-date/ext/png');

-- -----------------------------------------------------
-- Table `Form_QuestionTypeIndex`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_QuestionTypeIndex` ;

CREATE  TABLE IF NOT EXISTS `Form_QuestionTypeIndex` (
  `FQTI_QuestionTypeID` INT(11) NOT NULL ,
  `FQTI_Title` VARCHAR(255) NULL ,
  `FQTI_LanguageID` INT(11) NULL ,
  `FQTI_Description` TEXT NULL ,
  PRIMARY KEY (`FQTI_QuestionTypeID`, `FQTI_LanguageID`) ,
   INDEX `FK_FQTI_FQT` (`FQTI_QuestionTypeID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;

INSERT INTO `Form_QuestionTypeIndex` (`FQTI_QuestionTypeID`, `FQTI_Title`, `FQTI_LanguageID`, `FQTI_Description`) VALUES
(1, 'Texte', 1, 'Texte sur une simple ligne'),
(1, 'Text', 2, 'One row for text input'),
(2, 'Multi ligne', 1, 'Une zone de texte sur plusieurs lignes'),
(2, 'Multiline Text', 2, 'A textarea for response'),
(3, 'Sélection', 1, 'Une liste déroulante'),
(3, 'Select list', 2, 'A drop down list'),
(4, 'Choix unique', 1, 'Une réponse possible à cocher'),
(4, 'Single choice', 2, 'Only one resposne'),
(5, 'Choix multiple', 1, 'Sélection de plusieurs réponses'),
(5, 'Multi choices', 2, 'Possibility to check more than one option'),
(6, 'Date', 1, 'Date'),
(6, 'Date', 2, 'Date');

-- -----------------------------------------------------
-- Table `Form_QuestionIndex`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_QuestionIndex` ;

CREATE  TABLE IF NOT EXISTS `Form_QuestionIndex` (
  `FQI_QuestionID` INT(11) NULL ,
  `FQI_LanguageID` INT(11) NULL ,
  `FQI_Title` VARCHAR(255) NULL ,
  PRIMARY KEY (`FQI_QuestionID`, `FQI_LanguageID`) ,
  INDEX `FK_FQI_FQ` (`FQI_QuestionID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_QuestionValidationType`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_QuestionValidationType` ;

CREATE  TABLE IF NOT EXISTS `Form_QuestionValidationType` (
  `FQVT_ID` INT(11) NOT NULL ,
  `FQVT_TypeName` VARCHAR(20) NOT NULL ,
  `FQVT_Category` VARCHAR(3) NOT NULL ,
  `FQVT_Regex` VARCHAR(255) NULL ,
  PRIMARY KEY (`FQVT_ID`) ,
  INDEX `FK_FQVT_FQV` (`FQVT_ID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;

INSERT INTO `Form_QuestionValidationType` (`FQVT_ID`, `FQVT_TypeName`, `FQVT_Category`, `FQVT_Regex`) VALUES
(1, 'Required', 'VAL', ''),
(2, 'MinChar', 'MIX', ''),
(3, 'MaxChar', 'MIX', ''),
(4, 'Numeric', 'VAL', ''),
(5, 'Email', 'VAL', '^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$'),
(6, 'PhoneNum', 'VAL', '^(?:\([2-9]\d{2}\)\ ?|[2-9]\d{2}(?:\-?|\ ?))[2-9]\d{2}[- ]?\d{4}$'),
(7, 'ZipCode', 'VAL', '^((\d{5}-\d{4})|(\d{5})|([A-Z]\d[A-Z]\s\d[A-Z]\d))$'),
(8, 'MinChoices', 'MIX', ''),
(9, 'MaxChoices', 'MIX', ''),
(10, 'Date', 'OPT', ''),
(11, 'DateMin', 'VAL', ''),
(12, 'DateMax', 'VAL', ''),
(13, 'TitleRequired', 'MIX', ''),
(14, 'DescrRequired', 'MIX', ''),
(15, 'Height', 'OPT', ''),
(16, 'Sort', 'OPT', ''),
(17, 'Class', 'OPT', '');

-- -----------------------------------------------------
-- Table `Form_QuestionValidation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_QuestionValidation` ;

CREATE  TABLE IF NOT EXISTS `Form_QuestionValidation` (
  `FQV_QuestionID` INT(11) NOT NULL ,
  `FQV_TypeID` INT(11) NULL ,
  `FQV_Value` VARCHAR(255) NULL ,
  `FQV_QuestionCondID` INT(11) NULL ,
  PRIMARY KEY ( `FQV_QuestionID` , `FQV_TypeID` ) ,
  INDEX `FK_FQV_FQ` (`FQV_QuestionID` ASC) ,
  INDEX `fk_Form_QuestionValidation_Form_QuestionValidationType1` (`FQV_TypeID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;

-- -----------------------------------------------------
-- Table `Form_QuestionValidationTypeIndex`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_QuestionValidationTypeIndex` ;

CREATE  TABLE IF NOT EXISTS `Form_QuestionValidationTypeIndex` (
  `FVTI_ValidationTypeID` INT(11) NOT NULL ,
  `FVTI_Title` VARCHAR(255) NULL ,
  `FVTI_LanguageID` INT(11) NULL ,
  PRIMARY KEY (`FVTI_ValidationTypeID`, `FVTI_LanguageID`) ,
  INDEX `FK_FQVTI_FQVT` (`FVTI_ValidationTypeID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;

INSERT INTO `Form_QuestionValidationTypeIndex` (`FVTI_ValidationTypeID`, `FVTI_Title`, `FVTI_LanguageID`) VALUES
(1, 'Obligatoire', 1),
(1, 'Required', 2),
(2, 'Minimum de caractères', 1),
(2, 'Minimum of characters', 2),
(3, 'Maximum de caractères', 1),
(3, 'Maximun of characters', 2),
(4, 'Numérique', 1),
(4, 'Numeric', 2),
(5, 'Courriel', 1),
(5, 'Email', 2),
(6, 'Télép  hone', 1),
(6, 'Phone number', 2),
(7, 'Code postal', 1),
(7, 'Zip code', 2),
(8, 'Minimum de choix obligatoire', 1),
(8, 'Minimum of required choices', 2),
(9, 'Maximum de choix obligatoire', 1),
(9, 'Maximum of required choices', 2),
(10, 'Date', 1),
(10, 'Date', 2),
(11, 'Date minimum', 1),
(11, 'Minimum date', 2),
(12, 'Date maximum', 1),
(12, 'Maximum date', 2),
(13, 'Titre obligatoire', 1),
(13, 'Title required', 2),
(14, 'Description obligatoire', 1),
(14, 'Description required', 2),
(15, 'Hauteur', 1),
(15, 'Height', 2),
(16, 'Tri alphanumérique', 1),
(16, 'Sorting alphanumeric', 2),
(17, 'Classe', 1),
(17, 'Class', 2);
;

-- -----------------------------------------------------
-- Table `Form_QuestionOption`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_QuestionOption` ;

CREATE  TABLE IF NOT EXISTS `Form_QuestionOption` (
  `FQO_ID` INT(11) NOT NULL AUTO_INCREMENT ,
  `FQO_QuestionID` INT(11) NULL ,
  `FQO_TypeID` INT(11) NULL,
  `FQO_Value` VARCHAR(255) NULL ,
  PRIMARY KEY (`FQO_ID`) ,
  INDEX `FK_FQO_FQ` (`FQO_QuestionID` ASC))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_ResponseOption`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_ResponseOption` ;

CREATE  TABLE IF NOT EXISTS `Form_ResponseOption` (
  `FRO_ID` INT(11) NOT NULL AUTO_INCREMENT ,
  `FRO_QuestionID` INT(11) NULL ,
  `FRO_Type` VARCHAR(25) NULL ,
  `FRO_Seq` INT(11) NULL ,
  `FRO_Default` INT(2) NULL ,
  `FRO_Other` INT(2) NULL ,
  PRIMARY KEY (`FRO_ID`) ,
  INDEX `FK_FRO_FQ` (`FRO_QuestionID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_ResponseOptionIndex`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_ResponseOptionIndex` ;

CREATE  TABLE IF NOT EXISTS `Form_ResponseOptionIndex` (
  `FROI_ResponseOptionID` INT(11) NULL ,
  `FROI_LanguageID` INT(11) NULL ,
  `FROI_Label` VARCHAR(255) NULL ,
  PRIMARY KEY (`FROI_ResponseOptionID`, `FROI_LanguageID`) ,
  INDEX `FK_FROI_FRO` (`FROI_ResponseOptionID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `FormIndex`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `FormIndex` ;

CREATE  TABLE IF NOT EXISTS `FormIndex` (
  `FI_FormID` INT(11) NOT NULL ,
  `FI_LanguageID` INT(11) NULL ,
  `FI_Title` VARCHAR(255) NULL ,
  PRIMARY KEY ( `FI_FormID` , `FI_LanguageID` ) ,
  INDEX `FK_FI_Form` (`FI_FormID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_Notification`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_Notification` ;

CREATE  TABLE IF NOT EXISTS `Form_Notification` (
  `FN_FormID` INT(11) NOT NULL ,
  `FN_Email` VARCHAR(255) NULL ,
  `FN_Type` INT(11) NULL ,
  PRIMARY KEY (`FN_FormID`, FN_Email) ,
  INDEX `FK_FN_Form` (`FN_FormID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_QuestionConditionType`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `From_QuestionConditionType` ;

CREATE  TABLE IF NOT EXISTS `Form_QuestionConditionType` (
  `FQCT_ID` INT(11) NOT NULL AUTO_INCREMENT ,
  `FQCT_Operator` VARCHAR(255) NULL ,
  PRIMARY KEY (`FQCT_ID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_DisplayCondition`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_DisplayCondition` ;

CREATE  TABLE IF NOT EXISTS `Form_DisplayCondition` (
  `FDC_ID` INT(11) NOT NULL AUTO_INCREMENT ,
  `FDC_QuestionCondID` INT(11) NULL ,
  `FDC_TypeID` INT(11) NULL ,
  `FDC_Value` VARCHAR(255) NULL ,
  PRIMARY KEY (`FDC_ID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_QuestionDisplayCondition`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_QuestionDisplayCondition` ;

CREATE  TABLE IF NOT EXISTS `Form_QuestionDisplayCondition` (
  `FQDC_QuestionID` INT(11) NOT NULL ,
  `FQDC_QuestionCondID` INT(11) NULL ,
  `FQDC_TypeID` INT(11) NULL ,
  `FQDC_Value` VARCHAR(255) NULL ,
  PRIMARY KEY (`FQDC_QuestionID`) ,
  INDEX `FK_FQC_FQ` (`FQDC_QuestionID` ASC) ,
  INDEX `FK_FQC_FQCT` (`FQDC_TypeID` ASC) ,
  INDEX `FK_FQDC_FDC` (`FQDC_QuestionCondID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_QuestionType_ConditionType`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_QuestionType_ConditionType` ;

CREATE  TABLE IF NOT EXISTS `Form_QuestionType_ConditionType` (
  `FQTCT_QuestionTypeID` INT(11) NOT NULL ,
  `FQTCT_ConditionTypeID` INT(11) NULL ,
  PRIMARY KEY (`FQTCT_QuestionTypeID`) ,
  INDEX `FK_FQTCT_FQT` (`FQTCT_QuestionTypeID` ASC) ,
  INDEX `FK_FQTCT_FQCT` (`FQTCT_ConditionTypeID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;


-- -----------------------------------------------------
-- Table `Form_QuestionConditionTypeIndex`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_QuestionConditionTypeIndex` ;

CREATE  TABLE IF NOT EXISTS `Form_QuestionConditionTypeIndex` (
  `FQCTI_ConditionTypeID` INT(11) NOT NULL ,
  `FQCTI_LanguageID` INT(11) NULL ,
  `FQCTI_Title` VARCHAR(255) NULL ,
  PRIMARY KEY ( `FQCTI_ConditionTypeID` , `FQCTI_LanguageID` ) ,
  INDEX `FK_FQCTI_FQCT` (`FQCTI_ConditionTypeID` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Form_SectionDisplayCondition`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Form_SectionDisplayCondition` ;

CREATE  TABLE IF NOT EXISTS `Form_SectionDisplayCondition` (
  `FSDC_SectionID` INT(11) NOT NULL ,
  `FSCD_DisplayConditionID` INT(11) NULL ,
  PRIMARY KEY (`FSDC_SectionID`) ,
  INDEX `FK_FSDC_FS` (`FSDC_SectionID` ASC) ,
  INDEX `FK_FSDC_FDC` (`FSCD_DisplayConditionID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_swedish_ci;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

--
-- Données pour Mofidier la table pour les sites anciens
--


REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID) VALUES
('Module_form', 1, 'Formulaire', 'cible', '', 0, 13),
('Module_form', 2, 'Form', 'cible', '', 0, 13),
('management_module_form_list', 1, 'Liste des formulaires', 'cible', '', 0, 13),
('management_module_form_list', 2, 'List of the forms', 'cible', '', 0, 13),
('form_module_name', 1, 'Formulaire', 'cible', '', 0, 13),
('form_module_name', 2, 'Form', 'cible', '', 0, 13),
('header_list_form_description', 1, 'Cette page vous permet de gérer vos formulaires.', 'cible', '', 0, 13),
('header_list_form_description', 2, 'This page allows to manage the forms.', 'cible', '', 0, 13),
('button_add_form', 1, 'Ajouter un formulaire', 'cible', '', 0, 13),
('button_add_form', 2, 'Add a new form', 'cible', '', 0, 13),
('header_list_form_text', 1, 'Liste des formulaires', 'cible', '', 0, 13),
('header_list_form_text', 2, 'Forms list', 'cible', '', 0, 13),
('list_column_FI_Title', 1, 'Cette page permet d\'afficher la liste des formulaires.', 'cible', '', 0, 13),
('list_column_FI_Title', 2, 'This page is to consult the forms list.', 'cible', '', 0, 13),
('list_column_F_ID', 1, "#", 'cible', '', 0, 13),
('list_column_F_ID', 2, "#", 'cible', '', 0, 13),
('form_label_has_notification', 1, 'Notification', 'cible', '', 0, 13),
('form_label_has_notification', 2, 'Notification', 'cible', '', 0, 13),
('form_label_has_profil', 1, 'Doit être connecté', 'cible', '', 0, 13),
('form_label_has_profil', 2, 'Must be logged in', 'cible', '', 0, 13),
('form_label_has_captcha', 1, 'Ajouter un captcha de contrôle', 'cible', '', 0, 13),
('form_label_has_captcha', 2, 'Add a captcha for control', 'cible', '', 0, 13),
('button_response', 1, 'Liste des réponses', 'cible', '', 0, 13),
('button_response', 2, 'Response list', 'cible', '', 0, 13),
('header_list_form_response_text', 1, 'Liste des réponses du formulaire <em>%FORM_TITLE%</em>', 'cible', '', 0, 13),
('header_list_form_response_text', 2, 'Response list for the form <em>%FORM_TITLE%</em>', 'cible', '', 0, 13),
('header_list_form_response_description', 1, 'Cette page permet de consulter la lsite des réponses pour ce formulaire.', 'cible', '', 0, 13),
('header_list_form_response_description', 2, ' This page is to consult the response list for this form.', 'cible', '', 0, 13),
('header_add_form_text', 1, 'Créer un nouveau formulaire', 'cible', '', 0, 13),
('header_add_form_text', 2, 'Create a new form', 'cible', '', 0, 13),
('header_add_form_description', 1, 'Cette page permet de créer un nouveau formulaire. <p>En cliquant sur Sauvegarder, vous accèderez à la gestion du contenu de votre formulaire.</p>', 'cible', '', 0, 13),
('header_add_form_description', 2, 'This page is to create a new form. <p> By clicking on save, you will access to the content management of your new form. </p>', 'cible', '', 0, 13),
('header_edit_form_text', 1, 'Formulaire <em>%EDIT_FORM_TITLE%</em> <br /> Gestion du contenu', 'cible', '', 0, 13),
('header_edit_form_text', 2, 'Form <em>%EDIT_FORM_TITLE%</em> <br /> Content management', 'cible', '', 0, 13),
('header_edit_form_description', 1, 'Cette page permet de gérer le contenu du formulaire.<br /> Ajoutez ou supprimez les éléments selon vos besoins.<p> Faites glisser les sections dans le centre de la page puis ajoutez-y les questions que vous voulez.</p>', 'cible', '', 0, 13),
('header_edit_form_description', 2, 'This page is to manage and edit the form. Add or delete elements you need.<p> Drag and drop into the central part of the page and add the questions type you need.</p>', 'cible', '', 0, 13),
('form_manage_block_contents', 1, 'Gestion du formulaire associé', 'cible', '', 0, 13),
('form_manage_block_contents', 2, 'Manage associated form', 'cible', '', 0, 13),
('form_show_params', 1, 'Afficher les options.', 'cible', '', 0, 13),
('form_show_params', 2, 'Display options.', 'cible', '', 0, 13),
('form_hide_params', 1, 'Masquer les options.', 'cible', '', 0, 13),
('form_hide_params', 2, 'Hide the options.', 'cible', '', 0, 13),
('form_section_title_label', 1, 'Titre de la section', 'cible', '', 0, 13),
('form_section_title_label', 2, 'Section title.', 'cible', '', 0, 13),
('form_section_repeat_label', 1, 'Répéter la section', 'cible', '', 0, 13),
('form_section_repeat_label', 2, 'Repeat this section', 'cible', '', 0, 13),
('form_section_showtitle_label', 1, 'Afficher le titre', 'cible', '', 0, 13),
('form_section_showtitle_label', 2, 'Show the title', 'cible', '', 0, 13),
('form_section_repeatMin_label', 1, 'Nombre de répétition minimum', 'cible', '', 0, 13),
('form_section_repeatMin_label', 2, 'Minimum number of repetition', 'cible', '', 0, 13),
('form_section_repeatMax_label', 1, 'Nombre de répétition maximum', 'cible', '', 0, 13),
('form_section_repeatMax_label', 2, 'Maximum number of repetition', 'cible', '', 0, 13),
('form_section_pageBreak_alert', 1, 'Cette section possède déjà un saut de page.', 'cible', '', 0, 13),
('form_section_pageBreak_alert', 2, 'This section has already got a page break.', 'cible', '', 0, 13),
('form_section_deleteSection_confirm', 1, 'Désirez-vous supprimer définitivement cet élément?', 'cible', '', 0, 13),
('form_section_deleteSection_confirm', 2, 'Do you want to permanently delete this element?', 'cible', '', 0, 13),
('form_section_deletePageBreak_confirm', 1, 'Désirez-vous supprimer définitivement ce saut de page?', 'cible', '', 0, 13),
('form_section_deletePageBreak_confirm', 2, 'Do you want to permanently delete this page break?', 'cible', '', 0, 13),
('form_section_defaultTitle', 1, 'Nouvelle section', 'cible', '', 0, 13),
('form_section_defaultTitle', 2, 'New section', 'cible', '', 0, 13),
('form_popup_textzone_edit_title', 1, 'Ajouter ou Editer un texte', 'cible', '', 0, 13),
('form_popup_textzone_edit_title', 2, 'Add or Edit a text', 'cible', '', 0, 13),
('form_popup_textzone_edit_description', 1, 'Cette page permet d\'ajouter un nouveau texte ou d\'éditer celui qui était déjà enregistré.</p><p>Vous pouvez réaliser votre propre mise en page.</p>', 'cible', '', 0, 13),
('form_popup_textzone_edit_description', 2, 'This page allows to add a new text or to edit the last one registered.</p><p>You can set your own style. </p>', 'cible', '', 0, 13),
('form_element_edit_link', 1, 'Editer le texte', 'cible', '', 0, 13),
('form_element_edit_link', 2, 'Edit the text', 'cible', '', 0, 13),
('form_element_delete_link', 1, 'Supprimer', 'cible', '', 0, 13),
('form_element_delete_link', 2, 'Delete', 'cible', '', 0, 13),
('form_question_text_label', 1, 'Libellé de la question', 'cible', '', 0, 13),
('form_question_text_label', 2, 'Title of the question', 'cible', '', 0, 13),
('form_question_option_obligatoire', 1, 'Obligatoire', 'cible', '', 0, 13),
('form_question_option_obligatoire', 2, 'Requiered', 'cible', '', 0, 13),
('form_response_option_add_button', 1, 'Ajouter une option', 'cible', '', 0, 13),
('form_response_option_add_button', 2, 'Add an option', 'cible', '', 0, 13),
('form_zonetext_label_toedit', 1, 'Cliquer sur ce texte ou sur <em>Editer le texte</em> pour ajouter un descriptif à votre formulaire.', 'cible', '', 0, 13),
('form_zonetext_label_toedit', 2, 'Click this text or <em>Edit the text</em> to add a form description.', 'cible', '', 0, 13),
('form_validation_message_captcha_error', 1, 'La valeur du code de vérification est fausse.', 'cible', '', 0, 13),
('form_validation_message_captcha_error', 2, 'Captcha value is wrong.', 'cible', '', 0, 13),
('form_detail_label_for_response_options', 1, 'Précisez', 'cible', '', 0, 13),
('form_detail_label_for_response_options', 2, 'Detail', 'cible', '', 0, 13),
('form_select_option_view_form_form', 1, 'Vue par défaut', 'cible', '', 0, 13),
('form_select_option_view_form_form', 2, 'Default view', 'cible', '', 0, 13),
('form_select_option_view_form_detail', 1, ' ', 'cible', '', 0, 13),
('form_select_option_view_form_detail', 2, ' ', 'cible', '', 0, 13),
('form_default_text_value', '1', 'Cliquer sur ce texte ou sur <em>Editer le texte</em> pour ajouter un descriptif à votre formulaire.', 'cible', '', 0, 13),
('form_default_text_value', '2', 'Click this text or <em>Edit the text</em> to add a description to the form.', 'cible', '', 0, 13),
('form_notification_emails_info', '1', "Liste d'email séparée par des point virgules (;)", 'cible', '', 0, 13),
('form_notification_emails_info', '2', "Email list separated by semi colon (;)", 'cible', '', 0, 13),
('form_detail_label_choose', '1', 'Choisir', 'client', '', '1', '13'),
('form_detail_label_choose', '2', 'Choose', 'client', '', '1', '13');
