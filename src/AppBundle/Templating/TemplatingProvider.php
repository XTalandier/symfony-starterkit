<?php
namespace AppBundle\Templating;


class TemplatingProvider
{
    private $fallback;
    private $platform;

    public function __construct($templating, $request, $fallback)
    {
        die("ici");
    }

    private function setPlatform()
    {
    }

    private function getPlatform()
    {
        if (null === $this->platform) {
            $this->setPlatform();
        }

        return $this->platform;
    }

    private function getTemplateName($name, $platform)
    {
        die('-- ' . $name);
        if ($platform === 'html') {
            return $name;
        }

        $template = explode('.', $name);

        $template = array_merge(
            array_slice($template, 0, -2),
            array($platform),
            array_slice($template, -2)
        );

        return implode('.', $template);
    }

    public function renderResponse($name, array $parameters = array())
    {
        die('renderResponse');
        $newname = $this->getTemplateName($name, $this->getPlatform());

        if ($this->templating->exists($newname)) {
            return $this->templating->render($newname);
        }

        return $this->templating->renderResponse($this->getTemplateName(
            $name, $this->fallback));
    }
}