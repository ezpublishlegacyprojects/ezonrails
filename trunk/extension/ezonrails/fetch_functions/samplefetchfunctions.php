<?php
/**
 * A class used to demonstrate how 'automatic fetch function registration' work:
 * all static methods will be registered as fetches
 *
 * @version $Id$
 * @license code licensed under the GNU General Public License v2.0
 * @author G. Giunta
 * @copyright (C) G. Giunta 2011
 */

class samplefetchfunctions
{

    /// A basic fetch action: echo back received parameter
    static function fetchMirror( $IllBeBack )
    {
        return $IllBeBack;
    }

}

?>