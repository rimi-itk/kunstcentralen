<?php

/*
 * This file is part of itk-dev/kunstcentralen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataFixtures;

use App\Entity\WorkOfArt;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;

class WorkOfArtFixture extends AbstractFixture implements DependentFixtureInterface
{
    protected $class = WorkOfArt::class;

    public function getDependencies()
    {
        return [
            ArtistFixture::class,
            LocationFixture::class,
            CategoryFixture::class,
        ];
    }

    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function load(ObjectManager $manager)
    {
        parent::load($manager);

        $source = __DIR__.'/Data/uploads';
        $target = __DIR__.'/../../public/uploads';

        $directoryIterator = new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $this->filesystem->mkdir($target.\DIRECTORY_SEPARATOR.$iterator->getSubPathName());
            } else {
                $this->filesystem->copy($item, $target.\DIRECTORY_SEPARATOR.$iterator->getSubPathName());
            }
        }
    }
}
