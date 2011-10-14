<?php
/**
 *
 *
 * @version $Id$
 * @license code licensed under the GNU General Public License v2.0
 * @author G. Giunta
 * @copyright (C) G. Giunta 2011
 */

/**
 * Class that scans existing php functions / methods to prepare their declaration as fetch functions in the eZP module 'ezonrails'
 */
class eZOnRailsFetchFunction
{

    private static $functionList = null;

    public static function functionList()
    {
        self::initFetchFunctions();
        return self::$functionList;
    }

    /**
    * Scan all extensions that declare to contain fetch functions, to build list of
    * them.
    * (caching function: scans only once)
    */
    protected static function initFetchFunctions()
    {
        if ( self::$functionList === null )
        {
            self::$functionList = array();

            $ini = eZINI::instance( 'ezonrails.ini' );
            $extdir = eZExtension::baseDirectory();
            foreach ( $ini->variable( 'GeneralSettings', 'ExtensionRepositories' ) as $ext )
            {
                $controllerdir = $extdir . '/' . $ext . '/fetch_functions';
                if ( is_dir( $controllerdir ) )
                {
                    foreach ( scandir( $controllerdir ) as $file )
                    {
                        if ( substr( $file, -4 ) == '.php' )
                        {
                            $classname = substr( $file, 0, -4 );
                            if ( class_exists( $classname ) )
                            {
                                $methods = array();
                                foreach ( get_class_methods( $classname ) as $methodname )
                                {
                                    $func = new ReflectionMethod( $classname, $methodname );
                                    if (
                                        !$func->isPrivate() &&
                                        !$func->isProtected() &&
                                        $func->isStatic() &&
                                        !$func->isAbstract()
                                    )
                                    {
                                        $params = array(
                                            array(
                                                'name' => '__actual_method__',
                                                'type' => 'string',
                                                'default' => "$classname::$methodname",
                                                'required' => false
                                            )
                                        );
                                        foreach ( $func->getParameters() as $parameter )
                                        {
                                            $paramdesc = array(
                                                'name' => $parameter->getName(),
                                                'type' => 'any',
                                                'required' => !$parameter->isOptional()
                                            );
                                            if ( $parameter->isOptional() )
                                            {
                                                $paramdesc['default'] = $parameter->getDefaultValue();
                                                if ( $paramdesc['default'] !== null )
                                                {
                                                    $paramdesc['type'] = gettype( $paramdesc['default'] );
                                                }
                                            }
                                            $params[] = $paramdesc;
                                        }

                                        self::$functionList["$classname::$methodname"] = array(
                                            // these are never used
                                            //'operation_types' => array( 'read' ),
                                            //'parameter_type' => 'standard',
                                            'call_method' => array( 'class' => 'eZOnRailsFunctionCollection', 'method' => 'fetch' ),
                                            'parameters' => $params,
                                            //
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
                else
                {
                    eZDebug::writeWarning( "Directory $controllerdir for ezonrails controllers does not exist", __METHOD__ );
                }
            }

            /*self::$viewList = array(
                'samplecontroller' => array(
                    'script' => 'controllerexecutor.php',
                    'functions' => array( 'samplecontroller' ),
                    'params' => array()
                ),
                'controller2' => array(
                    'script' => 'controllerexecutor.php',
                    'functions' => array( 'controller2' ),
                    'params' => array()
                )
            );

            self::$functionList = array(
                'samplecontroller' => array(),
                'controller2' => array(),
            );*/
        }
    }
}

?>