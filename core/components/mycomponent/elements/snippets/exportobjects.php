<?php
/**
 * ExportObjects script for MyComponent Extra
 *
 * Copyright 2012 by Bob Ray <http://bobsguides.com>
 *
 * @author Bob Ray
 * 3/27/12
 *
 * ExportObjects is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * ExportObjects is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ExportObjects; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package exportobjects
 */
/**
 * MODx ExportObjects script
 *
 * Description:
 * ------------
 * Extracts objects (resources, chunks, snippets, etc.) from a MODX
 * install and creates code and transport build files for
 * MyComponent to use in creating a transport package
 *
 * Warning: Will overwrite code files for resources and elements
 * (except static elements) if CreateObjectFiles is set and dryRun
 * is not set.
 *
 * Warning: Will overwrite transport files, resolvers, and
 * properties for processed elements and resources if dryRun
 * is not set.
 *
 * @package exportobjects
 *
 */
/* @var $category string */

/*
 *
 * Object source files will be written to
 *  MODX_ASSETS_PATH/mycomponents/{packageNameLower}/core/components/
 * {packageNameLower}/elements/{elementName}/
 *
 * Transport files will be written to MODX_ASSETS_PATH/mycomponents/
 * {packageNameLower}/_build/data/transport.{elementName}.php
 *
 * &transportPath (directory for transport.chunks.php file)
 * defaults to assets/mycomponents/{categoryLower}/_build/data/
 *
 *
 */

include dirname(dirname(dirname(__FILE__))) . '/model/mycomponent/mycomponentproject.class.php';

/* set start time */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);
$mem_usage = memory_get_usage();

$project = new MyComponentProject();
$project->exportComponent(false);
// echo print_r(ObjectAdapter::$myObjects, true);
echo "\n\nInitial Memory Used: " . round($mem_usage / 1048576, 2) . " megabytes";
$mem_usage = memory_get_usage();
$peak_usage = memory_get_peak_usage(true);
echo "\nFinal Memory Used: " . round($mem_usage / 1048576, 2) . " megabytes";
echo "\nPeak Memory Used: " . round($peak_usage / 1048576, 2) . " megabytes";

