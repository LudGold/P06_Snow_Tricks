<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{ 
    #[Route('/')]
    public function homepage()
    {
        return new Response('<strong>SNOW_TRICKS</strong>: Bienvenue à ce site coopératif sur les plus belles figures de snow!');

    }

}
