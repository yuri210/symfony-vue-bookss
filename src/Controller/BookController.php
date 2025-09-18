<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class BookController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/books', name: 'api_books_list', methods: ['GET'])]
    public function getBooks(): JsonResponse
    {
        // Consulta eficiente con DQL para obtener libros con rating promedio
        $query = $this->entityManager->createQuery('
            SELECT b.title, b.author, b.publishedYear, 
                   COALESCE(AVG(r.rating), 0) as average_rating
            FROM App\Entity\Book b
            LEFT JOIN b.reviews r
            GROUP BY b.id, b.title, b.author, b.publishedYear
            ORDER BY b.title ASC
        ');

        $results = $query->getResult();
        
        // Formatear resultados
        $books = [];
        foreach ($results as $result) {
            $books[] = [
                'title' => $result['title'],
                'author' => $result['author'],
                'published_year' => $result['publishedYear'],
                'average_rating' => $result['average_rating'] > 0 ? round((float)$result['average_rating'], 1) : null
            ];
        }

        return $this->json($books);
    }
}