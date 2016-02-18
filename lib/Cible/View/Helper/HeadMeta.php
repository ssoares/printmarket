<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Placeholder.php 7078 2007-12-11 14:29:33Z matthew $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_View_Helper_Placeholder_Container_Standalone */
require_once 'Zend/View/Helper/Placeholder/Container/Standalone.php';

/**
 * Zend_Layout_View_Helper_HeadMeta
 *
 * @see        http://www.w3.org/TR/xhtml1/dtds.html
 * @uses       Zend_View_Helper_Placeholder_Container_Standalone
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Cible_View_Helper_HeadMeta extends Zend_View_Helper_HeadMeta
{
    /**
     * Types of attributes
     * @var array
     */
    protected $_typeKeys     = array('name', 'http-equiv', 'property');

    /**
     * Overload method access
     *
     * Allows the following 'virtual' methods:
     * - appendName($keyValue, $content, $modifiers = array())
     * - offsetGetName($index, $keyValue, $content, $modifers = array())
     * - prependName($keyValue, $content, $modifiers = array())
     * - setName($keyValue, $content, $modifiers = array())
     * - appendHttpEquiv($keyValue, $content, $modifiers = array())
     * - offsetGetHttpEquiv($index, $keyValue, $content, $modifers = array())
     * - prependHttpEquiv($keyValue, $content, $modifiers = array())
     * - setHttpEquiv($keyValue, $content, $modifiers = array())
     *
     * - appendProperty($keyValue, $content, $modifiers = array())
     * - offsetGetProperty($index, $keyValue, $content, $modifers = array())
     * - prependProperty($keyValue, $content, $modifiers = array())
     * - setProperty($keyValue, $content, $modifiers = array())
     *
     * @param  string $method
     * @param  array $args
     * @return Zend_View_Helper_HeadMeta
     */
    public function __call($method, $args)
    {
        if (preg_match('/^(?P<action>set|(pre|ap)pend|offsetSet)(?P<type>Name|HttpEquiv|Property)$/', $method, $matches)) {
            $action = $matches['action'];
            $type   = $this->_normalizeType($matches['type']);
            $argc   = count($args);
            $index  = null;

            if ('offsetSet' == $action) {
                if (0 < $argc) {
                    $index = array_shift($args);
                    --$argc;
                }
            }

            if (2 > $argc) {
                require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception('Too few arguments provided; requires key value, and content');
            }

            if (3 > $argc) {
                $args[] = array();
            }

            $item  = $this->createData($type, $args[0], $args[1], $args[2]);

            if ('offsetSet' == $action) {
                return $this->offsetSet($index, $item);
            }

            if ($action == 'set') {
                //var_dump($this->getContainer());
            }

            $this->$action($item);
            return $this;
        }

        return parent::__call($method, $args);
    }

    protected function _normalizeType($type)
    {
        switch ($type) {
            case 'Name':
                return 'name';
            case 'Property':
                return 'property';
            case 'HttpEquiv':
                return 'http-equiv';
            default:
                require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception(sprintf('Invalid type "%s" passed to _normalizeType', $type));
        }
    }
}
