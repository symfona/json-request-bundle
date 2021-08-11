<?php declare(strict_types=1);

namespace Symfona\Bundle\JsonRequestBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

final class ArgumentResolverTest extends WebTestCase
{
    public function testExample(): void
    {
        $body = \json_encode(['name' => 'John', 'age' => 21, 'isAgree' => true, 'child' => ['id' => 56789]]) ?: '';

        $client = self::createClient();
        $client->request(Request::METHOD_POST, '/12345', [], [], ['CONTENT_TYPE' => 'application/json'], $body);
        $response = $client->getResponse();

        $expected = '{"id":12345,"age":21,"name":"John","isAgree":true,"child":{"id":56789}}';

        $this->assertSame($expected, $response->getContent());
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testBadRequest(): void
    {
        $this->expectException(NotNormalizableValueException::class);

        $body = \json_encode(['name' => []]) ?: '';

        $client = self::createClient();
        $client->catchExceptions(false);
        $client->request(Request::METHOD_POST, '/12345', [], [], ['CONTENT_TYPE' => 'application/json'], $body);
    }
}
