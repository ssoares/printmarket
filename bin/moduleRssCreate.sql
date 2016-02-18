-- phpMyAdmin SQL Dump
-- version 3.1.3
-- http://www.phpmyadmin.net
--
-- Serveur: 209.222.235.12:3306
-- Version SVN: $Id: moduleRssCreate.sql 1851 2016-01-15 22:56:34Z ssoares $

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------


REPLACE INTO `Modules` (`M_ID`, `M_Title`, `M_MVCModuleTitle`) VALUES
(12, 'RSS', 'rss');

REPLACE INTO ModuleViews (MV_ID, MV_Name, MV_ModuleID) VALUES
(12001, 'news_feed', 12);

REPLACE INTO ModuleViewsIndex (MVI_ModuleViewsID, MVI_LanguageID, MVI_ActionName) VALUES
(12001, 1, 'rss-nouvelles'),
(12001, 2, 'rss-news');

REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`, `ST_ModuleID`) VALUES
('form_category_add_to_rss_label',1,'Ajouter cette catégorie au fil RSS.','cible','', 0, 12),
('form_category_add_to_rss_label',2,'Add this category to RSS.','cible','', 0, 12),
('module_rss',1,'RSS','cible','', 0, 12),
('module_rss',2,'RSS','cible','', 0, 12),
('rss_module_name',1,'Fil RSS','cible','', 0, 12),
('rss_module_name',2,'RSS','cible','', 0, 12),
('form_select_option_view_rss_news_feed',1,'Nouvelles','cible','', 0, 12),
('form_select_option_view_rss_news_feed',2,'News','cible','', 0, 12),
('form_select_option_rss_choose_category', 1, 'Sélectionnez la catégorie de nouvelles', 'cible', '', 0, 12),
('rss_read_description_field_label', 1, "Flux RSS pour ##SITE_NAME##", 'cible', '', 0, 12),
('rss_read_description_field_label', 2, "RSS feed for ##SITE_NAME##", 'cible', '', 0, 12),
('rss_news_subscribe', 1, 'Abonnez-vous au fil RSS', 'cible', '', 0, 12),
('rss_news_subscribe', 2, 'Subscribe to RSS', 'cible', '', 0, 12);

