<?php
namespace AppBundle\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * ThemeFilesystemLoader extends the default Twig filesystem loader
 * to work with Teapotio theming system.
 */
class FilesystemLoader extends \Twig_Loader_Filesystem
{
    protected $locator;
    protected $parser;

    /**
     * Constructor.
     *
     * @param FileLocatorInterface $locator A FileLocatorInterface instance
     * @param TemplateNameParserInterface $parser A TemplateNameParserInterface instance
     */
    public function __construct(FileLocatorInterface $locator, TemplateNameParserInterface $parser)
    {
        $this->locator = $locator;
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     *
     * The name parameter might also be a TemplateReferenceInterface.
     */
    public function exists($name)
    {
        // same logic as findTemplate below for the fallback
        try {
            $this->cache[(string)$name] = $this->locator->locate($this->parser->parse($name));
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Returns the path to the template file.
     *
     * The file locator is used to locate the template when the naming convention
     * is the symfony one (i.e. the name can be parsed).
     * Otherwise the template is located using the locator from the twig library.
     *
     * @param string|TemplateReferenceInterface $template The template
     *
     * @return string The path to the template file
     *
     * @throws \Twig_Error_Loader if the template could not be found
     */
    protected function findTemplate($template)
    {
        $logicalName = (string)$template;
        if (isset($this->cache[$logicalName])) {
            return $this->cache[$logicalName];
        }
        $file = false;
        // for BC
        try {
            $template = $this->parser->parse($template);
            try {
                $file = $this->locator->locate($template);
            } catch (\InvalidArgumentException $e) {
                $previous = $e;
            }
        } catch (\Exception $e) {
            $previous = $e;
        }
        if (false === $file || null === $file) {
            throw new \Twig_Error_Loader(sprintf('Unable to find template "%s".', $logicalName), -1, null, $previous);
        }
        return $this->cache[$logicalName] = $file;
    }
}