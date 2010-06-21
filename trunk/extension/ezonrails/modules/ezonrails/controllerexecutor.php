<?php
/**
 * Script that executes all views in the ezonrails module
 *
 * @version $Id$
 * @copyright 2010
 */

$Result = array();

$module = $Params['Module'];
$controller = $Params['FunctionName'];
$parameters = $Params['Parameters'];
$action = 'index';
if ( count( $parameters ) > 0 )
{
    $action = array_shift( $parameters );
}

if ( class_exists( $controller ) )
{
    $controllerobj = new $controller();
    if ( method_exists( $controllerobj, $action ) )
    {
        eZDebug::addTimingPoint( "Action start '$controller/$action'", __METHOD__ );
        $result = call_user_func_array( array( $controllerobj, $action ), $parameters );
        eZDebug::addTimingPoint( "Action end '$controller/$action'", __METHOD__ );
    }
    else
    {
        eZDebug::writeError( "method $action does not exist in controller class $controller", __METHOD__ );
        return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}
else
{
    eZDebug::writeError( "controller class $controller does not exist", __METHOD__ );
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

// Allow the action to return a complete Result array, if it wants to set more stuff
if ( is_array( $result ) )
{
    $Result = $result;
}
else
{
    $Result['content'] = $result;
}

if ( !isset( $Result['path'] ) )
{
    if ( $action == 'index' )
    {
        $Result['path'] = array( array( 'url' => false, 'text' => $controller ) );
    }
    else
    {
        $indexurl = false;
        if ( method_exists( $controllerobj, 'index' ) )
        {
            $indexurl = "ezonrails/$controller";
        }
        $Result['path'] = array( array( 'url' => $indexurl, 'text' => $controller ),
                                 array( 'url' => false, 'text' => $action ) );
    }
}

?>