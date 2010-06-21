<?php
/**
 *
 * @version $Id$
 * @license code licensed under the GNU General Public License v2.0
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010
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