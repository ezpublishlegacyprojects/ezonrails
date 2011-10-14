<?php
/**
 *
 * @version $Id$
 * @license code licensed under the GNU General Public License v2.0
 * @author G. Giunta
 * @copyright (C) G. Giunta 2011
 */

$siteIni = eZINI::instance();
$developmentMode = ( $siteIni->variable( 'TemplateSettings', 'DevelopmentMode' ) == 'enabled' );
$cachefile = eZSys::cacheDirectory() . '/ezonrails/function_definition.php';
$clusterfile = eZClusterFileHandler::instance( $cachefile );
if ( !$developmentMode && $clusterfile->exists() )
{
    $clusterfile->fetch();
    require_once( $cachefile );
}
else
{
    $FunctionList = eZOnRailsFetchFunction::functionList();

    if ( !$developmentMode )
    {

        $cachecontents = "<?php
\$FunctionList = " . var_export( $FunctionList, true ) . ";
?>";
        $clusterfile->fileStoreContents( $cachefile, $cachecontents );
    }
}

?>