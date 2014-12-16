<?php
namespace AppBundle\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator as BaseTemplateLocator;

/**
 * TemplateLocator locates templates in themes.
 */
class TemplateLocator extends BaseTemplateLocator implements FileLocatorInterface
{
    protected $rootDir;

    /**
     * {@inherit}
     *
     * @param string $rootDir the root directory
     */
    public function __construct(FileLocatorInterface $locator, $cacheDir = null, $rootDir = '')
    {
        parent::__construct($locator, $cacheDir);
        $this->rootDir = $rootDir;
    }

    /**
     * {@inherit}
     *
     * @throws \InvalidArgumentException When the template is not an instance of TemplateReferenceInterface
     * @throws \InvalidArgumentException When the template file can not be found
     */
    public function locate($template, $currentPath = null, $first = true)
    {
        if (!$template instanceof TemplateReferenceInterface) {
            throw new \InvalidArgumentException('The template must be an instance of TemplateReferenceInterface.');
        }
        $key = $this->getCacheKey($template);
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }
        $path = $this->rootDir . '/Resources/';
        $path .= $template->get('bundle') . '/';
        $path .= $template->get('controller') . '/';
        $path .= $template->get('name');
        //$path .= '.' . $template->get('format') . '.' . $template->get('engine');

        $isMob = false;
        $isTab = false;

        if ($isMob) {
            $mobPath = $path . '.mob.' . $template->get('format') . '.' . $template->get('engine');
            if (file_exists($mobPath) === false) {
                $path .= '.' . $template->get('format') . '.' . $template->get('engine');
            } else {
                $path = $mobPath;
            }
        } elseif ($isTab) {
            $tabPath = $path . '.tab.' . $template->get('format') . '.' . $template->get('engine');
            if (file_exists($tabPath) === false) {
                $path .= '.' . $template->get('format') . '.' . $template->get('engine');
            } else {
                $path = $tabPath;
            }
        } else {
            $path .= '.' . $template->get('format') . '.' . $template->get('engine');
        }

        //echo ($path)."(".$template->get('format').")"."<br />";
        if (file_exists($path) === false) {
            throw new \InvalidArgumentException(sprintf('Unable to find template "%s".', $template));
        }
        return $this->cache[$key] = $path;
    }
}