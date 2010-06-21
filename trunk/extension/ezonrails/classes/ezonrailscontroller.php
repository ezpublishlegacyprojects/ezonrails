<?php
/**
 *
 *
 * @version $Id$
 * @license code licensed under the GNU General Public License v2.0
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010
 */

/**
* Class that implements basic controller functionality. Can be extended by new controllers (but it is not mandatory)
*/
abstract class ezOnRailsController
{
    /**
    * Renders the controller output using a template ('view' in rails terms).
    * @param string $template
    * @param array $templateparams the params to be set to the template as hash: name => value
    */
    protected function renderView( $template, $templateparams = array() )
    {
        require_once( "kernel/common/template.php" );
        $tpl = templateInit();
        foreach ( $templateparams as $key => $val )
        {
            $tpl->setVariable( $key, $val );
        }
        return $tpl->fetch( $template );
    }
}

?>