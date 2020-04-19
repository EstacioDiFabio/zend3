<?php
namespace Base\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Base\View\Helper\InputForm;

class InputFormFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        return new InputForm($entityManager);
    }
}
