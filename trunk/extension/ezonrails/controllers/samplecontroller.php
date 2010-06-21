<?php
/**
 *
 *
 * @version $Id$
 * @license code licensed under the GNU General Public License v2.0
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010
 */

class samplecontroller extends ezOnRailsController
{

    /// A basic action. Is executed when no action is specified in the url after the controller
    function index()
    {
        return 'hello world';
    }

    /**
    *  An action using a template to render its output
    * nb: a php warning will be generated when user goes to /ezonrails/samplecontroller/action1 and omits the param p1
    */
    function action1( $p1 )
    {
        return $this->renderView( 'controllers/samplecontroller/action1.tpl', array( 'p1' => $p1 ) );
    }

    /**
    * An action that takes more control of the results, in this case the path
    * see ezpedia page on 'module' for a list of valid members for return array
    */
    function action2()
    {
        return array(
            'content' => 'action2 has executed',
            'path' => array( array( 'url' => false, 'text' => 'hide controller name from path...' ) )
        );
    }

    function action3()
    {
        return array(
            'content' => 'action3 has executed, sets layout to print',
            'pagelayout' => 'print_pagelayout.tpl'
        );
    }
}

?>