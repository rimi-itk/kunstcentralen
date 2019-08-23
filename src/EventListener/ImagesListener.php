<?php

/*
 * This file is part of itk-dev/kunstcentralen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\EventListener;

use App\Entity\WorkOfArt;
use App\Service\ImageGenerator;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ImagesListener
{
    /** @var \App\Service\ImageGenerator */
    private $imageGenerator;

    public function __construct(ImageGenerator $imageGenerator)
    {
        $this->imageGenerator = $imageGenerator;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->setImages($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->setImages($args);
    }

    private function setImages(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof WorkOfArt) {
            $this->imageGenerator->setImages($object);
        }
    }
}
