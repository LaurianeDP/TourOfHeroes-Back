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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HeroController extends AbstractController
{
    public function __construct(
        protected HeroRepository         $heroRepository,
        protected PowerRepository        $powerRepository,
        protected SerializerInterface    $serializer,
        protected EntityManagerInterface $entityManager,
        protected UrlGeneratorInterface  $urlGenerator,
        protected ValidatorInterface     $validator,
        protected TokenStorageInterface $tokenStorageInt,
        protected JWTTokenManagerInterface $JWTTokenManager,
    )
    {
    }

    //Show all heroes in Json, with pagination
    #[Route('/api/heroes', name: 'heroes', methods: ['GET'])]
    //Gets parameters from url, ex: "/api/books?page=3&limit=2" will show page 3 of pagination and display only two results
    public function getAllHeroes(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 15);
        $heroesList = $this->heroRepository->findAllPagination($page, $limit);

        $jsonHeroesList = $this->serializer->serialize($heroesList, 'json', context: ['groups' => ['get']]);

        return new JsonResponse($jsonHeroesList, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    //Show all heroes in Json
    #[Route('/api/heroesAll', name: 'heroesAll', methods: ['GET'])]
    //Gets parameters from url, ex: "/api/books?page=3&limit=2" will show page 3 of pagination and display only two results
    public function getFullHeroesList(Request $request): JsonResponse
    {
        $heroesList = $this->heroRepository->findAll();
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

    //Create a Hero, linked to the sing-up form in front-end interface
    #[Route('/api/hero', name: 'createHero', methods: ['POST'])]
    public function createHero(Request $request): JsonResponse
    {
        $hero = $this->serializer->deserialize($request->getContent(), Hero::class, 'json');

//        dump($this->powerRepository->find(58)); //TEST
//        dump($request->getContent()); //TEST

        $content = $request->toArray();
//        dump($content);
        if (array_key_exists('power', $content)) {
            $idPower = $content['power']['id'];
//            dump($idPower);
            $hero->setPower($this->powerRepository->find($idPower));
        }

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

    //Searches for a hero name corresponding to the params sent
    #[Route('/api/heroes_search', name: 'searchHero', methods: ['GET'])]
    public function searchHero(Request $request): JsonResponse
    {
        $term = $request->get('name');
        $foundHeroes = $this->heroRepository->findWithSearchTerms($term);

        if (!empty($foundHeroes)) {
            $jsonFoundHeroes = $this->serializer->serialize($foundHeroes, 'json', context: ['groups' => ['get']]);
            $searchResult = $jsonFoundHeroes;
        } else {
            $searchResult = "No heroes found matching that name";
//            $searchResult = json_encode([$searchResult]);
        }
        return new JsonResponse($searchResult, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    //HTML display route
    #[Route('/hero', name: 'app_hero')]
    public function index(): Response
    {
        return $this->render('hero/index.html.twig', [
            'controller_name' => 'HeroController',
        ]);
    }

    //*** Routes restricted by JWT token ***//

    //Delete a Hero, to be restricted in the front-end interface
    #[Route('/api/heroes/{id}', name: 'deleteHero', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'You do not have the necessary rights to delete a hero')]
    public function deleteHero(Request $request, Hero $hero): JsonResponse
    {

        $this->entityManager->remove($hero);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    //Update a Hero, to be resticted in the front-end interface
    #[Route('/api/heroes/{id}', name: 'updateHero', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'You do not have the necessary rights to
// update a hero')]
    public function updateHero(Request $request, Hero $currentHero): JsonResponse
    {
//        $decodedToken = $this->JWTTokenManager->decode($this->tokenStorageInt->getToken());//TEST
        $updatedHero = $this->serializer->deserialize($request->getContent(),
            Hero::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE =>
                $currentHero]);

        $content = $request->toArray();
        if (array_key_exists('power', $content)) {
            $idPower = $content['power']['id'] ?? -1;
            $updatedHero->setPower($this->powerRepository->find($idPower));
        }
        $this->entityManager->persist($updatedHero);
        $this->entityManager->flush();

        $jsonHero = $this->serializer->serialize($updatedHero, 'json', context: ['groups' => ['get']]);
        return new JsonResponse($jsonHero, Response::HTTP_OK, ['accept' => 'json'], true);
    }


}
