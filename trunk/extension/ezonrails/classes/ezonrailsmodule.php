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
 * Class that scans for existing controllers to inject their methods as views in the eZP module 'ezonrails'
 */
class eZOnRailsModule
{

    private static $viewList = null;
    private static $functionList = null;
    private static $controllersExtraInfo = null;

    public static function viewList()
    {
        self::initControllers();
        return self::$viewList;
    }

    public static function functionList()
    {
        self::initControllers();
        return self::$functionList;
    }

    public static function controllersExtraInfo()
    {
        self::initControllers();
        return self::$controllersExtraInfo;
    }

    /**
    * Scan all extensions that declare to contain controllers, to build list of
    * known controllers and actions.
    * (caching function: scans only once)
    */
    protected static function initControllers()
    {
        if ( self::$viewList === null )
        {
            self::$viewList = array();
            self::$functionList = array();
            self::$controllersExtraInfo = array();

            $ini = eZINI::instance( 'ezonrails.ini' );
            $extdir = eZExtension::baseDirectory();
            foreach ( $ini->variable( 'GeneralSettings', 'ExtensionRepositories' ) as $ext )
            {
                $controllerdir = $extdir . '/' . $ext . '/controllers';
                if ( is_dir( $controllerdir ) )
                {
                    foreach ( scandir( $controllerdir ) as $file )
                    {
                        if ( substr( $file, -4 ) == '.php' )
                        {
                            $classname = substr( $file, 0, -4 );

                            $controllerFilePath = $controllerdir . '/' . $file;
                            require_once( $controllerFilePath );

                            if ( class_exists( $classname ) )
                            {
                                // the view is named as the class
                                // to execute the view, a permission is needed on an access function of the same name
                                // the actual php file in use is always the same
                                // NB: we cannot declare actual view params, as they depend on the action
                                self::$viewList[$classname] = array(
                                    'script' => 'controllerexecutor.php',
                                    'functions' => array( $classname ),
                                    'params' => array( 'action' )
                                );

                                // for every public method of the class, we add a limitation on the access function
                                $methods = array();
                                foreach ( get_class_methods( $classname ) as $methodname )
                                {
                                    $func = new ReflectionMethod( $classname, $methodname );
                                    if (
                                        !$func->isPrivate() &&
                                        !$func->isProtected() &&
                                        !$func->isConstructor() &&
                                        !$func->isDestructor() &&
                                        !$func->isAbstract()
                                    )
                                    {
                                        $methods[$methodname] = array( 'Name' => $methodname, 'value' => $methodname );
                                    }
                                }

                                self::$functionList[$classname] = array( 'Action' => array( 'name' => 'Action', 'values' => $methods ) );

                                self::$controllersExtraInfo[$classname] = array( 'path' => $controllerFilePath );
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
            );
            
            self::$controllersExtraInfo = array(
                'samplecontroller' => array( 'path' => 'extension/ezonrails/controllers/samplecontroller.php' ),
            );*/
        }
    }
}

?>