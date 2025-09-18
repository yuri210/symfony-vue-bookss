<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class ReviewController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    #[Route('/reviews', name: 'api_reviews_create', methods: ['POST'])]
    public function createReview(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validar datos básicos
        if (!isset($data['book_id']) || !isset($data['rating']) || !isset($data['comment'])) {
            return $this->json([
                'error' => 'Faltan campos requeridos: book_id, rating, comment'
            ], 400);
        }

        // Buscar el libro
        $book = $this->entityManager->getRepository(Book::class)->find($data['book_id']);
        if (!$book) {
            return $this->json([
                'error' => 'El libro especificado no existe'
            ], 400);
        }

        // Crear nueva reseña
        $review = new Review();
        $review->setBook($book);
        $review->setRating((int)$data['rating']);
        $review->setComment(trim($data['comment']));

        // Validar entidad
        $violations = $this->validator->validate($review);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            return $this->json([
                'error' => 'Errores de validación',
                'details' => $errors
            ], 400);
        }

        // Guardar en base de datos
        $this->entityManager->persist($review);
        $this->entityManager->flush();

        return $this->json([
            'id' => $review->getId(),
            'book_id' => $review->getBook()->getId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment(),
            'created_at' => $review->getCreatedAt()->format('Y-m-d H:i:s')
        ], 201);
    }
}