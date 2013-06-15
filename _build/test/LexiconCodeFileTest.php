<?php

/**
 * Test class for MyComponentProject Bootstrap.
 * Generated by PHPUnit on 2012-03-02 at 23:02:19.
 * @outputBuffering disabled
 */

class LexiconCodeFileTest extends PHPUnit_Framework_TestCase {

    /* @var $mc MyComponentProject */
    public $mc;
    /* @var $modx modX */
    public $modx;
    /* @var $utHelpers UtHelpers */
    public $utHelpers;
    /* @var $lcf LexiconCodeFile;
     * */
    public $lcf;
    /**
     * @var $root string - target root
     */
    public $targetRoot;
    /**
     * @var $targetLexDir string - target lexicon directory
     */
    public $targetLexDir;
    /**
     * @var $targetCore string - target core directory
     */
    public $targetCore;
    /**
     * @var $targetModelDir string - target model directory
     */
    public $targetModelDir;

    /**
     * @var $targetJsDir string - target JS directory
     */
    public $targetJsDir;

    /**
     * @var $targetChunkDir string - target chunks directory
     */
    public $targetChunkDir;
    /**
     * @var $dataDir string - directory with mock files to copy
     */
    public $dataDir;

    /**
     * @var $targetPropertiesDir string - target properties dir
     */
    public $targetPropertiesDir;

    /** @var  $languages array */
    public $languages;

    /** @var  $targetDataDir string */
    public $targetDataDir;

    /** @var  $factory LexiconCodeFileFactory */
    public $factory;






    protected function setUp() {
        require_once dirname(__FILE__) . '/build.config.php';
        require_once dirname(__FILE__) . '/uthelpers.class.php';
        require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
        $this->utHelpers = new UtHelpers();
        $modx = new modX();
        $this->modx =& $modx;
        $modx->initialize('mgr');
        $modx->getService('error', 'error.modError', '', '');
        $modx->getService('lexicon', 'modLexicon');
        $modx->getRequest();
        $homeId = $modx->getOption('site_start');
        $homeResource = $modx->getObject('modResource', $homeId);

        if ($homeResource instanceof modResource) {
            $modx->resource = $homeResource;
        } else {
            echo "\nNo Resource\n";
        }

        $modx->setLogLevel(modX::LOG_LEVEL_ERROR);
        $modx->setLogTarget('ECHO');

        require_once MODX_ASSETS_PATH . 'mycomponents/mycomponent/core/components/mycomponent/model/mycomponent/mycomponentproject.class.php';
        require_once MODX_ASSETS_PATH . 'mycomponents/mycomponent/core/components/mycomponent/model/mycomponent/lexiconcodefile.class.php';

        /* @var $categoryObj modCategory */
        $this->mc = new MyComponentProject($modx);
        $this->mc->init(array(), 'unittest');
        $this->dataDir = dirname(__FILE__) . '/data/';
        $this->dataDir = str_replace('\\', '/', $this->dataDir);


        $this->targetRoot = dirname(dirname(dirname(dirname(__FILE__)))) . '/unittest/';
        $this->targetRoot = str_replace('\\', '/', $this->targetRoot);
        $this->targetRoot = strtolower($this->targetRoot);
        $this->utHelpers->rrmdir($this->targetRoot);
        @mkdir($this->targetRoot, '0644', true);

        $this->targetCore = $this->targetRoot . 'core/components/unittest/';
        @mkdir($this->targetCore, '0644', true);
        $this->targetCore = str_replace('\\', '/', $this->targetCore);

        $this->targetLexDir = $this->targetCore . 'lexicon/';
        $this->targetLexDir = str_replace('\\', '/', $this->targetLexDir);
        @mkdir($this->targetLexDir . 'en', '0644', true);
        copy($this->dataDir . 'default.inc.php',
            $this->targetLexDir . 'en/default.inc.php');
        copy($this->dataDir . 'chunks.inc.php',
            $this->targetLexDir . 'en/chunks.inc.php');
        copy($this->dataDir . 'properties.inc.php',
            $this->targetLexDir . 'en/properties.inc.php');

        $this->targetModelDir = $this->targetCore . 'model/';
        $this->targetModelDir = str_replace('\\', '/', $this->targetModelDir);
        @mkdir($this->targetModelDir, '0644', true);
        copy($this->dataDir . 'example.class.php',
        $this->targetModelDir . 'example.class.php');

        $this->targetDataDir = $this->targetRoot . '_build/data/';
        $this->targetDataDir = str_replace('\\', '/', $this->targetDataDir);
        @mkdir($this->targetDataDir, '0644', true);
        copy($this->dataDir . 'transport.menus.php',
            $this->targetDataDir . 'transport.menus.php');
        copy($this->dataDir . 'transport.settings.php',
            $this->targetDataDir . 'transport.settings.php');

        $this->targetJsDir = $this->targetRoot . 'assets/components/unittest/js/';
        $this->targetJsDir = str_replace('\\', '/', $this->targetJsDir);
        @mkdir($this->targetJsDir, '0644', true);
        copy($this->dataDir . 'example.js',
        $this->targetJsDir . 'example.js');

        $this->targetChunkDir = $this->targetCore . 'elements/chunks/';
        $this->targetChunkDir = str_replace('\\', '/', $this->targetChunkDir);
        @mkdir($this->targetChunkDir, '0644', true);
        copy($this->dataDir . 'chunk1.chunk.html',
        $this->targetChunkDir . 'chunk1.chunk.html');

        $this->targetPropertiesDir = $this->targetRoot . '_build/data/properties/';
        $this->targetPropertiesDir = str_replace('\\', '/', $this->targetPropertiesDir);
        @mkdir($this->targetPropertiesDir, '0644', true);
        copy($this->dataDir . 'properties.propertyset1.propertyset.php',
            $this->targetPropertiesDir . 'properties.propertyset1.propertyset.php');
        copy($this->dataDir . 'properties.snippet1.snippet.php',
            $this->targetPropertiesDir . 'properties.snippet1.snippet.php');

        $this->languages = array(
            'en' => array(
                'default',
                'properties',
                'forms',
            ),
        );

        $this->assertNotEmpty($this->targetRoot, 'Empty Root');
        $this->assertNotEmpty($this->targetCore, 'Empty target core');
        $this->assertNotEmpty($this->targetLexDir, 'Empty target lex dir');
        $this->assertNotEmpty($this->targetModelDir, 'Empty Model dir');
        $this->assertNotEmpty($this->targetJsDir, 'Empty JS dir');
        $this->assertNotEmpty($this->targetChunkDir, 'Empty chunk dir');
    }

    protected function tearDown() {
        // $this->utHelpers->rrmdir($this->targetRoot);
    }

    public function testSetup() {
        $lcf = null;
        $lcf = LexiconCodeFileFactory::getInstance($this->modx, $this->mc->helpers,
            $this->targetModelDir, 'example.class.php', $this->targetLexDir, $this->languages);
        $this->assertTrue($lcf instanceof LexiconCodeFile);
        $this->assertEmpty($lcf->hasError());

        /* test with bad file name */
        $lcf = null;
        $lcf = LexiconCodeFileFactory::getInstance($this->modx, $this->mc->helpers,
            $this->targetModelDir, 'xx.class.php', $this->targetLexDir, $this->languages);
        $this->AssertTrue($lcf instanceof LexiconCodeFile);
        $e = $lcf->getErrors();
        $m = implode(', ' . $e);
        $this->assertNotEmpty($lcf->hasError(), $m);
    }

    public function testSetLexFiles() {
        $lcf = LexiconCodeFileFactory::getInstance($this->modx, $this->mc->helpers,
            $this->targetModelDir, 'example.class.php', $this->targetLexDir, $this->languages);
        $language = $lcf->language;
        $this->assertNotEmpty($language);

        $lexFiles = $lcf->getLexFiles();
        $this->assertEmpty($lcf->hasError());
        $expected = array(
            'default.inc.php' => $this->targetLexDir . $language . '/default.inc.php',
        );

            $this->assertNotEmpty($lexFiles);
        $this->assertEquals($expected, $lexFiles);

        $lcf->addLexfile('properties');

        $lexFiles = $lcf->getLexFiles();
        $this->assertEmpty($lcf->hasError());
        $expected = array(
            'default.inc.php' => $this->targetLexDir . $language . '/default.inc.php',
            'properties.inc.php' => $this->targetLexDir . $language . '/properties.inc.php',
        );
        $this->assertNotEmpty($lexFiles);
        $this->assertEquals($expected, $lexFiles);

        /* Test with no lexicon load line */
        $lcf = null;
        $lcf = LexiconCodeFileFactory::getInstance($this->modx, $this->mc->helpers,
            $this->targetChunkDir, 'chunk1.chunk.html', $this->targetLexDir, $this->languages);
        $lexFiles = $lcf->getLexFiles();
        $this->assertEmpty($lcf->hasError());
        $expected = array(
            'chunks.inc.php' => $this->targetLexDir . $language . '/chunks.inc.php',
        );
        $this->assertNotEmpty($lexFiles);
        $this->assertEquals($expected, $lexFiles);

        /* Test with properties file */
        $lcf = null;
        $lcf = LexiconCodeFileFactory::getInstance($this->modx, $this->mc->helpers,
            $this->targetPropertiesDir, 'properties.snippet1.snippet.php', $this->targetLexDir, $this->languages);
        $lexFiles = $lcf->getLexFiles();
        $expected = array(
            'properties.inc.php' => $this->targetLexDir . $language . '/properties.inc.php',
        );
        $this->assertNotEmpty($lexFiles);
        $this->assertEquals($expected, $lexFiles);

        /* Test with menus */
        $lcf = null;
        $lcf = LexiconCodeFileFactory::getInstance($this->modx, $this->mc->helpers,
            $this->targetDataDir, 'transport.menus.php', $this->targetLexDir, $this->languages);
        $lexFiles = $lcf->getLexFiles();
        $expected = array(
            'default.inc.php' => $this->targetLexDir . $language . '/default.inc.php',
        );
        $this->assertNotEmpty($lexFiles);
        $this->assertEquals($expected, $lexFiles);

        /* Test with settings */
        $lcf = null;
        $lcf = LexiconCodeFileFactory::getInstance($this->modx, $this->mc->helpers,
            $this->targetDataDir, 'transport.settings.php', $this->targetLexDir, $this->languages);
        $lexFiles = $lcf->getLexFiles();
        $expected = array(
            'default.inc.php' => $this->targetLexDir . $language . '/default.inc.php',
        );
        $this->assertNotEmpty($lexFiles);
        $this->assertEquals($expected, $lexFiles);

    }

    /** Test getting lex strings from php file, js file, and Tpl chunk */
    public function testSetUsed() {

        $files = array(
            $this->targetModelDir => 'example.class.php',
            $this->targetJsDir => 'example.js',
            $this->targetChunkDir => 'chunk1.chunk.html',
            $this->targetPropertiesDir => 'properties.propertyset1.propertyset.php',
            $this->targetPropertiesDir => 'properties.snippet1.snippet.php',
            $this->targetDataDir => 'transport.menus.php',
            $this->targetDataDir => 'transport.settings.php',
        );
        foreach ($files as $dir => $fileName) {
            $lcf = null;
            $this->assertFileExists($dir . '/' . $fileName);
            $lcf = LexiconCodeFileFactory::getInstance($this->modx, $this->mc->helpers,
                $dir, $fileName, $this->targetLexDir, $this->languages);
            $lexStrings = $lcf->getUsed();
            $this->assertEmpty($lcf->hasError(), $fileName);
            $this->assertTrue(is_array($lexStrings));
            $this->assertNotEmpty($lexStrings, $fileName);
            if ($fileName == 'transport.settings.php') {
                $this->assertTrue(array_key_exists('setting_setting_one', $lexStrings), $fileName);
                $this->assertTrue(array_key_exists('setting_setting_one_desc', $lexStrings), $fileName);
                $this->assertTrue(array_key_exists('setting_setting_two', $lexStrings), $fileName);
                $this->assertTrue(array_key_exists('setting_setting_two_desc', $lexStrings), $fileName);
                $this->assertTrue(array_key_exists('setting_setting_three', $lexStrings), $fileName);
                $this->assertTrue(array_key_exists('setting_setting_three_desc', $lexStrings), $fileName);
                $this->assertEquals("Hello 'columbus'", $lexStrings['setting_setting_one_desc'], $fileName);
                $this->assertEquals('Hello "columbus"', $lexStrings['setting_setting_two_desc'], $fileName);
            } elseif (strpos($fileName, 'properties') !== false) {

            } else {
                $this->assertTrue(array_key_exists('string1', $lexStrings), $fileName);
                $this->assertTrue(array_key_exists('string2', $lexStrings), $fileName);
                $this->assertTrue(array_key_exists('string3', $lexStrings), $fileName);
                $this->assertEquals("Hello 'columbus'", $lexStrings['string1'], $fileName);
                $this->assertEquals('Hello "columbus"', $lexStrings['string2'],  $fileName);
            }
        }
    }

    public function testSetMissing() {

        $files = array(
            1 => $this->targetModelDir . '#' . 'example.class.php',
            2 => $this->targetJsDir . '#' . 'example.js',
            3 => $this->targetChunkDir . '#' . 'chunk1.chunk.html',
            4 => $this->targetPropertiesDir . '#' . 'properties.propertyset1.propertyset.php',
            5 => $this->targetPropertiesDir . '#' . 'properties.snippet1.snippet.php',
            6 => $this->targetDataDir . '#' . 'transport.menus.php',
            7 => $this->targetDataDir . '#' . 'transport.settings.php',
        );
        foreach ($files as $dir => $fileName) {
            $couple = explode('#', $fileName);
            $dir = $couple[0];
            $fileName = $couple[1];
            $lcf = null;
            $this->assertFileExists($dir . '/' . $fileName);
            $lcf = LexiconCodeFileFactory::getInstance($this->modx, $this->mc->helpers,
                $dir, $fileName, $this->targetLexDir, $this->languages);
            $lexStrings = $lcf->getUsed();
            $missing = $lcf->getMissing();
            $toUpdate = $lcf->getToUpdate();
            $this->assertEmpty($lcf->hasError());

            $this->assertTrue(is_array($lexStrings));
            $this->assertNotEmpty($lexStrings, $fileName);
            $this->assertTrue(is_array($missing));
            $this->assertNotEmpty($missing);
            $this->assertTrue(is_array($toUpdate));

            if ($fileName == 'example.class.php') {
                $this->assertNotEmpty($toUpdate);
            }

            if ($fileName == 'transport.settings.php') {
                $this->assertTrue(array_key_exists('setting_setting_one', $lexStrings), $fileName);
                $this->assertTrue(array_key_exists('setting_setting_one_desc', $lexStrings), $fileName);
                $this->assertTrue(array_key_exists('setting_setting_two', $lexStrings), $fileName);
                $this->assertTrue(array_key_exists('setting_setting_two_desc', $lexStrings), $fileName);
                $this->assertTrue(array_key_exists('setting_setting_three', $lexStrings), $fileName);
                $this->assertTrue(array_key_exists('setting_setting_three_desc', $lexStrings), $fileName);
                $this->assertEquals("Hello 'columbus'", $lexStrings['setting_setting_one_desc'], $fileName);
                $this->assertEquals('Hello "columbus"', $lexStrings['setting_setting_two_desc'], $fileName);
            } elseif ( strpos($fileName, 'properties') !== false) {
            } else {
                $this->assertTrue(array_key_exists('string1', $missing), $fileName);
                $this->assertTrue(array_key_exists('string3', $missing), $fileName);
                $this->assertEquals("Hello 'columbus'", $lexStrings['string1'], $fileName);
                $this->assertEquals('Hello "columbus"', $lexStrings['string2'], $fileName);
            }

            $expected = array(
                'string2' => 'Hello "columbus"',
                'string4' => 'Hello \\\'columbus\\\'',
            );
            if ($fileName == 'example.class.php') {
                $this->assertEquals($expected, $toUpdate, $fileName);
            }
        }
    }

    public function testUpdateLexiconFile() {
        $_lang = array();
        $expected = array(
            'string2' => 'String2 in Lexicon file',
            'string4' => 'String4 in Lexicon file',
            'unused' => 'Unused Lexicon String',
            'empty_string' => '',
        );

        $files = array(
            1 => $this->targetModelDir . '#' . 'example.class.php',
            2 => $this->targetJsDir . '#' . 'example.js',
            3 => $this->targetChunkDir . '#' . 'chunk1.chunk.html',
            4 => $this->targetPropertiesDir . '#' . 'properties.propertyset1.propertyset.php',
            5 => $this->targetPropertiesDir . '#' . 'properties.snippet1.snippet.php',
            6 => $this->targetDataDir . '#' . 'transport.menus.php',
            7 => $this->targetDataDir . '#' . 'transport.settings.php',
        );
        foreach ($files as $s => $fileName) {
            $couple = explode('#', $fileName);
            $dir = $couple[0];
            $fileName = $couple[1];
            $dir = rtrim($dir, '/');
            $lcf = null;
            $this->assertFileExists($dir . '/' . $fileName);
            $lcf = LexiconCodeFileFactory::getInstance($this->modx, $this->mc->helpers,
                $dir, $fileName, $this->targetLexDir, $this->languages);
            $lcf->updateLexiconFile();
            $this->assertEmpty($lcf->hasError(), print_r($lcf->getErrors(), true));
            $path = reset($lcf->getLexFiles());
            $_lang = array();
            include $path;
            $this->assertNotEmpty($_lang);
            switch ($fileName) {
                case 'example.class.php':
                    $this->assertContains('default.inc.php', $path);
                    $this->assertEquals('Hello "columbus"', $_lang['string2']);
                    $this->assertEquals('Hello \'columbus\'', $_lang['string4']);
                    $this->assertEquals('Unused Lexicon String', $_lang['unused']);
                    $this->assertEquals('', $_lang['empty_string']);
                    $this->assertEquals('Hello \'columbus\'', $_lang['string1']);
                    $this->assertEquals('', $_lang['string3']);
                    break;
                case 'example.js':
                    $this->assertContains('default.inc.php', $path);
                    $this->assertEquals('Hello \'columbus\'', $_lang['string1']);
                    $this->assertEquals('Hello "columbus"', $_lang['string2']);
                    break;
                case 'chunk1.chunk.html':
                    $this->assertContains('chunks.inc.php', $path);
                    $this->assertEquals('Updated String', $_lang['string4']);
                    $this->assertEquals('Updated Empty string', $_lang['string3']);
                    break;
                case 'properties.propertyset1.propertyset.php':
                    $this->assertContains('properties.inc.php', $path);
                   // $this->assertEquals('This is description 3', $_lang['string3']);
                    break;

                case 'properties.snippet1.snippet.php':
                    $this->assertContains('properties.inc.php', $path);
                    // $this->assertEquals('Hello \'columbus\'', $_lang['string1'], $fileName);
                    // $this->assertEquals('Hello "columbus"', $_lang['string2'], $fileName);
                    // $this->assertEquals('This is the newest description5', $_lang['Descriptionx']);
                    // $this->assertEquals('This is the even newer description', $_lang['new_description']);
                    // $this->assertArrayHasKey('Description8', $_lang);
                    // $this->assertEquals('', $_lang['Description8']);
                    break;

                case 'transport.menus.php':
                    $this->assertContains('default.inc.php', $path);
                    $this->assertEquals('Hello \'columbus\'', $_lang['string1'], $fileName);
                    $this->assertEquals('Hello "columbus"', $_lang['string2'], $fileName);
                    break;

                case 'transport.settings.php':
                    $this->assertContains('default.inc.php', $path);
                    $this->assertEquals('Hello \'columbus\'', $_lang['string1'], $fileName);
                    $this->assertEquals('Hello "columbus"', $_lang['string2'], $fileName);
                    break;

                default:
                    $this->assertTrue(false);
            }
        }
    }

    public function testUpdateCodeFile() {
        $files = array(
            $this->targetModelDir => 'example.class.php',
            $this->targetJsDir => 'example.js',
            $this->targetChunkDir => 'chunk1.chunk.html',
            $this->targetPropertiesDir => 'properties.propertyset1.propertyset.php',
            $this->targetPropertiesDir => 'properties.snippet1.snippet.php',
            $this->targetDataDir => 'transport.menus.php',
        );
        foreach ($files as $dir => $fileName) {
            $lcf = null;
            $this->assertFileExists($dir . '/' . $fileName);
            $lcf = LexiconCodeFileFactory::getInstance($this->modx, $this->mc->helpers,
                $dir, $fileName, $this->targetLexDir, $this->languages);
            $updated = $lcf->updateCodeFile();
            $this->assertEmpty($lcf->hasError());
            $content = file_get_contents($dir .'/' . $fileName);
            $this->assertNotEmpty($content, 'File content is empty');

            if ($fileName == 'example.class.php') {
                $this->assertTrue(strpos($content, '~~') === false, '~~ found', $fileName);
                $this->assertContains('$x = $this->modx->lexicon("string1")', $content);
                $this->assertContains('$y = $this->modx->lexicon(\'string2\')', $content);
                $this->assertContains('$z = $this->modx->lexicon(\'string3\')', $content);
            }
            if ($fileName == 'chunk1.chunk.html') {
                $this->assertTrue(strpos($content, '~~') === false, '~~ found', $fileName);
                $this->assertContains('[[%string1]]', $content);
                $this->assertContains('[[%string2]]', $content);
                $this->assertContains('[[%string3]]', $content);
            }
            if ($fileName == 'example.js') {
                $this->assertTrue(strpos($content, '~~') === false, '~~ found', $fileName);
                $this->assertContains('x = _("string1")', $content);
                $this->assertContains('y = _(\'string2\')', $content);
                $this->assertContains('z = _(\'string3\')', $content);
            }
            if ($fileName == 'properties.propertyset1.propertyset.php') {
                $this->assertTrue(strpos($content, '~~') === false, '~~ found', $fileName);
                $this->assertContains("'desc' => \"string1\"", $content);
                $this->assertContains("'desc' => 'string2'", $content);
                $this->assertContains("'desc' => 'string3'", $content);
            }

            if ($fileName == 'transport.menus.php') {
                $this->assertTrue(strpos($content, '~~') === false, '~~ found', $fileName);
                $this->assertContains("'description' => \"string1\"", $content);
                $this->assertContains("'description' => 'string2'", $content);
                $this->assertContains("'description' => 'string3'", $content);
            }

            if ($fileName == 'transport.settings.php') {
                $this->assertTrue(strpos($content, '~~') === false, '~~ found', $fileName);
                $this->assertContains("'key' => \"string1\"", $content);
                $this->assertContains("'key' => 'string2'", $content);
                $this->assertContains("'key' => 'string3'", $content);
            }
        }
    }

    public function xtestEverything() {
        $this->utHelpers->rrmdir($this->targetLexDir);
        $lexHelper = new LexiconHelper($this->modx);
        $lexHelper->init(array(), 'unittest');
        $lexHelper->run();
        $this->assertNotEmpty($lexHelper->props, 'LexHelper->props is empty');
        $this->assertEquals('unittest', $lexHelper->packageNameLower);
        $this->assertEquals('en', $lexHelper->primaryLanguage);
        $this->assertEquals($this->targetLexDir, $lexHelper->targetLexDir);
        $this->assertEquals($this->targetDataDir, $lexHelper->targetData);

        $file = $this->targetLexDir . 'en/' . 'chunks.inc.php';
        $this->assertFileExists($file);
        $content = file_get_contents($file);

        $this->assertContains('$_lang[\'string1\'] = \'Hello \\\'columbus\\\'', $content);
        $this->assertContains("\$_lang['string2'] = 'Hello \"columbus\"'", $content);
        $this->assertContains("\$_lang['string3'] = 'Updated Empty string'", $content);
        $this->assertContains("\$_lang['string4'] = 'Updated String'", $content);
        $this->assertContains("\$_lang['string14'] = 'String in Chunk'", $content);
        $this->assertContains("\$_lang['string15'] = 'Hello \"Columbus\"'", $content);
    }
}
