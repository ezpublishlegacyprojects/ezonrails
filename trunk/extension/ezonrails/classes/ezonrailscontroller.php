<?php
/**
 *
 *
 * @version $Id$
 * @license code licensed under the GNU General Public License v2.0
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010 - 2011
 */
require_once( 'kernel/common/template.php' );

/**
* Class that implements basic controller functionality. Can be extended by new controllers (but it is not mandatory)
*/
abstract class ezOnRailsController
{
    protected $actionParams = null;

    /**
     * Renders the controller output using a template ('view' in rails terms).
     * @param string $template
     * @param array $templateparams the params to be set to the template as hash: name => value
     */
    protected function renderView( $template, $templateparams = array() )
    {
        $tpl = templateInit();
        foreach ( $templateparams as $key => $val )
        {
            $tpl->setVariable( $key, $val );
        }
        return $tpl->fetch( $template );
    }

    /**
     * Fetches content node specified by its id or uses passed one.
     * Assigns fetched node to template.
     * Renders the controller output using a template ('view' in rails terms).
     * Fills correct result array.
     *
     * @param mixed $nodeOrNodeId Content node or its id
     * @param string $template
     * @param array $templateparams The params to be set to the template as hash: name => value
     */
    protected function renderNodeView( $nodeOrNodeId, $template, $templateparams = array() )
    {
        if ( is_object( $nodeOrNodeId ) )
        {
            $node = $nodeOrNodeId;
            $nodeId = $node->attribute( 'node_id' );
            $nodePath = $node->fetchPath();
        }
        else
        {
            $node = eZContentObjectTreeNode::fetch( $nodeOrNodeId );
            if ( $node === null )
            {
                $nodeId = null;
                $nodePath = null;
            }
            else
            {
                $nodeId = $nodeOrNodeId;
                $nodePath = $node->fetchPath();
            }
        }

        $tpl = templateInit();
        $tpl->setVariable( 'node', $node );
              
        return array(
            'content' => $this->renderView( $template, $templateparams ),
            'content_info' => array( 'node_id' => $nodeId ),
            'node_id' => $nodeId,
            'path' => $nodePath,
        ); 
    }

    /**
     * Runs action from current controller specified by its name passing action-specific parameters.
     * Action-specific parameters should be accessed using $this->actionParams if it is not null.
     *
     * @param string $action
     * @param array $actionParams
     * @return Result data from called action
     */
    protected function runAction( $action, $actionParams = null )
    {
        $this->actionParams = $actionParams;

        if ( !method_exists( $this, $action ) )
        {
            eZDebug::writeError( "Couldn't run action '{$action}' in ezonrails controller. It does not exist.", __CLASS__ );
            return null;
        }

        return $this->$action();
    }

    /**
     * Sets redirect status for current module.
     *
     * @param string $uri
     * @return string
     */
    protected function redirectTo( $uri )
    {
        global $module;

        $module->redirectTo( $uri );

        return null;
    }

}

?>