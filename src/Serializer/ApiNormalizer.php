<?php

/*
 * This file is part of itk-dev/kunstcentralen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Serializer;

use App\Entity\WorkOfArt;
use App\Service\ImageGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ApiNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    /** @var \Symfony\Component\Serializer\Normalizer\DenormalizerInterface|\Symfony\Component\Serializer\Normalizer\NormalizerInterface */
    private $decorated;

    /** @var \App\Service\ImageGenerator */
    private $imageGenerator;

    public function __construct(NormalizerInterface $decorated, ImageGenerator $imageGenerator)
    {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }

        $this->decorated = $decorated;
        $this->imageGenerator = $imageGenerator;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = $this->decorated->normalize($object, $format, $context);

        if ($object instanceof WorkOfArt) {
            $imageUrl = $this->imageGenerator->getImageUrl($data['image'], $object, UrlGeneratorInterface::ABSOLUTE_URL);
            $data['image'] = $imageUrl;
        }

        return $data;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $this->decorated->supportsDenormalization($data, $type, $format);
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return $this->decorated->denormalize($data, $class, $format, $context);
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}
