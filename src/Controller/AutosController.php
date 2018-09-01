<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AutosController extends AbstractController
{
    /**
     * @Route("/autos", name="autos")
     */
    public function index()
    {
        return $this->render('autos/index.html.twig', [
            'controller_name' => 'AutosController',
        ]);
    }
}
