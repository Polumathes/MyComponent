<?php

/**
 * Controller index.php for the MyComponent package
 * @author Bob Ray
 * 2/4/11
 *
 * @package mycomponent

 */

require_once dirname(dirname(__FILE__)).'/model/mycomponent/mycomponent.class.php';
$mycomponent = new MyComponent($modx, $scriptProperties);
// return $mycomponent->init('mgr');
return true;