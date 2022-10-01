<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function add(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getPaginatedPosts($page, $postsPerPage): array
    {
        $postsArray = [];
        $query = $this->createQueryBuilder('p')
            ->orderBy('p.created', 'DESC')
            ->getQuery();

        $paginator = new Paginator ($query);
        $paginator->getQuery()
            ->setFirstResult($postsPerPage * ($page - 1))
            ->setMaxResults($postsPerPage);

        foreach ($paginator as $post) {
            $post->comment_count = $post->getComment()->count();
            array_push($postsArray, $post);
        }

        $data['posts'] =  $postsArray;
        $data['posts_count'] = $paginator->count();

        return $data;
    }
}
