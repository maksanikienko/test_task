<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UrlMappingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Url;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UrlMapping;

class UrlShortenerController extends AbstractController
{
    #[Route('/shorten', name: 'app_shorten', methods: [ 'POST'])]
    public function shorten(Request $request, EntityManagerInterface $entityManager)
    {
        // Получение длинного URL из тела запроса
        $longUrl = $request->request->get('long_url');

        // Проверка валидности длинного URL
        $validator = Validation::createValidator();
        $errors = $validator->validate($longUrl, new Url());

        if (count($errors) > 0) {
            // Если длинный URL невалиден, возвращаем ошибку
            return new Response('Невалидный URL', Response::HTTP_BAD_REQUEST);
        }

        // Проверяем, существует ли уже запись для данного длинного URL
        $urlMapping = $entityManager->getRepository(UrlMapping::class)->findOneBy(['longUrl' => $longUrl]);

        if ($urlMapping) {
            // Если запись уже существует, возвращаем короткий URL
            $shortUrl = $this->generateShortUrl($urlMapping->getShortCode());
        } else {
            // Создаем новую запись и генерируем короткий код
            // Генерируем уникальный короткий код
            $shortCode = $this->generateShortCode();

            // Создаем новую запись в базе данных
            $urlMapping = new UrlMapping();
            $urlMapping->setLongUrl($longUrl);
            $urlMapping->setShortCode($shortCode);

            // Сохраняем запись в базе данных
            $entityManager->persist($urlMapping);
            $entityManager->flush();

            // Формируем короткий URL на основе базового URL вашего приложения и сгенерированного кода
            $shortUrl = $this->generateShortUrl($shortCode);
        }

        // Возвращаем короткий URL
        return new Response($shortUrl, Response::HTTP_CREATED);
    }

    #[Route('/{shortCode}', name: 'app_redirect', methods: ['GET'])]
    public function redirectLink(Request $request, string $shortCode, EntityManagerInterface $entityManager)
    {
        // Ищем запись в базе данных по короткому коду
        $urlMapping = $entityManager->getRepository(UrlMapping::class)->findOneBy(['shortCode' => $shortCode]);

        if ($urlMapping) {
            // Если запись найдена, перенаправляем пользователя по длинному URL
            return $this->redirect($urlMapping->getLongUrl());
        }

        // Если запись не найдена, возвращаем ошибку
        return new Response('Страница не найдена', Response::HTTP_NOT_FOUND);
    }

    /**
     * Генерация уникального короткого кода
     */
    private function generateShortCode()
    {
        // Логика генерации случайного короткого кода, например, с использованием базовой кодировки или алгоритмов хэширования
        // ...

        return 'generated_short_code'; // Замените на фактическую генерацию кода
    }

    /**
     * Формирование короткого URL на основе базового URL вашего приложения и короткого кода
     */
    private function generateShortUrl(string $shortCode)
    {
        // Замените 'your_app_base_url' на базовый URL вашего приложения
        return 'your_app_base_url/' . $shortCode;
    }

    #[Route('/create-url-mapping', name: 'create_url', methods: ['GET', 'POST'])]
    public function createUrlMapping(Request $request, UrlMappingRepository $urlMappingRepository)
    {
        $urlMapping = new UrlMapping();

        $form = $this->createForm(UrlMappingFormType::class, $urlMapping);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $urlMappingRepository->save($urlMapping, true);

            return $this->redirectToRoute('url_mapping_success');
        }

        return $this->render('url_mapping/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/url-mapping/success", name="url_mapping_success")
     */
    #[Route('/url-mapping/success', name: 'url_mapping_success')]
    public function success()
    {
        return $this->render('url_mapping/success.html.twig');
    }
}
