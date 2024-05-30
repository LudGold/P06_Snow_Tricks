<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\Figure;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentController extends AbstractController
{
    private $commentRepository;
    private $figureRepository;
  

    public function __construct(CommentRepository $commentRepository, FigureRepository $figureRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->figureRepository = $figureRepository;
       
    }
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        $figures = $this->figureRepository->findAll();
        return $this->render('comment/index.html.twig', [
            'figures' => $figures,
        ]);
    }
    #[Route('/comment/new/{figure_slug}', name: 'comment_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, $figure_slug, FigureRepository $figureRepository): Response
    {
        $figure = $figureRepository->findOneBySlug($figure_slug);
        dd($request);
        $comment = $request->request->get('comment');
      
        if ($request->isMethod('POST')) {
           
            $this->commentRepository->save($comment, true);

            return $this->redirectToRoute('figure_show', ['slug' => $figure->getSlug()]);
        }
            }
}
