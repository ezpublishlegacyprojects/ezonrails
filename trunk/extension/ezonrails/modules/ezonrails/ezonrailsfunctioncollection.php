<?php
/**
 *
 *
 * @version $Id$
 * @copyright 2011
 */

/**
 * Wraps in a single eZ-exposed fetch functions all the functions declared by user
 */
class eZOnRailsFunctionCollection
{
    static function fetch()
    {
        $parameters = func_get_args();
        if ( !count( $parameters ) )
        {
            eZDebug::writeError( "Unknown fetch function called. Logic bug in ezonrails?", __METHOD__ );
            return array( 'error' => "Unknown fetch function called" );
        }
        $method = array_shift( $parameters );

        // recover the (possibly cached) list of methods
        require_once( eZExtension::baseDirectory(). '/ezonrails/modules/ezonrails/function_definition.php' );

        if ( !isset( $FunctionList[$method] ) )
        {
            eZDebug::writeError( "Unknown fetch function called: $method. Possible hacker attempt?", __METHOD__ );
            return array( 'error' => "Unknown fetch function called: $method" );
        }



        try
        {
            $result = call_user_func_array( $method, $parameters );
        }
        catch( exception $e )
        {
            eZDebug::writeError( "Exception executing fetch function $method: " . $e->getMessage(), __METHOD__ );
            return array( 'error' => "Exception executing fetch function $method" );
        }

        return array( 'result' => $result );
    }
}

?>