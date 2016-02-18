<?php

    class Rss_IndexController extends Cible_Controller_Action
    {
        public function allfeedsAction()
        {
           $this->view->assign('lang', Zend_Registry::get('languageSuffix'));
        }

        public function newsfeedAction()
        {
           $this->view->assign('lang', Zend_Registry::get('languageSuffix'));

           $categoryID = Cible_FunctionsBlocks::getBlockParameter($this->_getParam('BlockID'), 1);

           $this->view->assign('CategoryID', $categoryID);
        }

        public function allcategoriesAction()
        {
            $this->view->assign('lang', Zend_Registry::get('languageSuffix'));

            $select = $this->_db->select();
            $select->from('CategoriesIndex')
                   ->joinleft('Categories', 'Categories.C_ID = CategoriesIndex.CI_CategoryID')
                   ->where('CategoriesIndex.CI_LanguageID = ?', Zend_Registry::get('languageID'))
                   ->where('Categories.C_ShowInRss = ?', 1)
                   ->group('Categories.C_ID')
                   ->order('CI_Title DESC');
            $categories = $this->_db->fetchAll($select);
            $this->view->assign('categories', $categories);
            $this->view->assign('domainName', $this->_config->domainName);
        }


        public function readAction(){
            $this->disableLayout();
            $this->disableView();

            $ressource_dir = dirname( dirname( __FILE__ ) ) . '/ressources';

            $feed = $this->_request->getParam('feed');
            $catID = $this->_request->getParam('catID');
            $feed = str_replace('.xml', "-{$catID}.xml", $feed);
            $rssLimit = Cible_FunctionsCategories::getRssItemsLimitPerCategory($catID);
            $langId = Cible_FunctionsGeneral::getLanguageID( $this->_request->getParam('lang') );

            Zend_Registry::set('languageID', $langId);

            $lang = $this->_request->getParam('lang');
            $feed_url = Zend_Registry::get('absolute_web_root') . "/{$this->_request->getParam('lang')}/{$this->_request->getParam('feed')}";

            if( file_exists("{$ressource_dir}/{$lang}/{$feed}") ){
                $lastUpdate = new Zend_Date( filemtime("{$ressource_dir}/{$lang}/{$feed}") );
                $now = new Zend_Date();
            }

            if( file_exists("{$ressource_dir}/{$lang}/{$feed}") && !$now->isLater($lastUpdate->addMinute($this->_config->rss->refresh->delay)) ){
                $xml = file_get_contents("{$ressource_dir}/{$lang}/{$feed}");
            } else {
                $file = fopen("{$ressource_dir}/{$lang}/{$feed}", "w");

                $select = $this->_db->select();
                $select->from('NewsData')
                       ->joinLeft('NewsIndex', 'NewsData.ND_ID = NewsIndex.NI_NewsDataID')
                       ->joinleft('CategoriesIndex', 'CategoriesIndex.CI_CategoryID = NewsData.ND_CategoryID')
                       ->joinleft('Categories', 'Categories.C_ID = NewsData.ND_CategoryID')
                       ->where('CategoriesIndex.CI_LanguageID = ?', $langId)
                       ->where('NewsIndex.NI_LanguageID = ?', $langId)
                       ->where('Categories.C_ID = ?', $catID)
                       ->where('Categories.C_ShowInRss = ?', 1)
                       ->order('ND_Date DESC')
                       ->limit($rssLimit);
                 //die($select);
                $news = $this->_db->fetchAll($select);

                $feedPubDate = date("r", strtotime('now'));

                $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";

                $xml .= "<rss version=\"2.0\">";
                $xml .= "<channel>";

                $xml .="<title>" . $this->_config->site->title->$langId . "</title>";
                $xml .="<description>";
                $xml .= str_replace('##SITE_NAME##', $this->_config->site->title->$langId, $this->view->getCibleText('rss_read_description_field_label'));
                $xml .="</description>";
                $xml .="<lastBuildDate>{$feedPubDate}</lastBuildDate>";
                $xml .="<link>{$feed_url}</link>";

                foreach($news as $details){

                    if(($details['NI_Status'])==1){
                    // Titres des nouvelles (FR)
                    $title = str_replace("'","'", $details['NI_Title']);
                    $title = str_replace("<br>", "<br />", $title);
                    $title = str_replace("&", "&amp;", $title);

                    // Textes des nouvelles (FR)
                    $description = str_replace("'","'", $this->KeepTextOnly($details["NI_Brief"]));
                    $description = htmlspecialchars($description);
                    $description = str_replace("<br>", "<br />", $description);

                    $pubDate = date("r", strtotime($details["ND_ReleaseDate"]));

                    $news_url = Zend_Registry::get('absolute_web_root') . '/' . Cible_FunctionsCategories::getPagePerCategoryView($details["ND_CategoryID"],'details'). "/ID/{$details['ND_ID']}";

                    $xml .="<item>";
                        $xml .="<title>{$title}</title>";
                        $xml .="<description>{$description}</description>";
                        $xml .="<pubDate>{$pubDate}</pubDate>";
                        $xml .="<guid>{$news_url}</guid>";
                        $xml .="<link>{$news_url}</link>";
                    $xml .="</item>";
                }
                }

                $xml .="</channel>";
                $xml .="</rss>";

                fwrite($file, $xml);
                fclose($file);
            }

            $this->_response->setHeader('Content-Type','application/rss+xml');
            echo $xml;
        }

        function KeepTextOnly($Texte){
            $search = array ('@<script[^>]*?>.*?</script>@si', // Strip out javascript
                             '@<[\/\!]*?[^<>]*?>@si',          // Strip out HTML tags
                             '@[\r\n]+@',                      // Fcl- : On enlève les sauts de ligne
                             '@\s\s+@',                        // Strip out white space (modif par Fcl-)
                             '@&(quot|#34);@i',                // Replace HTML entities
                             '@&(amp|#38);@i',
                             '@&(lt|#60);@i',
                             '@&(gt|#62);@i',
                             '@&(nbsp|#160);@i',
                             '@&(iexcl|#161);@i',
                             '@&(cent|#162);@i',
                             '@&(pound|#163);@i',
                             '@&(copy|#169);@i',
                             '@&#(\d+);@e');                    // evaluate as php

            $replace = array (' ',
                              ' ',
                              ' ',                              // Fcl- : Sauts de ligne = espace
                              ' ',                              // Fcl- : Plusieurs espaces = un seul
                              '"',
                              '&',
                              '<',
                              '>',
                              ' ',
                              chr(161),
                              chr(162),
                              chr(163),
                              chr(169),
                              'chr(\1)');

            $Texte = html_entity_decode($Texte, null, 'UTF-8');
            $Texte = str_replace('&rsquo;','\'',$Texte);
            $Texte = str_replace('&ndash;','-',$Texte);
            $Texte = preg_replace($search, $replace, $Texte);
            $Texte = preg_replace('/\s\s+/', ' ', $Texte);
            $Texte = str_replace('&hellip;','...',$Texte);
            $Texte = str_replace('&oelig;','&#339;',$Texte);
            $Texte = str_replace('&OElig;','&#338;',$Texte);

            return $Texte;
        }
    }
?>