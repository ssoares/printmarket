<?php
 /**
 * Control errors from controllers or actions unknown
 *
 * The system checks whether the controller or action is present in the database and call the controller page if it does. 
 * If not present, a custom 404 page is displayed.
 *
 * PHP versions 5
 *
 * LICENSE: 
 *
 * @category   Controller
 * @package    Default
 * @author     Alexandre Beaudet <alexandre.beaudet@ciblesolutions.com>
 * @copyright  2009 CIBLE Solutions d'Affaires
 * @license    http://www.ciblesolutions.com
 * @version    CVS: <?php $ ?> Id:$
 */
 
    class ErrorController extends Cible_Extranet_Controller_Action
    {
       public function errorAction()
       {
            $errors = $this->_getParam('error_handler');

            $exception = $errors->exception;
            echo <<< End_of_error
            <p>
            <strong>Error</strong>
            {$exception->getMessage()}
            </p>
            <p>
            <strong>Stack Trace</strong>
            {$exception->getTraceAsString()}
            </p>
End_of_error;

       }       
    }
?>