<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function homepage()
    {

        return $this->render('home_page.html.twig');
        // return new Response('<strong>SNOW_TRICKS</strong>: Bienvenue à ce site coopératif sur les plus belles figures de snow!');

    }
}
