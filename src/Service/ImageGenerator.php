<?php

/*
 * This file is part of itk-dev/kunstcentralen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Entity\WorkOfArt;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageGenerator
{
    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    /** @var \Liip\ImagineBundle\Imagine\Filter\FilterManager */
    private $filterManager;

    /** @var \Liip\ImagineBundle\Imagine\Cache\CacheManager */
    private $cacheManager;

    /** @var \Liip\ImagineBundle\Imagine\Data\DataManager */
    private $dataManager;

    /** @var \ApiPlatform\Core\Api\UrlGeneratorInterface */
    private $urlGenerator;

    /** @var \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface */
    private $parameters;

    public function __construct(
        EntityManagerInterface $entityManager,
        FilterManager $filterManager,
        CacheManager $cacheManager,
        DataManager $dataManager,
        UrlGeneratorInterface $urlGenerator,
        ParameterBagInterface $parameters
    ) {
        $this->entityManager = $entityManager;
        $this->filterManager = $filterManager;
        $this->cacheManager = $cacheManager;
        $this->dataManager = $dataManager;
        $this->parameters = $parameters;
        $this->urlGenerator = $urlGenerator;
    }

    public function setImages($entity, $field = 'image')
    {
        if ($entity instanceof WorkOfArt) {
            $imagePath = $this->getImagePath($entity->getImage(), $entity);
            if (null !== $imagePath) {
                $images = $this->getImages($imagePath);
                $entity->setImages($images);
            }
        }
    }

    public function getImagePath($path, $entity)
    {
        $basePath = '';
        if ($entity instanceof WorkOfArt) {
            $basePath = $this->parameters->get('app.path.works_of_art');
        }

        return $basePath.'/'.$path;
    }

    public function getImageUrl($path, $entity)
    {
        $path = $this->getImagePath($path, $entity);

        $context = $this->urlGenerator->getContext();
        $scheme = $context->getScheme();
        $host = $context->getHost();
        $port = '';
        if ('http' === $scheme && 80 !== $context->getHttpPort()) {
            $port = ':'.$context->getHttpPort();
        } elseif ('https' === $scheme && 443 !== $context->getHttpsPort()) {
            $port = ':'.$context->getHttpsPort();
        }

        return $scheme.'://'.$host.$port.$context->getBaseUrl().$path;
    }

    public function setImagesBatch(
        string $className,
        array $ids = null,
        OutputInterface $output
    ) {
        $this->process($className, $ids, $output);
    }

    public function reset(
        string $className,
        array $entityIds = null,
        OutputInterface $output = null
    ) {
        $metadata = $this->entityManager->getClassMetadata($className);
        $imagesField = $metadata->getFieldName('images');

        $values = [];
        $types = [];

        $sql = 'update '.$metadata->getTableName().' set '.$imagesField.' = null';
        if (null !== $entityIds) {
            $sql .= ' where id in (?)';
            $values[] = $entityIds;
            $types[] = Connection::PARAM_INT_ARRAY;
        }
        $this->entityManager->getConnection()
            ->executeQuery($sql, $values, $types);
    }

    private function process(
        string $className,
        array $entityIds = null,
        OutputInterface $output = null
    ) {
        $generate = false;
        $metadata = $this->entityManager->getClassMetadata($className);
        $idField = $metadata->getFieldName('id');
        $imagesField = $metadata->getFieldName('images');

        $sql = 'update '.$metadata->getTableName().' set '.$imagesField.' = :images where '.$idField.' = :id';
        $updateStmt = $this->entityManager->getConnection()->prepare($sql);

        $entities = $this->getEntities($className, $entityIds);

        foreach ($entities as $index => $entity) {
            $url = $entity['image'];
            $images = $this->getImages($url, $generate);

            if (null !== $output) {
                $output->writeln(
                    sprintf(
                        '% 8d % 8d %s %s',
                        $index + 1,
                        $entity['id'],
                        $url,
                        json_encode($images)
                    )
                );
            }

            if (null !== $images) {
                switch ($metadata->getTypeOfField('images')) {
                    case 'json_array':
                        $encodedImages = json_encode($images);

                        break;
                    case 'array':
                        $encodedImages = serialize($images);

                        break;
                }
                $updateStmt->execute(
                    ['images' => $encodedImages, 'id' => $entity['id']]
                );
            }
        }
    }

    private function getImages(
        string $path,
        $generate = false,
        OutputInterface $output = null
    ) {
        if (empty($path)) {
            return null;
        }

        $filters = array_keys(
                $this->filterManager->getFilterConfiguration()->all()
            );

        $images = [];
        foreach ($filters as $filter) {
            if ($generate) {
                if (!$this->cacheManager->isStored($path, $filter)) {
                    try {
                        $binary = $this->dataManager->find(
                                $filter,
                                $path
                            );
                        $binary = $this->filterManager->applyFilter(
                                $binary,
                                $filter
                            );
                        $this->cacheManager->store(
                                $binary,
                                $path,
                                $filter
                            );
                    } catch (\Exception $ex) {
                        if (null !== $output) {
                            $output->writeln(
                                    '<error>'.$ex->getMessage().'</error>'
                                );
                        }

                        break;
                    }
                }
            }
            $images[$filter] = $this->cacheManager->getBrowserPath(
                    $path,
                    $filter
                );
        }

        return $images;
    }

    private function getEntities(
        $className,
        $entityIds,
        $offset = null,
        $limit = null
    ) {
        $metadata = $this->entityManager->getClassMetadata($className);
        $queryBuilder = $this->entityManager->getRepository($className)
            ->createQueryBuilder('e');
        $idField = 'e.'.$metadata->getFieldName('id');
        $imageField = 'e.'.$metadata->getFieldName('image');
        $imagesField = 'e.'.$metadata->getFieldName('images');
        $queryBuilder->select([
            $idField.' as id',
            $imageField.' as image',
            $imagesField.' as images',
        ])
            ->where($queryBuilder->expr()->isNotNull($imageField))
            ->andWhere($imageField.' != :empty')
            ->setParameter('empty', '')
            ->andWhere($queryBuilder->expr()->isNull($imagesField));

        if (null !== $offset) {
            $queryBuilder->setFirstResult($offset);
        }
        if (null !== $limit) {
            $queryBuilder->setMaxResults($limit);
        }

        if (null !== $entityIds) {
            $queryBuilder->andWhere($queryBuilder->expr()
                ->in($idField, $entityIds));
        }

        $query = $queryBuilder->getQuery();

        return $query->execute();
    }
}
