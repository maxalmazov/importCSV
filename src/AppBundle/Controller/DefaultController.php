<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\BrowserKit\Response;

class IndexController extends Controller
{
    /**
     *  @Route ("/", name="index")
     */
    public function indexAction ($a, $b)
    {
        $c = $a+$b;
        return new Response($c);
    }
}