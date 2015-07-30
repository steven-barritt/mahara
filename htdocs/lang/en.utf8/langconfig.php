<?php
/**
 *
 * @package    mahara
 * @subpackage lang
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

defined('INTERNAL') || die();

$string['parentlanguage'] = '';
$string['strftimedate'] = '%%e %%B %%Y'; //6 November 2014
$string['strftimedateshort'] = '%%d %%B'; //06 November
$string['strftimedatevshort'] = '%%d %%b'; //06 Nov
$string['strftimedatetime'] = '%%d %%B %%Y, %%l:%%M %%p'; //06 November 2014, 5:57 PM
$string['strftimedatetimeshort'] = '%%Y/%%m/%%d %%H:%%M'; //2014/11/06 17:57
$string['strftimedaydate'] = '%%A, %%d %%B %%Y'; //Thursday, 06 November 2014
$string['strftimedaydatetime'] = '%%A, %%d %%B %%Y, %%l:%%M %%p'; //Thursday, 06 November 2014, 5:57 PM
$string['strftimedayshort'] = '%%A, %%d %%B'; // Thursday, 06 November
$string['strftimeday'] = '%%A'; //Thursday
$string['strftimenmonth'] = '%%m'; //Thursday
$string['strftimenday'] = '%%d'; //Thursday
$string['strftimedayvshort'] = '%%a %%d %%b'; //Thu 06 Nov
$string['strftimedayvshortyear'] = '%%a, %%d %%b %%y'; //Thu 06 Nov 14
$string['strftimedayvshorttime'] = '%%k:%%M %%a, %%d %%b'; //17:57 Thu, 06 Nov
$string['strftimetimedayyearshort'] = '%%I:%%M%%p %%a, %%d %%b %%Y'; //05:57PM Thu, 06 Nov 2014
$string['strftimedaytime'] = '%%a, %%k:%%M'; //Thu, 17:57
$string['strftimemonthyear'] = '%%B %%Y'; //November 2014
$string['strftimenotspecified']  = 'Not specified';
$string['strftimerecent'] = '%%d %%b, %%k:%%M'; //06 Nov, 17:57
$string['strftimerecentyear'] = '%%d %%b %%Y, %%k:%%M %%p'; // 06 Nov 2014, 17:57 PM
$string['strftimerecentfull'] = '%%a, %%d %%b %%Y, %%l:%%M %%p'; //Thu, 06 Nov 2014, 5:57 PM
$string['strftimetime'] = '%%l:%%M %%p';
$string['strftimetimezero'] = '%%k:%%M';
$string['strfdaymonthyearshort'] = '%%d/%%m/%%Y'; //  22/09/2015
$string['strfdateofbirth'] = '%%Y/%%m/%%d';
$string['strftimew3cdatetime'] = '%%Y-%%m-%%dT%%H:%%M:%%S%%z';
$string['strftimew3cdate'] = '%%Y-%%m-%%d';
$string['thislanguage'] = 'English';
$string['locales'] = 'en_US.utf8,en_GB.utf8,en,english-us,english-uk,english';

// Rule to choose from the language's plural forms.
// See the gettext manual, http://www.gnu.org/s/hello/manual/gettext/Plural-forms.html
// For language packs converted from PO format, the following strings and function will be
// automatically generated from the expression in the PO file's "Plural-Forms:" header.
$string['pluralrule'] = 'n != 1';
$string['pluralfunction'] = 'plural_en_utf8';
function plural_en_utf8($n) {
    return (int) $n != 1;
}
