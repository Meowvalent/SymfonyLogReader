<?php

namespace Parasys\LogReaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;
use Parasys\LogReaderBundle\Request\Tail;

class LogReaderController extends Controller
{
    public function loginAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'ParasysLogReaderBundle:LogReader:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            )
        );
    }
    public function logAction() {
        $logs = array();
        
        $finder = new Finder();
        
        // Get current directory and remove everything past the Symphony directory
        $directory = substr(getcwd(), 0, (strpos(getcwd(), "Symfony") + strlen("Symfony")+1)) . "app/logs";
        
        $finder->files()->in($directory);
        
        foreach($finder as $file) {
            $logs[$file->getFilename()] = $file->getRealpath();
        }

        return $this->render('ParasysLogReaderBundle:LogReader:log.html.twig', array('logs' => $logs));
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
