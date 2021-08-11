<?php declare(strict_types=1);

namespace Symfona\Bundle\JsonRequestBundle\Tests\App\Request;

class ExampleDTO
{
    public int $id;
    public int $age;
    public string $name;
    public bool $isAgree;
    public ExampleDTO $child;
}
