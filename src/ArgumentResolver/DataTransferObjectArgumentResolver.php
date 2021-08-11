<?php declare(strict_types=1);

namespace Symfona\Bundle\JsonRequestBundle\ArgumentResolver;

use Symfona\Bundle\JsonRequestBundle\Attribute\DTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class DataTransferObjectArgumentResolver implements ArgumentValueResolverInterface
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        foreach ($argument->getAttributes() as $attribute) {
            if ($attribute instanceof DTO) {
                return true;
            }
        }

        return false;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        try {
            yield from $this->createObject($request, $argument);
        } catch (\TypeError $exception) {
            throw new NotNormalizableValueException($exception->getMessage());
        }
    }

    private function getRequestData(Request $request): array
    {
        $data = \array_merge(
            $request->attributes->get('_route_params', []),
            $request->files->all(),
            $request->query->all(),
            $request->request->all(),
        );

        $content = (string) $request->getContent();
        $contentType = $request->getContentType();

        if ($this->serializer instanceof DecoderInterface && false === empty($content) && null !== $contentType) {
            $data = \array_merge(
                $data,
                $this->serializer->decode($content, $contentType),
            );
        }

        return $data;
    }

    private function createObject(Request $request, ArgumentMetadata $argument): \Generator
    {
        $data = $this->getRequestData($request);

        if ($this->serializer instanceof DenormalizerInterface) {
            yield $this->serializer->denormalize($data, $argument->getType() ?: '', null, [
                AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
            ]);
        }
    }
}
