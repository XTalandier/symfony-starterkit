<?php
namespace AppBundle\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\LogicException;

/**
 * Reorder the twig loader chain to prioritize Teapotio TwigLoader
 * over Symfony's TwigLoader
 */
class TwigLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('twig')) {
            return;
        }
        // register additional template loaders
        $loaderIds = $container->findTaggedServiceIds('twig.loader');
        // if there are one or less TwigLoader we don't need to go further
        if (count($loaderIds) <= 1) {
            return;
        }
        $teapotioLoaderId = 'app.loader.filesystem';
        $symfonyLoaderId = 'twig.loader.filesystem';
        $chainLoader = $container->getDefinition('twig.loader.chain');
        // Keep the original list of the method calls
        $methodCalls = $chainLoader->getMethodCalls();
        // Reset the chain
        $chainLoader->setMethodCalls(array());
        // We want to swap Symfony's twig loader position with Teapotio's
        foreach (array_keys($loaderIds) as $id) {
            // We are manually adding teapotio loader so we can skip this loop
            // when $id is equal to Teapotio Loader Id.
            if ($id === $teapotioLoaderId) {
                continue;
            }
            // Inject Teapotio loader right before Symfony's
            if ($id === $symfonyLoaderId) {
                $chainLoader->addMethodCall('addLoader', array(new Reference($teapotioLoaderId)));
            }
            $chainLoader->addMethodCall('addLoader', array(new Reference($id)));
        }
        $container->setAlias('twig.loader', 'twig.loader.chain');
    }
}