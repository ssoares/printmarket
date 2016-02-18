-- Codep our report newsletter C28
INSERT INTO `ModuleViews` (`MV_ID`, `MV_Name`, `MV_ModuleID`) VALUES ('8009', 'resubscribe', '8');
INSERT INTO `ModuleViewsIndex`(`MVI_ModuleViewsID`,`MVI_LanguageID`,`MVI_ActionName`)VALUES
(8009,1,'reinscription'),
(8009,2,'resubscribe');
ALTER TABLE `NewsletterProfiles`
ADD COLUMN `NP_TypeID` INT(11) NULL AFTER `NP_Categories`,
ADD COLUMN `NP_SubscriptionDate` DATE NULL AFTER `NP_TypeID`;

REPLACE INTO `Static_Texts`(`ST_Identifier`,`ST_LangID`,`ST_Value`,`ST_Type`,`ST_Desc_backend`,`ST_Editable`,`ST_ModuleID`)VALUES
("form_select_option_view_newsletter_resubscribe",1,"Ré-inscription",'cible',"",0,0),
("form_select_option_view_newsletter_resubscribe",2,"Resubscribe",'cible',"",0,0),
("newsletter_title_reabonnement_text",1,"Réabonnement ",'client',"Titre Message loi anti-spam",1,8),
("newsletter_title_reabonnement_text",2,"Re-subscribe",'client',"Titre Message loi anti-spam",1,8),
("newsletter_reabonnement_text",1,"Afin de nous conformer à la Loi canadienne anti-pourriel, nous devons demander votre consentement pour vous faire parvenir nos messages. Après cette date, les gens qui ne se seront pas réabonnés ne recevront plus d'information de notre part.",'client',"Message loi anti-spam",1,8),
("newsletter_reabonnement_text",2,"In order to comply with the Canadian Anti-Spam Legislation, we must ask your permission to send you our messages. After this date, people who do not subscribed again will no longer receive information from us.",'client',"Message loi anti-spam",1,8),
("form_label_agree",1,"Je consens à recevoir les communications électroniques de ##SITENAME##, celle-ci incluant des nouvelles, des mises à jour, des offres et des promotions. Il est possible de retirer mon consentement à tout moment.",'cible',"",0,0),
("form_label_agree",2,"I agree to receive ##SITENAME## electronic communications including news, updates, promotions and offers. You can withdraw your consent at any time.",'cible',"",0,0),
("newsletter_your_email_address",1,"Votre adresse courriel : ",'cible',"",0,0),
("newsletter_your_email_address",2,"Your email address: ",'cible',"",0,0),
("form_enum_typeClient",1,"Type de profil",'cible',"",0,0),
("form_enum_typeClient",2,"Profile type",'cible',"",0,0),
("form_enum_datelimit_gt6m",1,"Plus de 6 mois",'cible',"",0,0),
("form_enum_datelimit_gt6m",2,"More than 6 month",'cible',"",0,0),
("form_enum_datelimit_lt6m",1,"Moins de 6 mois",'cible',"",0,0),
("form_enum_datelimit_lt6m",2,"Less than 6 month",'cible',"",0,0),
("form_enum_datelimit_gt2y",1,"Plus de 2 ans",'cible',"",0,0),
("form_enum_datelimit_gt2y",2,"More than 2 years",'cible',"",0,0),
("form_enum_datelimit_lt2y",1,"Moins de 2 ans",'cible',"",0,0),
("form_enum_datelimit_lt2y",2,"Less than 2 years",'cible',"",0,0),
("form_enum_datelimit_gt3y",1,"Plus de 3 ans",'cible',"",0,0),
("form_enum_datelimit_gt3y",2,"More than 3 years",'cible',"",0,0),
("form_enum_datelimit_lt3y",1,"Moins de 3 ans",'cible',"",0,0),
("form_enum_datelimit_lt3y",2,"Less than 3 years",'cible',"",0,0),
("newsletter_send_filter_NP_SubscriptionDate",1,"Date d'inscription",'cible',"",0,0),
("newsletter_send_filter_NP_SubscriptionDate",2,"Subscription date",'cible',"",0,0),
("newsletter_send_filter_NP_TypeID",1,"Type de profil",'cible',"",0,0),
("newsletter_send_filter_NP_TypeID",2,"Type de profil",'cible',"",0,0)
;
ALTER TABLE `References` CHANGE COLUMN `R_TypeRef` `R_TypeRef` ENUM('subscrArg','unsubscrArg','typeClient') NOT NULL ;

INSERT INTO `NewsletterFilter_ProfilesFields` (`NFPF_ProfileTableID`, `NFPF_Type`, `NFPF_Name`) VALUES
('2', 'int', 'NP_TypeID'),
('2', 'qrystr', 'NP_SubscriptionDate');
-- Fin C28

CREATE TABLE `cible_admin`.`Extranet_UsersSites` (
  `EUS_UserId` INT(11) NOT NULL,
  `EU_DefaultSite` VARCHAR(45) NOT NULL,
  `EU_SiteAccess` VARCHAR(255) NULL,
  PRIMARY KEY (`EUS_UserId`, `EU_DefaultSite`));

-- Pour ajouter manuellement les accès admin à un site.
-- Pour les multisite, mettre la liste des site séparés par un |.
REPLACE INTO `cible_admin`.`Extranet_UsersSites` (`EUS_UserId`, `EU_DefaultSite`, `EU_SiteAccess`) VALUES
(1, '##DEFAULT##', '##LIST|SITES##'),
(19, '##DEFAULT##', '##LIST|SITES##'),
(20, '##DEFAULT##', '##LIST|SITES##'),
(22, '##DEFAULT##', '##LIST|SITES##'),
(24, '##DEFAULT##', '##LIST|SITES##'),
(25, '##DEFAULT##', '##LIST|SITES##'),
(26, '##DEFAULT##', '##LIST|SITES##'),
(27, '##DEFAULT##', '##LIST|SITES##')
;

DELETE FROM `Extranet_Users` WHERE `EU_ID`='19';
DELETE FROM `Extranet_Users` WHERE `EU_ID`='20';
DELETE FROM `Extranet_Users` WHERE `EU_ID`='22';
DELETE FROM `Extranet_Users` WHERE `EU_ID`='24';
DELETE FROM `Extranet_Users` WHERE `EU_ID`='25';
DELETE FROM `Extranet_Users` WHERE `EU_ID`='26';
DELETE FROM `Extranet_UsersGroups` WHERE `EUG_UserID`='11' and`EUG_GroupID`='1';
DELETE FROM `Extranet_UsersGroups` WHERE `EUG_UserID`='19' and`EUG_GroupID`='2';
DELETE FROM `Extranet_UsersGroups` WHERE `EUG_UserID`='20' and`EUG_GroupID`='1';
DELETE FROM `Extranet_UsersGroups` WHERE `EUG_UserID`='22' and`EUG_GroupID`='1';
DELETE FROM `Extranet_UsersGroups` WHERE `EUG_UserID`='23' and`EUG_GroupID`='1';
DELETE FROM `Extranet_UsersGroups` WHERE `EUG_UserID`='24' and`EUG_GroupID`='2';
DELETE FROM `Extranet_UsersGroups` WHERE `EUG_UserID`='25' and`EUG_GroupID`='1';
DELETE FROM `Extranet_UsersGroups` WHERE `EUG_UserID`='26' and`EUG_GroupID`='1';


ALTER TABLE `Catalog_CategoriesData`
CHANGE COLUMN `CC_Online` `CC_Online` tinyint(1) NULL DEFAULT NULL COMMENT 'elem:checkbox' AFTER `CC_imageCat`;
ALTER TABLE `Catalog_ProductsData`
ADD COLUMN `P_Inactive` TINYINT(1) NULL COMMENT 'elem:checkbox' AFTER `P_Number`;

REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID, ST_RichText) VALUES
('form_label_P_Inactive', 1, "Ne pas afficher en ligne", "cible", "", 0, 14, 0),
('form_label_P_Inactive', 2, "Do not display online", "cible", "", 0, 14, 0);
REPLACE INTO Static_Texts (ST_Identifier, ST_LangID, ST_Value, ST_Type, ST_Desc_backend, ST_Editable, ST_ModuleID, ST_RichText) VALUES
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
('crop_selection_width', 2, 'Selection width', 'cible', '0', 0, 0, 0);
