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
        // 1) Leer y validar JSON crudo
        $raw = $request->getContent();
        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return $this->json(['error' => 'JSON inválido'], 400);
        }

        // 2) Validar campos requeridos
        foreach (['book_id', 'rating', 'comment'] as $key) {
            if (!array_key_exists($key, $data)) {
                return $this->json(['error' => 'Faltan campos requeridos: book_id, rating, comment'], 400);
            }
        }

        // Normalizar/validar tipos básicos
        $bookId  = (int) $data['book_id'];
        $rating  = (int) $data['rating'];
        $comment = trim((string) $data['comment']);

        if ($bookId <= 0) {
            return $this->json(['error' => 'book_id debe ser un entero positivo'], 400);
        }
        if ($rating < 1 || $rating > 5) {
            return $this->json(['error' => 'rating debe estar entre 1 y 5'], 400);
        }
        if ($comment === '') {
            return $this->json(['error' => 'comment no puede estar vacío'], 400);
        }

        // 3) Buscar el libro
        /** @var Book|null $book */
        $book = $this->entityManager->getRepository(Book::class)->find($bookId);
        if (!$book) {
            return $this->json(['error' => 'El libro especificado no existe'], 404);
        }

        // 4) Crear y poblar la entidad Review
        $review = new Review();
        $review->setBook($book);
        $review->setRating($rating);
        $review->setComment($comment);
        $review->setCreatedAt(new \DateTime());

        // 5) Validación por constraints en la entidad (si las tienes)
        $violations = $this->validator->validate($review);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $v) {
                $errors[] = $v->getPropertyPath() . ': ' . $v->getMessage();
            }
            return $this->json(['error' => 'Errores de validación', 'details' => $errors], 400);
        }

        // 6) Persistir
        $this->entityManager->persist($review);
        $this->entityManager->flush();

        return $this->json([
            'id'         => $review->getId(),
            'book_id'    => $review->getBook()->getId(),
            'rating'     => $review->getRating(),
            'comment'    => $review->getComment(),
            'created_at' => $review->getCreatedAt()->format('Y-m-d H:i:s'),
        ], 201);
    }

    // ========= OPCIONAL: helpers para "ver" reseñas =========

    #[Route('/reviews', name: 'api_reviews_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $reviews = $this->entityManager->getRepository(Review::class)->findBy([], ['id' => 'DESC']);

        $data = array_map(static function (Review $r): array {
            return [
                'id'         => $r->getId(),
                'book_id'    => $r->getBook()?->getId(),
                'rating'     => $r->getRating(),
                'comment'    => $r->getComment(),
                'created_at' => $r->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $reviews);

        return $this->json($data);
    }

    #[Route('/books/{id}/reviews', name: 'api_reviews_by_book', methods: ['GET'])]
    public function byBook(int $id): JsonResponse
    {
        $book = $this->entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            return $this->json(['error' => 'Book no encontrado'], 404);
        }

        $reviews = $this->entityManager->getRepository(Review::class)->findBy(['book' => $book], ['id' => 'DESC']);

        $data = array_map(static function (Review $r): array {
            return [
                'id'         => $r->getId(),
                'book_id'    => $r->getBook()?->getId(),
                'rating'     => $r->getRating(),
                'comment'    => $r->getComment(),
                'created_at' => $r->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $reviews);

        return $this->json($data);
    }
}
