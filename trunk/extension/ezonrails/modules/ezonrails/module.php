<?php
/**
 *
 * @version $Id$
 * @license code licensed under the GNU General Public License v2.0
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010
 */

$siteIni = eZINI::instance();
$developmentMode = ( $siteIni->variable( 'TemplateSettings', 'DevelopmentMode' ) == 'enabled' );
$cachefile = eZSys::cacheDirectory() . '/ezonrails/module.php';
$clusterfile = eZClusterFileHandler::instance( $cachefile );
if ( !$developmentMode && $clusterfile->exists() )
{
    $clusterfile->fetch();
    require_once( $cachefile );
}
else
{
    $Module = array( 'name' => 'eZOnRails' );
    $ViewList = eZOnRailsModule::viewList();
    $FunctionList = eZOnRailsModule::functionList();

    if ( !$developmentMode )
    {

        $cachecontents = "<?php
\$Module = " . var_export( $Module, true ) . ";
\$ViewList = " . var_export( $ViewList, true ) . ";
\$FunctionList = " . var_export( $FunctionList, true ) . ";
?>";
        $clusterfile->fileStoreContents( $cachefile, $cachecontents );
    }
}

?>