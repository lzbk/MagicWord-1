<?php

namespace  MagicWordBundle\Twig;

class UserExtension extends \Twig_Extension
{
    protected $em;
    protected $userManager;

    public function __construct($entityManager, $userManager)
    {
        $this->em = $entityManager;
        $this->userManager = $userManager;
    }

    public function getConnected($threshold)
    {
        $connected = $this->userManager->getConnected($threshold);

        return $connected;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_connected', array($this, 'getConnected')),
        );
    }

    public function getName()
    {
        return 'get_connected';
    }
}
