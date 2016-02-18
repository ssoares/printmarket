<?php

/**
 * Cible
 *
 *
 * @category   Cible
 * @package    Cible_View
 * @subpackage Cible_View_Helper
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaires
 *             (http://www.ciblesolutions.com)
 * @version    $Id: CssFromLess.php 1788 2015-06-18 20:07:52Z ssoares $
 */

/**
 * Allow to build css files from less engine
 *
 * @category   Cible
 * @package    cible_View
 * @subpackage Cible_View_Helper
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaire
 *             (http://www.ciblesolutions.com)
 */
class Cible_View_Helper_CssFromLess extends Zend_View_Helper_Abstract {

    public function cssFromLess() {
        $this->config = Zend_Registry::get('config');
        $lessParserCacheDir = APPLICATION_PATH . '/cache/lessparser/';
        $lessParserCacheDir .= $this->view->currentSite . '/';
        $lessParserImportDir = APPLICATION_PATH . '/cache/lessimport/';
        $lessParserImportDir .= $this->view->currentSite . '/';
        if (!is_dir($lessParserCacheDir)){
            mkdir($lessParserCacheDir);
        }
        if (!is_dir($lessParserImportDir)){
            mkdir($lessParserImportDir);
        }
        $styles = array(
            'site.less' => 'site.css',
            'integration.less' => 'integration.css'
        );

        $types = array(
            'normal' => '',
            'mobile' => 'mobile-'
        );

        $modules = Cible_FunctionsModules::getModules();
        if ($this->config->bootstrap->enabled){
            $styles['bootstrap.less'] = 'bootstrap.css';
        }
        foreach ($modules as $module){
            $styles[$module['M_MVCModuleTitle'] . '.less'] = $module['M_MVCModuleTitle'] . '.css';
        }
        foreach ($styles as $less => $css) {
            foreach ($types as $type) {
                $source = Zend_Registry::get('fullDocumentRoot') . $this->view->locateFile($type . $less);
                $output = str_replace('less', 'css', $source);
//                $output = Zend_Registry::get('fullDocumentRoot') . $this->view->locateFile($type . $css);
                $importCacheFile = $lessParserImportDir . $type . $less . '.json';
                $this->_importsList = array();
                $hasToBeParsed = false;

                if (is_file($source)) {
                    //On détermine si le fichier doit être parsé.
                    $date = filemtime($source);
                    $date2 = (is_file($output)) ? filemtime($output) : 0;
                    if ($this->config->lessImportCache->enabled) {

                        if (is_file($importCacheFile)) {
                            //On regarde la date des fichiers racines
                            if ($date > $date2) {
                                $hasToBeParsed = true;
                            } else {
                                //On regarde tous les fichiers dans le json pour déterminer si l'un d'eux doivent être reparsé
                                $currentImportList = json_decode(file_get_contents($importCacheFile));
                                //$baseLess = str_replace($less, '', $source);
                                foreach ($currentImportList as $import) {
                                    //$fileImport = $baseLess . $import;
                                    $fileImport = $import;
                                    if (is_file($fileImport)) {

                                        $date3 = filemtime($fileImport);
                                        if ($date3 > $date2) {
                                            $hasToBeParsed = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        } else {
                            //Si le fichier de cache n'existe pas, on parse
                            $hasToBeParsed = true;
                        }
                    } else {
                        $hasToBeParsed = ($date > $date2);
                    }

                    if ($hasToBeParsed) {
                        $parser = new Cible_LessParser();
                        $parser->SetOptions(array(
                            'cache_dir' => $lessParserCacheDir,
                            'compress' => ($this->config->cssMinify->enabled),
                            'sourceMap' => !($this->config->cssMinify->enabled)
                        ));
                        /* //register function feature
                          function superMegaCallback($param) {
                          $param->value = "#" . $param->value;
                          return $param;
                          }
                          $parser->registerFunction('splouch', 'superMegaCallback');
                         */

                        //$parser->SetCacheDir('cache');
                        try {
                            $parser->parseFile($source, "");
                            $minifiedCss = $parser->getCss();
                            if ((bool)$this->config->lessImportCache->enabled) {
                                file_put_contents($importCacheFile, json_encode($parser->AllParsedFiles()));
                            }

                            file_put_contents($output, $minifiedCss);
                        } catch (Cible_LessException_Compiler $e) {
                            echo '<h1>LESS ERROR</h1>';
                            echo "<b>While generating:</b> {$source}<br />";
                            echo "<b>Message:</b>" . $e->getMessage() . "<br />";
                            echo "<pre>";
                            echo $e;
                            echo "</pre>";
                            exit;
                            exit();
                        }
                    }
                }
            }
        }
    }

}
