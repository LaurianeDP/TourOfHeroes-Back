<?php

namespace App\Controller;

use App\Entity\Hero;
use App\Helpers\ValidatorParser;
use App\Repository\HeroRepository;
use App\Repository\PowerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HeroController extends AbstractController
{
    public function __construct(
        protected HeroRepository         $heroRepository,
        protected PowerRepository       $powerRepository,
        protected SerializerInterface    $serializer,
        protected EntityManagerInterface $entityManager,
        protected UrlGeneratorInterface  $urlGenerator,
        protected ValidatorInterface     $validator,
    )
    {}

    //Show all heroes in Json, with pagination
    #[Route('/api/heroes', name: 'heroes', methods: ['GET'])]
    //Gets parameters from url, ex: "/api/books?page=3&limit=2" will show page 3 of pagination and display only two results
    public function getAllHeroes(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit= $request->get('limit', 10);
        $heroesList = $this->heroRepository->findAllPagination($page, $limit);

        $jsonHeroesList = $this->serializer->serialize($heroesList, 'json', context: ['groups' => ['get']]);

        return new JsonResponse($jsonHeroesList, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    //Show one hero in Json
    #[Route('/api/heroes/{id}', name: 'heroDetail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getHeroDetail(Hero $hero, SerializerInterface $serializer): JsonResponse
    {
        $jsonHero = $serializer->serialize($hero, 'json', context: ['groups' => ['get']]);
        return new JsonResponse($jsonHero, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    //Create a Hero, to be linked to the sing-up form in front-end interface
    #[Route('/api/hero', name: 'createHero', methods: ['POST'])]
    public function createHero(Request $request): JsonResponse
    {
        $hero = $this->serializer->deserialize($request->getContent(), Hero::class, 'json');

//        dump($this->powerRepository->find(58)); //TEST

        //All of the request, in the form of an array
        $content = $request->toArray();

        //If idPower is not in the request, sets its value at -1
        $idPower = $content['idPower'] ?? -1;

        //If the power is found, set it in the hero's property, if not found,
        // automatically null
        $hero->setPower($this->powerRepository->find($idPower));

        //Checks for errors when receiving the body of the request
        $errors = $this->validator->validate($hero);
        $errors = ValidatorParser::handleViolationList($errors);

        if (!empty($errors)) {
//            dump($errors->get(1)); //TEST
            return new JsonResponse($this->serializer->serialize($errors, 'json'),
                JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $this->entityManager->persist($hero);
        $this->entityManager->flush();

        $jsonHero = $this->serializer->serialize($hero, 'json', context: ['groups' => ['get']]);

        $location = $this->urlGenerator->generate('heroDetail', ['id' => $hero->getId
        ()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonHero, Response::HTTP_CREATED, ['location' => $location],
            true);
    }

    //Delete a Hero, to be restricted in the front-end interface
    #[Route('/api/heroes/{id}', name: 'deleteHero', methods: ['DELETE'])]
//    #[IsGranted('ROLE_ADMIN', message: 'You do not have the necessary rights to
// delete a hero')]
    public function deleteHero(Hero $hero): JsonResponse
    {
        $this->entityManager->remove($hero);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    //Update a Hero, to be resticted in the front-end interface
    #[Route('/api/heroes/{id}', name: 'updateHero', methods: ['PUT'])]
//    #[IsGranted('ROLE_ADMIN', message: 'You do not have the necessary rights to
// update a hero')]
    public function updateHero(Request $request, Hero $currentHero): JsonResponse
    {
        $updatedHero = $this->serializer->deserialize($request->getContent(),
            Hero::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE =>
                $currentHero]);

        $content = $request->toArray();
        if(array_key_exists('power', $content)) {
            $idPower = $content['power']['id'] ?? -1;
            $updatedHero->setPower($this->powerRepository->find($idPower));
        }
        $this->entityManager->persist($updatedHero);
        $this->entityManager->flush();

        $jsonHero = $this->serializer->serialize($updatedHero, 'json', context: ['groups' => ['get']]);
        return new JsonResponse($jsonHero, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    //HTML display route
    #[Route('/hero', name: 'app_hero')]
    public function index(): Response
    {
        return $this->render('hero/index.html.twig', [
            'controller_name' => 'HeroController',
        ]);
    }
}
