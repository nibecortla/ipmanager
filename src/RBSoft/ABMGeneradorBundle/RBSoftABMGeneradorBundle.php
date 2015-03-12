<?php

namespace RBSoft\ABMGeneradorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Symfony\Component\Console\Application;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\Filesystem\FileSystem;

class RBSoftABMGeneradorBundle extends Bundle
{

//    public function registerCommands(Application $application){
//        $crudCommand = $application->get('generate:doctrine:crud');
//        $generator = new DoctrineCrudGenerator(new FileSystem, __DIR__.'/Resources/skeleton/crud');
//        $crudCommand->setGenerator($generator);
//        echo "-Por aca<br>";
//        parent::registerCommands($application);
//    }
//    public function getParent()
//    {
//        return 'SensioGeneratorBundle';
//    }
}
