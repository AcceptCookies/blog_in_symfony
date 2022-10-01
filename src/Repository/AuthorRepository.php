<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function add(Author $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Author $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getFirstOrCreate(): Author
    {
        $query = $this->createQueryBuilder('a')->getQuery();

        $paginator = new Paginator ($query);
        $paginator->getQuery()
            ->setFirstResult(0)
            ->setMaxResults(1);
        $author = $paginator->getQuery()->execute();

        if ($author === []) {
            // dummy data for non-registered account
            $author = new Author();
            $author->setName('dummy name')
                ->setEmail('dummy@email.com');
            $this->getEntityManager()->persist($author);
            $this->getEntityManager()->flush();
        } else {
            $author = $author[0];
        }

        return $author;
    }
}
