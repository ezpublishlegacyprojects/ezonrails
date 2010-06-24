<?php
/**
 * A class used to demonstrate how 'controllers' work
 *
 * @version $Id$
 * @license code licensed under the GNU General Public License v2.0
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010
 */

/**
* NB: making this a subclass  of ezOnRailsController is not necessary at all.
* It is only done here to take advantage of the renderView method
*/
class samplecontroller extends ezOnRailsController
{

    /// A basic action. Is executed when no action is specified in the url after the controller
    function index()
    {
        return 'hello world';
    }

    /**
    * An action using a template to render its output
    * nb: a php warning will be generated when a user goes to /ezonrails/samplecontroller/action1 and omits the param p1
    */
    function action1( $p1 )
    {
        // NB: the template used here is not delivered with this extension!
        // If you want to use templates to render the output of your methods remember to:
        // 1. declare your extension as design extension
        // 2. create the templates you use
        return $this->renderView( 'design:controllers/samplecontroller/action1.tpl', array( 'FirstParamReceived' => $p1 ) );
    }

    /**
    * An action that takes more control of the results, in this case the path
    * (see ezpedia page on 'module' for a list of valid members for return array).
	* It also uses func_get_args to make the list of parameters/arguments even more dynamic
    */
    function action2()
    {
        $arg_list = func_get_args();
        return array(
            'content' => 'action2 was executed. ' . count( $arg_list ) . ' parameters received: '. htmlspecialchars( print_r( $arg_list, true ) ),
            'path' => array( array( 'url' => false, 'text' => 'hide controller name from path...' ) )
        );
    }

    /**
    * An action that takes more control of the results, in this case the pagelayout.
    */
    function action3()
    {
        return array(
            'content' => 'action3 has executed, sets layout to print',
            'pagelayout' => 'print_pagelayout.tpl'
        );
    }

    static function action4()
    {
        return 'static functions are ok too';
    }
}

?>