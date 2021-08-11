<?php declare(strict_types=1);

namespace Symfona\Bundle\JsonRequestBundle\DependencyInjection;

use Symfona\Bundle\JsonRequestBundle\ArgumentResolver\DataTransferObjectArgumentResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class JsonRequestExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $argumentResolver = new Definition(DataTransferObjectArgumentResolver::class);
        $argumentResolver->setAutowired(true);
        $argumentResolver->addTag('controller.argument_value_resolver');

        $container->setDefinition(DataTransferObjectArgumentResolver::class, $argumentResolver);
    }
}
