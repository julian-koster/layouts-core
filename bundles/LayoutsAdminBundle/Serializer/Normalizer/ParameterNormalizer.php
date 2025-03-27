<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use ArrayObject;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\Parameters\Parameter;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ParameterNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param string|null $format
     *
     * @return mixed[]|string|int|float|bool|null
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): ArrayObject|array|string|int|float|bool|null
    {
        /** @var \Netgen\Layouts\Parameters\Parameter $parameter */
        $parameter = $object->getValue();
        $parameterDefinition = $parameter->getParameterDefinition();

        return $parameterDefinition->getType()->toHash($parameterDefinition, $parameter->getValue());
    }

    /**
     * @param mixed $data
     * @param string|null $format
     */
    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof Parameter;
    }

    /**
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            Value::class => false,
        ];
    }
}
