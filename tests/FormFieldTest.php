<?php
/**
 * Created by PhpStorm.
 * User: angel
 * Date: 06/02/2018
 * Time: 12:36
 */

namespace fw;

use PHPUnit\Framework\TestCase;

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
include '../Loader.php';


class FormFieldTest extends TestCase
{




    public function testSetPlaceholder()
    {
        /*$stub = $this->createMock(FormFieldFile::class);
        $stub->setPlaceholder('testplace');

        $this->assertContains('<foo><bar/></foo>', $stub->__toString());*/
    }



}
