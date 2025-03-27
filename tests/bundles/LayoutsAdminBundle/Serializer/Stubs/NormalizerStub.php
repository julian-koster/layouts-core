<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Stubs;

use ArrayObject;
use Generator;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function is_object;

final class NormalizerStub implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param string|null $format
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): ArrayObject|array|string|int|float|bool|null
    {
        return 'data';
    }

    /**
     * @param mixed $data
     * @param string|null $format
     */
    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return is_object($data) && !$data instanceof Generator;
    }

    /**
     * @return array<string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => false,
        ];
    }
}
