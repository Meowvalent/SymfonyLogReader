<?php

namespace Parasys\LogReaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;
use Parasys\LogReaderBundle\Request\Tail;

class DefaultController extends Controller
{
    public function logAction() {
        $logs = array();
        
        $finder = new Finder();
        
        // Get current directory and remove everything past the Symphony directory
        $directory = substr(getcwd(), 0, (strpos(getcwd(), "Symfony") + strlen("Symfony")+1)) . "app/logs";
        
        $finder->files()->in($directory);
        
        foreach($finder as $file) {
            $logs[$file->getFilename()] = $file->getRealpath();
        }
        
        return $this->render('ParasysLogReaderBundle:Default:index.html.twig', array('logs' => $logs));
    }

    public function tailAction() {
        if ($this->get('request')->isXmlHttpRequest()) {
            $get = $this->get('request')->query;
            $file = $get->get('file');
            $size = $get->get('lastsize');

            // create the tail request
            $tail = new Tail($file);

            if($size == 0) {
                $size = filesize($file);
            }

            // call getNewLines
            return new Response($tail->getNewLines($size, $get->get('grep'), $get->get('invert')));
        }
    }
}
