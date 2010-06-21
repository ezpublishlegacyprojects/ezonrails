<?php
/**
 *
 * @author G. Giunta
 * @license
 * @version $Id$
 * @copyright (C) 2010
 *
 * @todo add a caching layer to avoid all the introspection on every module view execution
 */

$cachefile = eZSys::cacheDirectory() . '/ezonrails/module.php';
$clusterfile = eZClusterFileHandler::instance( $cachefile );
if ( $clusterfile->exists() )
{
    $clusterfile->fetch();
    require_once( $cachefile );
}
else
{
    $Module = array( 'name' => 'eZ Rails' );
    $ViewList = eZOnRailsModule::viewList();
    $FunctionList = eZOnRailsModule::functionList();

    $cachecontents = "<?php
\$Module = " . var_export( $Module, true ) . ";
\$ViewList = " . var_export( $ViewList, true ) . ";
\$FunctionList = " . var_export( $FunctionList, true ) . ";
?>";
     $clusterfile->fileStoreContents( $cachefile, $cachecontents );
}


?>