<?php

namespace Parasys\LogReaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ParasysLogReaderBundle:Default:index.html.twig');
    }
}
