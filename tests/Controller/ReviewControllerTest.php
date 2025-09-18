<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReviewControllerTest extends WebTestCase
{
    public function testCreateReviewSuccess(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/reviews', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'book_id' => 1,
                'rating' => 5,
                'comment' => 'Test review - excelente libro para aprender'
            ])
        );

        $this->assertResponseStatusCodeSame(201);
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('book_id', $data);
        $this->assertArrayHasKey('rating', $data);
        $this->assertArrayHasKey('comment', $data);
        
        $this->assertEquals(1, $data['book_id']);
        $this->assertEquals(5, $data['rating']);
        $this->assertEquals('Test review - excelente libro para aprender', $data['comment']);
    }

    public function testCreateReviewValidationErrorInvalidRating(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/reviews', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'book_id' => 1,
                'rating' => 6, // Rating inválido (debe ser 1-5)
                'comment' => 'Test comment'
            ])
        );

        $this->assertResponseStatusCodeSame(400);
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertArrayHasKey('details', $data);
    }

    public function testCreateReviewValidationErrorEmptyComment(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/reviews', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'book_id' => 1,
                'rating' => 4,
                'comment' => '' // Comentario vacío
            ])
        );

        $this->assertResponseStatusCodeSame(400);
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testCreateReviewNonExistentBook(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/reviews', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'book_id' => 999, // Libro que no existe
                'rating' => 4,
                'comment' => 'Test comment'
            ])
        );

        $this->assertResponseStatusCodeSame(400);
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertStringContainsString('no existe', $data['error']);
    }

    public function testCreateReviewMissingFields(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/reviews', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'rating' => 4
                // Faltan book_id y comment
            ])
        );

        $this->assertResponseStatusCodeSame(400);
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertStringContainsString('campos requeridos', $data['error']);
    }
}