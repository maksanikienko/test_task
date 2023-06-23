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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UrlController extends AbstractController
{
    #[Route('/shorten', name: 'app_shorten', methods: [ 'POST'])]
    public function shorten(Request $request, EntityManagerInterface $entityManager)
    {
        // Получение длинного URL из тела запроса
        $longUrl = $request->request->get('longUrl');
        

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
            $shortUrl = $this->generateShortUrl( $shortCode);
        }

        // Возвращаем короткий URL
        return $this->render('index/shortUrl.html.twig', [
            'shortUrl' => $shortUrl,
        ]);
    }

    #[Route('/go-{shortCode}', name: 'app_redirect', methods: ['GET'])]
    public function redirectLink(Request $request, string $shortCode, EntityManagerInterface $entityManager)
    {
        $shortCode = $request->get('shortCode');
        
        $urlMapping = $entityManager->getRepository(UrlMapping::class)->findOneBy(['shortCode' => $shortCode]);

        if ($urlMapping) {
            $clicks = $urlMapping->getClickCount() ?? 0;
            $urlMapping->setClickCount($clicks + 1);

            $entityManager->persist($urlMapping);
            $entityManager->flush();

            return $this->redirect($urlMapping->getLongUrl());
        }

        
        return new Response('Страница не найдена', Response::HTTP_NOT_FOUND);
    }

    private function generateShortCode($length = 10)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $shortCode = '';
    
    $max = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $shortCode .= $characters[random_int(0, $max)];
    }
    
    return $shortCode;
    }

    
    private function generateShortUrl(string $shortCode)
    {
        
        
        return $this->generateUrl('app_redirect',['shortCode' => $shortCode], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    //выводит все ссылки для admin пользователя
    #[Route('/url', name: 'url_show', methods: ['GET'])] 
    public function getAllUrl(UrlMappingRepository $urlMappingRepository)
    {
        return $this->render('admin/urlMapping/index.html.twig', [
            'allUrl' => $urlMappingRepository->findAll(),
        ]);
    }

    
   
    
}
