-- phpMyAdmin SQL Dump
-- version 3.1.3
-- http://www.phpmyadmin.net
--
-- Serveur: 209.222.235.12:3306
-- Version SVN: $Id: moduleRssreaderCreate.sql 1367 2013-12-27 04:19:31Z ssoares $

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------


REPLACE INTO `Modules` (`M_ID`, `M_Title`, `M_MVCModuleTitle`, `M_SiteType`) VALUES
(19, 'RSS reader', 'rssreader', 's,m');

REPLACE INTO ModuleViews (MV_ID, MV_Name, MV_ModuleID) VALUES
(19001, 'homepagelist', 19),
(19002, 'listall', 19);

REPLACE INTO ModuleViewsIndex (MVI_ModuleViewsID, MVI_LanguageID, MVI_ActionName) VALUES
(19001, 1, 'rss-accueil'),
(19001, 2, 'rss-home'),
(19002, 1, 'toutes'),
(19002,	2, 'list-all');


REPLACE INTO `Static_Texts` (`ST_Identifier`, `ST_LangID`, `ST_Value`, `ST_Type`, `ST_Desc_backend`, `ST_Editable`,  `ST_ModuleID`) VALUES
('form_label_rss_reader_title', 1, 'Titre du RSS', 'cible', '', 0, 19),
('form_label_rss_reader_link_max', 2, 'Number of links to display', 'cible', '', 0, 19),
('form_label_rss_reader_show_web', 1, 'Afficher en ligne', 'cible', '', 0, 19),
('form_label_rss_reader_link', 1, 'Lien RSS (français)', 'cible', '', 0, 19),
('form_label_rss_reader_link', 2, 'RSS feed (french)', 'cible', '', 0, 19),
('form_label_rss_reader_link_max', 1, 'Nombre de lien à afficher', 'cible', '', 0, 19),
('form_label_rss_reader_title', 2, 'RSS Title', 'cible', '', 0, 19),
('rssreader_module_name', 1, 'Lecteur RSS', 'cible', '', 0, 19),
('rssreader_module_name', 2, 'RSS reader', 'cible', '', 0, 19),
('management_module_rss_reader_rssreader_manage', 1, 'Gestion des lecteurs RSS', 'cible', '', 0, 19),
('management_module_rss_reader_rssreader_manage', 2, 'RSS reader management', 'cible', '', 0, 19),
('form_label_rss_reader_show_web', 2, 'Show online', 'cible', '', 0, 19),
('form_select_option_view_rssreader_homepagelist', 1, 'Page d''accueil', 'cible', '', 0, 19),
('form_select_option_view_rssreader_homepagelist', 2, 'Homepage', 'cible', '', 0, 19),
('form_select_option_view_rssreader_listall', 1, 'Liste détaillée', 'cible', '', 0, 19),
('form_select_option_view_rssreader_listall', 2, 'Detailed List', 'cible', '', 0, 19),
('form_label_rss_reader_link_en', 1, 'Lien RSS (anglais)', 'cible', '', 0, 19),
('form_label_rss_reader_link_en', 2, 'RSS feed (english)', 'cible', '', 0, 19),
('module_rssreader', 1, 'Lecteur RSS', 'cible', '', 0, 19),
('module_rssreader', 2, 'RSS reader', 'cible', '', 0, 19),
('see_all_rss_reader_text', 1, 'Voir tous les articles', 'client', '', 0, 19),
('see_all_rss_reader_text', 2, 'See all posts', 'client', '', 0, 19),
('rss_reader_no_rss_feed', 1, 'Aucun articles', 'cible', '', 0, 19),
('rss_reader_no_rss_feed', 2, 'No Posts', 'cible', '', 0, 19),
('see_details_rss_reader_text', 1, 'Détail', 'client', '', 0, 19),
('see_details_rss_reader_text', 2, 'Read more', 'client', '', 0, 19);
