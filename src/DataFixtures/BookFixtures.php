<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Crear libros
        $book1 = new Book();
        $book1->setTitle('El Arte de Programar');
        $book1->setAuthor('Donald Knuth');
        $book1->setPublishedYear(1968);
        $manager->persist($book1);

        $book2 = new Book();
        $book2->setTitle('Clean Code');
        $book2->setAuthor('Robert C. Martin');
        $book2->setPublishedYear(2008);
        $manager->persist($book2);

        $book3 = new Book();
        $book3->setTitle('Refactoring');
        $book3->setAuthor('Martin Fowler');
        $book3->setPublishedYear(1999);
        $manager->persist($book3);

        // Crear reseñas para libro 1 (3 reseñas)
        $review1 = new Review();
        $review1->setBook($book1);
        $review1->setRating(5);
        $review1->setComment('Libro fundamental para cualquier programador. Excelente contenido.');
        $manager->persist($review1);

        $review2 = new Review();
        $review2->setBook($book1);
        $review2->setRating(4);
        $review2->setComment('Muy técnico pero muy valioso. Algunos ejemplos están desactualizados.');
        $manager->persist($review2);

        $review3 = new Review();
        $review3->setBook($book1);
        $review3->setRating(5);
        $review3->setComment('Una obra maestra de la programación. Lectura obligatoria.');
        $manager->persist($review3);

        // Crear reseñas para libro 2 (2 reseñas)
        $review4 = new Review();
        $review4->setBook($book2);
        $review4->setRating(5);
        $review4->setComment('Cambió mi forma de escribir código. Altamente recomendado.');
        $manager->persist($review4);

        $review5 = new Review();
        $review5->setBook($book2);
        $review5->setRating(4);
        $review5->setComment('Buenas prácticas esenciales. Fácil de leer y aplicar.');
        $manager->persist($review5);

        // Crear reseña para libro 3 (1 reseña)
        $review6 = new Review();
        $review6->setBook($book3);
        $review6->setRating(3);
        $review6->setComment('Útil pero algunos conceptos son complejos de implementar.');
        $manager->persist($review6);

        $manager->flush();
    }
}