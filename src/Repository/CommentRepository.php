<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Figure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @return Comment[] Returns an array of Comment objects
     */
    public function findByExampleField(string $value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findOneBySomeField(string $value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function save(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
 /**
     * Récupère les commentaires avec pagination pour une figure donnée.
     *
     * @param int $figureId L'ID de la figure.
     * @param int $limit Le nombre de commentaires à récupérer.
     * @param int $offset L'offset pour la pagination.
     * @return Comment[] La liste des commentaires.
     */

   /**
     * Récupère les commentaires paginés pour une figure donnée.
     *
     * @param Figure $figure La figure pour laquelle récupérer les commentaires.
     * @param int $page Le numéro de la page actuelle.
     * @param int $limit Le nombre de commentaires par page.
     * @return array Un tableau contenant les commentaires paginés, la page actuelle et le nombre total de pages.
     */
    public function findPaginatedByFigure(Figure $figure, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        // Récupérer les commentaires paginés
        $queryBuilder = $this->createQueryBuilder('c')
            ->where('c.figure = :figure')
            ->setParameter('figure', $figure)
            ->orderBy('c.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $comments = $queryBuilder->getQuery()->getResult();

        // Compter le nombre total de commentaires pour cette figure
        $totalComments = $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.figure = :figure')
            ->setParameter('figure', $figure)
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'data' => $comments,
            'page' => $page,
            'totalPages' => ceil($totalComments / $limit)
        ];
    }
}
