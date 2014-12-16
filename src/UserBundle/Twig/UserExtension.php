<?php

namespace UserBundle\Twig;

use Symfony\Component\Security\Core\SecurityContext;

class UserExtension extends \Twig_Extension
{

    private $context;

    public function __construct(SecurityContext $context)
    {
        $this->context = $context;
    }


    private function getUser()
    {
        return $this->context->getToken()->getUser();
    }


    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('theUser', array($this, 'theUserFunction')),
        );
    }

    public function theUserFunction()
    {
        return $this->context->getToken()->getUser();
    }

    public function getName()
    {
        return 'user_extension';
    }
}

