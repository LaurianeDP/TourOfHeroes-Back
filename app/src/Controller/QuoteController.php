<?php

namespace App\Controller;

use App\Repository\QuoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuoteController extends AbstractController
{
    #[Route('/quotes', name: 'index', methods: [Request::METHOD_GET])]
    public function index(
        QuoteRepository $quoteRepository
    )
    : Response
    {
        return $this->render(
            'quote/index.html.twig',
            [
            'quotes' => $quoteRepository->findAll(),
            ]
        );
    }
}
