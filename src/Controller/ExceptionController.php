<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ExceptionController extends AbstractController
{
//     #[Route('/error/403', name: 'access_denied')]
//     public function showAccessDenied(): Response
//     {
//         return $this->render('error/error403.html.twig', [], new Response('', 403));
//     }

//     #[Route('/error/404', name: 'not_found')]
//     public function showNotFound(): Response
//     {
//         return new Response($this->renderView('error/error404.html.twig'), Response::HTTP_NOT_FOUND);
//     }

//     #[Route('/error', name: 'error')]
//     public function showError(): Response
//     {
//         return $this->render('error/error500.html.twig', [], new Response('', Response::HTTP_INTERNAL_SERVER_ERROR));
//     }

//     public function onAccessDenied(AccessDeniedException $exception): Response
//     {
//         return $this->redirectToRoute('access_denied');
//     }
}
