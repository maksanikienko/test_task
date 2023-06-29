<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UrlMappingRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Url;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UrlMapping;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class UrlController extends AbstractController
{
    #[Route('/user/shorten', name: 'app_shorten', methods: ['POST'])]
    public function shorten(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $longUrl = $request->request->get('longUrl');


        $validator = Validation::createValidator();
        $errors = $validator->validate($longUrl, new Url());

        if (count($errors) > 0) {
            return new Response('Невалидный URL', Response::HTTP_BAD_REQUEST);
        }

        $urlMapping = $entityManager->getRepository(UrlMapping::class)->findOneBy(['longUrl' => $longUrl]);

        if ($urlMapping) {

            $shortUrl = $this->generateShortUrl($urlMapping->getShortCode());
        } else {

            $shortCode = $this->generateShortCode();
            $token = $tokenStorage->getToken();
            $currentUser = $token->getUser();
            $urlMapping = new UrlMapping();
            $urlMapping->setLongUrl($longUrl);
            $urlMapping->setShortCode($shortCode);
            $urlMapping->setClient($currentUser);

            $entityManager->persist($urlMapping);
            $entityManager->flush();

            $shortUrl = $this->generateShortUrl($shortCode);
        }

        return new JsonResponse([
            'shortUrl' => $shortUrl,
        ]);
    }

    #[Route('/success', name: 'app_success', methods: ['GET'])]
    public function success(Request $reguest): Response
    {
        return $this->render('index/shortUrl.html.twig', [
            'shortUrl' => $reguest->get('link'),
        ]);
    }

    #[Route('/go-{shortCode}', name: 'app_redirect', methods: ['GET'])]
    public function redirectLink(Request $request, string $shortCode, UrlMappingRepository $urlMappingRepository): RedirectResponse|Response
    {
        $shortCode = $request->get('shortCode');

        $urlMapping = $urlMappingRepository->findOneBy(['shortCode' => $shortCode]);
        if ($urlMapping) {
            $clicks = $urlMapping->getClickCount() ?? 0;
            $urlMapping->setClickCount($clicks + 1);

            $urlMappingRepository->save($urlMapping, true);

            return $this->redirect($urlMapping->getLongUrl());
        }


        return new Response('Страница не найдена', Response::HTTP_NOT_FOUND);
    }

    private function generateShortCode($length = 10): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $shortCode = '';

        $max = strlen($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $shortCode .= $characters[random_int(0, $max)];
        }

        return $shortCode;
    }

    private function generateShortUrl(string $shortCode): string
    {
        return $this->generateUrl('app_redirect', ['shortCode' => $shortCode], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    //выводит все ссылки для admin пользователя
    #[Route('/admin/url', name: 'url_show', methods: ['GET'])]
    public function getAllUrl(UrlMappingRepository $urlMappingRepository): Response
    {
        return $this->render('admin/urlMapping/index.html.twig', [
            'allUrl' => $urlMappingRepository->findAll(),
        ]);
    }

    //выводит ссылки для user пользователя

    #[Route('/api/current-user/links', name: 'url_show_user', methods: ['GET'])]
    public function userUrl(): JsonResponse
    {
        // Получаем текущего пользователя
        /** @var User $user */
        $user = $this->getUser();
        $links = $user->getUrlMapping();

        return $this->json(['links' => $links,], context: ['groups' => ['api']]);
    }


    #[Route('/user/links', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('index/currentUserUrl.html.twig');
    }
}
