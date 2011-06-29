<?php
/**
 * Script that executes all views in the ezonrails module
 *
 * @version $Id$
 * @license code licensed under the GNU General Public License v2.0
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010
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

$access = false;
$user = eZUser::currentUser();
$accessResult = $user->hasAccessTo( 'ezonrails' , $controller );
$accessWord = $accessResult['accessWord'];
if ( $accessWord == 'yes' )
{
    $access = true;
}
else if ( $accessWord != 'no' ) // with limitation
{
    foreach ( $accessResult['policies'] as $key => $policy )
    {
        if ( isset( $policy['Action'] ) && in_array( $action, $policy['Action'] ) )
        {
            $access = true;
        }
    }
}

if ( !$access )
{
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$controllerFilePath = $GLOBALS['eZOnRailsControllers'][$controller]['path'];
require_once( $controllerFilePath );

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