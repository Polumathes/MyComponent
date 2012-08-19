<?php
/**
 * Important: You should almost never need to edit this file,
 * except to add components that it won't handle (e.g., permissions,
 * users, policies, policy templates, ACL entries, and Form
 * Customization rules), and most of those might better be handled
 * in a script resolver, which you can add without editing this file.
 *
 *
 * Build Script for [[+packageName]] extra
 *
 * Copyright 2012 by Bob Ray <http://bobsguides.com>
 * Created on [[+createdon]]
 *
[[+license]]
 *
 * @package [[+packageNameLower]]
 * @subpackage build
 */

/**
 * This is the template for the build script, which creates the
 * transport.zip file for your extra.
 *

 */
/* See the tutorial at http://http://bobsguides.com/mycomponent-tutorial.html
 * for more detailed information about using the package.
 */


$config = dirname(__FILE__) . '/build.config.php';

if (file_exists($config)) {
    $props = @include $config;
} else {
    die('Could not find main config file at ' . $config);
}

/* @var $configFile string - set in included file */
if (empty($props)) {
    die('Could not find project config file at ' . $configFile);
}

/* Set package info be sure these are all set in the project config file */
define('PKG_NAME', $props['packageName']);
define('PKG_NAME_LOWER', $props['packageNameLower']);
define('PKG_VERSION', $props['version']);
define('PKG_RELEASE', $props['release']);
define('PKG_CATEGORY', $props['category']);

/* Set package options - you can set these manually, but it's
 * recommended to let them be generated autmatically from
 * the project config file.
 */

$hasAssets = $props['hasAssets']; /* Transfer the files in the assets dir. */
$hasCore = $props['hasCore'];   /* Transfer the files in the core dir. */
$hasSnippets = !empty($props['elements']['snippets']);
$hasChunks = !empty($props['elements']['chunks']);
$hasTemplates = !empty($props['elements']['templates']);
$hasTemplateVariables = !empty($props['elements']['templates']);
$hasPlugins = !empty($props['elements']['plugins']);
$hasResources = !empty($props['resources']);
$hasValidator = !empty($props['validators']); /* Run a validator before installing anything */
$hasResolver = !empty ($props['resolvers']); /* Run a resolver after installing everything */
$hasSetupOptions = !empty($props['install.options']); /* HTML/PHP script to interact with user */
$hasPropertySets = !empty($props['propertySets']);
$hasResolvers = !empty($props['resolvers']);
$hasPluginResolver = !empty($props['pluginEvents']);
$hasTvResolver = !empty($props['templateVarTemplates']);

$hasMenu = !empty($props['menus']); /* Add items to the MODx Top Menu */
$hasSettings = !empty($props['newSystemSettings']); /* Add new MODx System Settings */
$hasSubPackages = !empty($props['subPackages']);
$minifyJS = $props['minifyJS'];


/* Note: property sets are connected to elements in the script
 * resolver (see _build/data/resolvers/install.script.php)
 */

// $hasSubPackages = true; /* add in other component packages (transport.zip files)*/
/* Note: The package files will be copied to core/packages but will
 * have to be installed manually with "Add New Package" and "Search
 * Locally for Packages" in Package Manager. Be aware that the
 * copied packages may be older versions than ones already
 * installed. This is necessary because Package Manager's
 * autoinstall of the packages is unreliable at this point. 
 */

/******************************************
 * Work begins here
 * ****************************************/

/* set start time */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* define sources */
$root = dirname(dirname(__FILE__)) . '/';
$sources= array (
    'root' => $root,
    'build' => $root . '_build/',
    /* note that the next two must not have a trailing slash */
    'source_core' => $root.'core/components/'.PKG_NAME_LOWER,
    'source_assets' => $root.'assets/components/'.PKG_NAME_LOWER,
    'resolvers' => $root . '_build/resolvers/',
    'validators'=> $root . '_build/validators/',
    'data' => $root . '_build/data/',
    'docs' => $root . 'core/components/' . PKG_NAME_LOWER . '/docs/',
    'install_options' => $root . '_build/install.options/',
    'packages'=> $root . 'core/packages',
);
unset($root);

/* Instantiate MODx -- if this require fails, check your
 * _build/build.config.php file
 */
require_once $sources['build'].'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

/* load builder */
$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');

/* minify JS */

if ($minifyJS) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Creating js-min file(s)');
    // require $sources['build'] . 'utilities/jsmin.class.php';
    require MYCOMPONENT_ROOT . '_build/utilities/jsmin.class.php';

    $jsDir = $sources['source_assets'] . '/js';

    if (is_dir($jsDir)) {
        $files = scandir($jsDir);
        foreach ($files as $file) {
            /* skip non-js and already minified files */
            if ( (!stristr($file, '.js') || strstr($file,'min'))) {
                continue;
            }

            $jsmin = JSMin::minify(file_get_contents($sources['source_assets'] . '/js/' . $file));
            if (!empty($jsmin)) {
                $outFile = $jsDir . '/' . str_ireplace('.js', '-min.js', $file);
                $fp = fopen($outFile, 'w');
                if ($fp) {
                    fwrite($fp, $jsmin);
                    fclose($fp);
                    $modx->log(modX::LOG_LEVEL_INFO, 'Created: ' . $outFile);
                } else {
                    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not open min.js outfile: ' . $outFile);
                }
            }
        }

    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Could not open JS directory.');
    }
}



/* create category  The category is required and will automatically
 * have the name of your package
 */
/* @var $category modCategory */
$category= $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category',PKG_CATEGORY);

/* add snippets */
if ($hasSnippets) {
    $modx->log(modX::LOG_LEVEL_INFO,'Adding in snippets.');
    $snippets = include $sources['data'].'transport.snippets.php';
    /* note: Snippets' default properties are set in transport.snippets.php */
    if (is_array($snippets)) {
        $category->addMany($snippets, 'Snippets');
    } else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding snippets failed.'); }
}
/* ToDo: Implement Property Sets */
if ($hasPropertySets) {
    $modx->log(modX::LOG_LEVEL_INFO,'Adding in property sets.');
    $propertySets = include $sources['data'].'transport.propertysets.php';
    //  note: property set' properties are set in transport.propertysets.php
    if (is_array($propertySets)) {
        $category->addMany($propertySets, 'PropertySets');
    } else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding property sets failed.'); }
}
if ($hasChunks) { /* add chunks  */
    $modx->log(modX::LOG_LEVEL_INFO,'Adding in chunks.');
    /* note: Chunks' default properties are set in transport.chunks.php */    
    $chunks = include $sources['data'].'transport.chunks.php';
    if (is_array($chunks)) {
        $category->addMany($chunks, 'Chunks');
    } else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding chunks failed.'); }
}


if ($hasTemplates) { /* add templates  */
    $modx->log(modX::LOG_LEVEL_INFO,'Adding in templates.');
    /* note: Templates' default properties are set in transport.templates.php */
    $templates = include $sources['data'].'transport.templates.php';
    if (is_array($templates)) {
        if (! $category->addMany($templates,'Templates')) {
            $modx->log(modX::LOG_LEVEL_INFO,'addMany failed with templates.');
        };
    } else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding templates failed.'); }
}

if ($hasTemplateVariables) { /* add template variables  */
    $modx->log(modX::LOG_LEVEL_INFO,'Adding in Template Variables.');
    /* note: Template Variables' default properties are set in transport.tvs.php */
    $tvs = include $sources['data'].'transport.tvs.php';
    if (is_array($tvs)) {
        $category->addMany($tvs, 'TemplateVars');
    } else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding Template Variables failed.'); }
}


if ($hasPlugins) {
    $modx->log(modX::LOG_LEVEL_INFO,'Adding in Plugins.');
    $plugins = include $sources['data'] . 'transport.plugins.php';
     if (is_array($plugins)) {
        $category->addMany($plugins);
     }
}

/* Create Category attributes array dynamically
 * based on which elements are present
 */

$attr = array(xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
);

if ($hasValidator) {
      $attr[xPDOTransport::ABORT_INSTALL_ON_VEHICLE_FAIL] = true;
}

if ($hasSnippets) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Snippets'] = array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        );
}

if ($hasPropertySets) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['PropertySets'] = array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        );
}

if ($hasChunks) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Chunks'] = array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        );
}

if ($hasPlugins) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Plugins'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'name',
    );
}

if ($hasTemplates) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Templates'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'templatename',
    );
}

if ($hasTemplateVariables) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['TemplateVars'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'name',
    );
}

/* create a vehicle for the category and all the things
 * we've added to it.
 */
$vehicle = $builder->createVehicle($category,$attr);

if ($hasValidator) {
    $validators = explode(',', $props['validators']);
    if (! empty($validators)) {
        foreach ($validators as $validator) {
            if ($validator == 'default') {
                $validator = PKG_NAME_LOWER;
            }
            $modx->log(modX::LOG_LEVEL_INFO,'Adding in ' . $validator . ' Validator.');
            $vehicle->validate('php',array(
                'source' => $sources['validators'] . $validator . '.validator.php',
            ));
        }
    }
}

/* Package in script resolvers, if any */
$resolvers = $props['resolvers'];
$resolvers = empty($resolvers)? array() : explode(',', $resolvers);
if ($hasPluginResolver) {
    $resolvers[] = 'plugin';
}
if ($hasTvResolver) {
    $resolvers[] = 'tv';
}
if (!empty ($resolvers)) {
    foreach ($resolvers as $resolver) {
        if ($resolver == 'default') {
            $resolver = PKG_NAME_LOWER;
        }
        $modx->log(modX::LOG_LEVEL_INFO, 'Adding in ' . $resolver . ' resolver.');
        $vehicle->resolve('php', array(
            'source' => $sources['resolvers'] . $resolver . '.resolver.php',
        ));
    }
}
/* This section transfers every file in the local
 mycomponents/mycomponent/assets directory to the
 target site's assets/mycomponent directory on install.
 If the assets dir. has been renamed or moved, they will still
 go to the right place.
 */

if ($hasCore) {
    $vehicle->resolve('file',array(
            'source' => $sources['source_core'],
            'target' => "return MODX_CORE_PATH . 'components/';",
        ));
}

/* This section transfers every file in the local 
 mycomponents/mycomponent/core directory to the
 target site's core/mycomponent directory on install.
 If the core has been renamed or moved, they will still
 go to the right place.
 */

    if ($hasAssets) {
        $vehicle->resolve('file',array(
            'source' => $sources['source_assets'],
            'target' => "return MODX_ASSETS_PATH . 'components/';",
        ));
    }

/* Add subpackages */
/* The transport.zip files will be copied to core/packages
 * but will have to be installed manually with "Add New Package and
 *  "Search Locally for Packages" in Package Manager
 */

if ($hasSubPackages) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding in subpackages.');
     $vehicle->resolve('file',array(
        'source' => $sources['packages'],
        'target' => "return MODX_CORE_PATH;",
        ));
}

/* Put the category vehicle (with all the stuff we added to the
 * category) into the package 
 */
$builder->putVehicle($vehicle);



/* Transport Resources */
/* ToDo: Move this to top */
if ($hasResources) {
    $resources = include $sources['data'].'transport.resources.php';
    if (!is_array($resources)) {
        $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in resources.');
    } else {
        $attributes= array(
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'pagetitle',
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'ContentType' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
    ),
);
foreach ($resources as $resource) {
    $vehicle = $builder->createVehicle($resource,$attributes);
    $builder->putVehicle($vehicle);
}
        $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($resources).' resources.');
    }
    unset($resources,$resource,$attributes);
}

/* Transport Menus */
if ($hasMenu) {
    /* load menu */
    $modx->log(modX::LOG_LEVEL_INFO,'Packaging in menu...');
    $menu = include $sources['data'].'transport.menu.php';
    if (empty($menu)) {
        $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in menu.');
    } else {
        $vehicle= $builder->createVehicle($menu,array (
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'text',
        xPDOTransport::RELATED_OBJECTS => true,
        xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
            'Action' => array (
                xPDOTransport::PRESERVE_KEYS => false,
                xPDOTransport::UPDATE_OBJECT => true,
                xPDOTransport::UNIQUE_KEY => array ('namespace','controller'),
            ),
        ),
));
        $builder->putVehicle($vehicle);

        $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($menu).' menu items.');
        unset($vehicle,$menu);
    }
}

/* load system settings */
if ($hasSettings) {
    $settings = include $sources['data'].'transport.settings.php';
    if (!is_array($settings)) {
        $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in settings.');
    } else {
        $attributes= array(
            xPDOTransport::UNIQUE_KEY => 'key',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
        );
        foreach ($settings as $setting) {
            $vehicle = $builder->createVehicle($setting,$attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($settings).' System Settings.');
        unset($settings,$setting,$attributes);
    }
}

/* Next-to-last step - pack in the license file, readme.txt, changelog,
 * and setup options 
 */
$attr = array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
);

if (!empty($props['install.options'])) {
    $attr['setup-options'] = array(
        'source' => $sources['install_options'] . 'user.input.php',
    );
} else {
    $attr['setup-options'] = array();
}
$builder->setPackageAttributes($attr);

/* Last step - zip up the package */
$builder->pack();

/* report how long it took */
$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(xPDO::LOG_LEVEL_INFO, "Package Built.");
$modx->log(xPDO::LOG_LEVEL_INFO, "Execution time: {$totalTime}");
exit();