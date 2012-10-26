<?php
/**
 * LexiconHelper
 * Copyright 2012 Bob Ray
 *
 * LexiconHelper is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * LexiconHelper is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * LexiconHelper; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package lexiconhelper
 * @author Bob Ray <http://bobsguides.com>
 
 *
 * Description: The LexiconHelper snippet identifies lexicon strings
 * in code and checks them against strings in a language file.
 * In the code, the references must be in this form:
 * $modx->lexicon('language_string_key')
 *
 * You must use single quotes and no spaces.
 *
 * Output can be pasted into language file for editing.
 * ToDo: More info here (~~ option, rewrite files options)
 * /

/*

  Modified: June, 2012

   
  Properties:
    @property code_path  - (required) Path to directory with code
         file. Should end in a slash.
         {core_path} and {assets_path} will be translated.

    @property code_file - (required) name of code file to be analyzed.

    @property language_path - (required) Path to directory with code
         file. Should end in a slash.
         {core_path} and {assets_path} will be translated.

    @property language_file - (optional) Path to language file.
         Default: default.inc.php

    @property language - (optional) Two-letter language code identifying
         language file to process.
         Default: en
    @property manager_language - (optional) Two-letter language code
         to use in error messages and reports. Use only to override manager
         language.
         Default: manager_language System Setting
*/

/**
 * @package = lexiconhelper
 *
 */

/* Important: All language keys in the code file must be in this form:
 *
 *      $modx->lexicon('language_string_key');
 *      $modx->lexicon("language_string_key"');
 *
 * or This form:
 *
 *      $modx->lexicon('language_string_key~~Actual Language String');
 *      $modx->lexicon("language_string_key~~Actual Language String");
 *
 * Use no spaces in the key (the left side).
 *
 *
 * With the first version, LexiconHelper will create a lexicon entry with a blank value.
 * With the second version, LexiconHelper will fill in the value as well.
 *
 * You have the option to rewrite the language file to append the new strings.
 *
 * You have the option to rewrite the code file to remove the description
 * from the lexicon() calls.
 *

*/

/* ToDo:  check System Setting descriptions */
/* ToDo: update version */
/* ToDo: update tutorial */
/* ToDo: lexicon strings in resources and chunks */

/* set start time */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);
$mem_usage = memory_get_usage();

/* @var $modx modX */

if (!defined('MODX_CORE_PATH')) {
    $path1 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/_build/build.config.php';
    if (file_exists($path1)) {
        include $path1;
    } else {
        $path2 = dirname(dirname(dirname(dirname(__FILE__)))) . '/_build/build.config.php';
        if (file_exists($path2)) {
            include($path2);
        }
    }
    if (!defined('MODX_CORE_PATH')) {
        die('[bootstrap.php] Could not find build.config.php');
    }
    require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
    $modx = new modX();
    /* Initialize and set up logging */
    $modx->initialize('mgr');
    $modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
    $modx->setLogTarget(XPDO_CLI_MODE
        ? 'ECHO'
        : 'HTML');

    /* This section will only run when operating outside of MODX */
    if (php_sapi_name() == 'cli') {
        /* Set $modx->user and $modx->resource to avoid
         * other people's plugins from crashing us */
        $modx->getRequest();
        $homeId = $modx->getOption('site_start');
        $homeResource = $modx->getObject('modResource', $homeId);

        if ($homeResource instanceof modResource) {
            $modx->resource = $homeResource;
        } else {
            echo "\nNo Resource\n";
        }
    } else {
        echo "\n<pre>\n";
    }
} else {
    echo "\n<pre>\n";
}

require_once $modx->getOption('mc.core_path', null, $modx->getOption('core_path') . 'components/mycomponent/') . 'model/mycomponent/lexiconhelper.class.php';

$lexiconHelper = new LexiconHelper($modx, $props);
    $lexiconHelper->init($modx, $props);
    $lexiconHelper->run();

echo "\n\nInitial Memory Used: " . round($mem_usage / 1048576, 2) . " megabytes";
$mem_usage = memory_get_usage();
$peak_usage = memory_get_peak_usage(true);
echo "\nFinal Memory Used: " . round($mem_usage / 1048576, 2) . " megabytes";
echo "\nPeak Memory Used: " . round($peak_usage / 1048576, 2) . " megabytes";


exit();

/* ************************************************* */
/* Code below as examples for future use */


/*$ss = $modx->getObject('modSystemSetting', array('key' => 'default_template'));

$modx->lexicon->load('core:setting');
if ($ss) {
    $desc = $modx->lexicon('setting_default_template_desc');
    echo "DESC: " . $desc;
} else {
    echo "No System Setting";
}
$np = $modx->getObject('modSnippet', array('name' => 'NewsPublisher'));
if ($np) {
    echo "\nGot NP\n";
    $props = $np->getProperties();
    if (!empty($props)) {
        echo print_r($props,true);

        $modx->lexicon->load('newspublisher:properties');
        foreach($props as $prop => $value ) {
            $desc = $modx->lexicon('np_' . $prop. '_desc');
            echo "\nDESC: " . $desc;
        }
    }
} else {
    echo 'No NewsPublisher';
}
exit;*/


/* language to use for error messages and reports */



/* look for descriptions in property files */
if ($has_properties) {
    $matches = array();
    preg_match_all("/\s*\'desc\'\s*\=\>\s*\'(.*)\'/", $code, $matches);
    $codeStrings = array_merge($codeStrings, $matches[1]);
}
$codeStringValues = array();
$codeStringKeys = array();
/* see if codestrings are in language file */
if (!empty($codeStrings)) {
    foreach($codeStrings as $key => $codeString) {

        if (strstr($codeString,'~~')) {

            $t = explode('~~', $codeString);
            $codeString = $t[0];
            $search[] = '~~' . $t[1];
            $replace[] = '';
            $codeStringValues[$codeString] = $t[1];
            $code = str_replace('~~' . $t[1], '', $code);
        }
        $codeStringKeys[] = $codeString;
        if (! isset($_lang[$codeString]) ) {
            $untranslated[] = $codeString;
        }
        if (isset($_lang[$codeString]) && empty($_lang[$codeString])) {
            $empty[] = $codeString;
        }

    }
} else {
    $output .= "\n\n   *** " . $modx->lexicon('lh.no_language_strings_in_code') . ' ***';
}

/* look for unused strings in language file */
if (isset($_lang)) {
    foreach($_lang as $key => $value) {
        if (! in_array($key, $codeStringKeys)) {
            $orphans[] = $key;
        }
    }
}


if ($rewriteCodeFile) {

    foreach ($codeFiles as $codeFile) {
        $path = $modx->getOption('code_path', $props, null);
        $path = str_replace('{core_path}', MODX_CORE_PATH, $path);
        $path = str_replace('{assets_path}', MODX_ASSETS_PATH, $path);
        $codeFile = $path . $codeFile;

        $content = file_get_contents($codeFile);
        $content = str_replace($search, $replace, $content);

        $fp = fopen($codeFile, 'w');
        if (! $fp) {
            $output .= "\nCould not open code file: " . $codeFile . "\n";
        } else {
            fwrite($fp, $content);
            fclose($fp);
        }

        $output .= "\n Lexicon descriptions removed from code file: " . $codeFile . "\n\n";
    }
}

if ($rewriteLanguageFile && (!empty($languageStrings))) {
    $content = file_get_contents($languageFile);
    $content .= "\n" . $languageStrings;
    $fp = fopen($languageFile, 'w');
    fwrite($fp, $content);
    fclose($fp);
    $output .= "\n Lexicon strings appended to language file: " . $languageFile . "\n\n";
}

if ($showCode) {
    $output .= "\n\n" . $code . "\n\n";
}
if ($outsideModx) {
    echo $output;
} else {
    return '<pre>' . $output . '</pre>';
}