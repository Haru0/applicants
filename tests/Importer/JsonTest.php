<?php

namespace Tests\Importer;

use Applicants\Importer\Json;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

/**
 * Json class.
 *
 * @package Tests\Importer
 */
class JsonTest extends TestCase
{

    /**
     * @var vfsStreamDirectory
     */
    protected $root;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->root = vfsStream::setup('root', null, array(
            'dummy.json' => '{}',
        ));
    }


    /**
     * Test proper import.
     */
    public function testImport()
    {
        $importer = new Json();
        $this->assertSame(
            [],
            $importer->import($this->root->url() . '/dummy.json')
        );
    }

    /**
     * Test invalid path.
     *
     * @expectedException \Exception
     */
    public function testImportException()
    {
        $importer = new Json();
        $importer->import($this->root->url() . '/invalid.json');
    }

}
