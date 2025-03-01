<?php

declare(strict_types=1);

namespace Typhoon\DeclarationId;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

#[CoversFunction('Typhoon\DeclarationId\classId')]
final class ClassIdFunctionTest extends TestCase
{
    public function testItResolvesAnonymousClassColumn(): void
    {
        $object = new class {};

        $id = classId($object::class);

        self::assertEquals(anonymousClassId(__FILE__, 15, 23), $id);
    }
}
