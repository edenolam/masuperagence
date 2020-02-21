<?php

namespace App\Repository;

use App\Entity\Picture;
use App\Entity\Property;
use App\Entity\PropertySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Property::class);
        $this->paginator = $paginator;
    }


    /**
     * @param PropertySearch $search
     * @param int $page
     * @return PaginationInterface
     */
    public function paginateAllVisibleQuery(PropertySearch $search, int $page): PaginationInterface
    {
        $query = $this->findVisibleQuery();

        if ($search->getMaxPrice()) {
            $query = $query
                ->andWhere('p.price <= :maxprice')
                ->setParameter('maxprice', $search->getMaxPrice());
        }
        if ($search->getMinSurface()) {
            $query = $query
                ->andWhere('p.surface >= :minsurface')
                ->setParameter('minsurface', $search->getMinSurface());
        }
        if ($search->getEquipements()->count() > 0) {
            $k = 0;
            foreach ($search->getEquipements() as $equipement) {
                $k++;
                $query = $query
                    ->andWhere(":equipement$k MEMBER OF p.equipements")
                    ->setParameter("equipement$k", $equipement);
            }
        }
        $properties = $this->paginator->paginate(
            $query->getQuery(),
            $page,
            12
        );


        $this->hydratePicture($properties);
        return $properties;
    }

    /**
     * @return Property[] Returns an array of Property objects
     */
    public function findLatest(): array
    {
        $properties = $this->findVisibleQuery()
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
        $this->hydratePicture($properties);
        return $properties;
    }


    private function findVisibleQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('p')->where('p.sold = false');
    }

    // pour reduire le nombre de requetes

    /**
     * @param $properties
     */
    private function hydratePicture($properties)
    {
        if (method_exists($properties, 'getItems')) {
            $properties = $properties->getItems();
        }
        $pictures = $this->getEntityManager()->getRepository(Picture::class)->findForProperties($properties);
        foreach ($properties as $property) {
            /**
             * @var $property Property
             */
            if ($pictures->containsKey($property->getId())) {
                $property->setPicture($pictures->get($property->getId()));
            }
        }
    }


}
