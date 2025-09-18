<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testGetBooks(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/books');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        
        if (count($data) > 0) {
            $book = $data[0];
            $this->assertArrayHasKey('title', $book);
            $this->assertArrayHasKey('author', $book);
            $this->assertArrayHasKey('published_year', $book);
            $this->assertArrayHasKey('average_rating', $book);
        }
    }

    public function testBooksHaveCorrectStructure(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/books');

        $this->assertResponseIsSuccessful();
        
        $data = json_decode($client->getResponse()->getContent(), true);
        
        foreach ($data as $book) {
            $this->assertIsString($book['title']);
            $this->assertIsString($book['author']);
            $this->assertIsInt($book['published_year']);
            $this->assertTrue(
                is_null($book['average_rating']) || is_float($book['average_rating']) || is_int($book['average_rating'])
            );
        }
    }
}