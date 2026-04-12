<?php

declare(strict_types=1);

namespace AppBundle\Site\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Site\Entity\Article;
use AppBundle\Site\Entity\Rubrique;
use AppBundle\Site\Enum\ArticleEtat;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Article>
 */
final class ArticleRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @return int[]
     */
    public function getAllYears(): array
    {
        $result = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('YEAR(FROM_UNIXTIME(a.date)) as year')
            ->from('afup_site_article', 'a')
            ->where('a.id_site_rubrique = :rubricId')
            ->andWhere('a.etat = :etat')
            ->setParameter('etat', ArticleEtat::EnLigne->value)
            ->groupBy('YEAR(FROM_UNIXTIME(a.date))')
            ->orderBy('YEAR(FROM_UNIXTIME(a.date))', 'DESC')
            ->setParameter('rubricId', Rubrique::ID_RUBRIQUE_ACTUALITES)
            ->executeQuery()
            ->fetchAllAssociative();

        return array_column($result, 'year');
    }

    /**
     * @return mixed[]
     */
    public function getEventsLabelsById(): array
    {
        $result = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('f.id', 'f.titre')
            ->from('afup_site_article', 'a')
            ->join('a', 'afup_forum', 'f', 'a.id_forum = f.id')
            ->groupBy('f.id')
            ->orderBy('f.date_debut', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        $eventsLabelsById = [];
        foreach ($result as $row) {
            $eventsLabelsById[$row['id']] = $row['titre'];
        }

        return $eventsLabelsById;
    }

    public function countPublishedNews(array $filters): int
    {
        $qb = $this->buildPublishedNewsQuery($filters);
        $qb->select('COUNT(a.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return Article[]
     */
    public function findPublishedNews(int $page, int $itemsPerPage, array $filters): array
    {
        $qb = $this->buildPublishedNewsQuery($filters);

        return $qb
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Article[]
     */
    public function findAllPublishedNews(array $filters = []): array
    {
        return $this->buildPublishedNewsQuery($filters)->getQuery()->getResult();
    }

    public function findPrevious(Article $article): ?Article
    {
        if ($article->datePublication === null) {
            return null;
        }

        return $this->buildPublishedNewsQuery(['before_date' => $article->datePublication])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNext(Article $article): ?Article
    {
        if ($article->datePublication === null) {
            return null;
        }

        return $this->buildPublishedNewsQuery(['after_date' => $article->datePublication], 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function buildPublishedNewsQuery(array $filters, string $order = 'DESC'): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.idRubrique = :rubricId')
            ->andWhere('a.etat = :etat')
            ->setParameter('etat', ArticleEtat::EnLigne)
            ->andWhere('a.datePublication <= CURRENT_TIMESTAMP()')
            ->setParameter('rubricId', Rubrique::ID_RUBRIQUE_ACTUALITES)
            ->orderBy('a.datePublication', $order);

        if (isset($filters['year']) && count($filters['year']) > 0) {
            $qb->andWhere('YEAR(FROM_UNIXTIME(a.datePublication)) IN (:years)')->setParameter('years', $filters['year']);
        }

        if (isset($filters['theme']) && count($filters['theme']) > 0) {
            $qb->andWhere('a.theme IN (:themes)')->setParameter('themes', $filters['theme']);
        }

        if (isset($filters['event']) && count($filters['event']) > 0) {
            $qb->andWhere('a.idEvenement IN (:events)')->setParameter('events', $filters['event']);
        }

        if (isset($filters['after_date'])) {
            $qb->andWhere('a.datePublication > :dateApres')->setParameter('dateApres', $filters['after_date']);
        }

        if (isset($filters['before_date'])) {
            $qb->andWhere('a.datePublication < :dateAvant')->setParameter('dateAvant', $filters['before_date']);
        }

        return $qb;
    }

    public function findNewsBySlug(string $slug): ?Article
    {
        return $this->createQueryBuilder('a')
            ->where('a.idRubrique = :rubricId')
            ->andWhere("CONCAT(a.id, '-', a.raccourci) = :slug")
            ->setParameter('rubricId', Rubrique::ID_RUBRIQUE_ACTUALITES)
            ->setParameter('slug', $slug)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findBySlug(string $slug): ?Article
    {
        return $this->createQueryBuilder('a')
            ->where("CONCAT(a.id, '-', a.raccourci) = :slug")
            ->setParameter('slug', $slug)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return mixed[]
     */
    public function getAllArticlesWithCategoryAndTheme(string $ordre = 'titre', string $direction = 'desc', string $filtre = '%'): array
    {
        $allowedColumns = ['date', 'titre', 'etat', 'id', 'nom_rubrique', 'nom_forum'];
        if ($direction !== 'desc' && $direction !== 'asc') {
            $direction = 'desc';
        }
        if (!in_array($ordre, $allowedColumns, true)) {
            $ordre = 'titre';
        }

        return $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('a.*', 'r.nom AS nom_rubrique', 'f.titre AS nom_forum')
            ->from('afup_site_article', 'a')
            ->join('a', 'afup_site_rubrique', 'r', 'a.id_site_rubrique = r.id')
            ->leftJoin('a', 'afup_forum', 'f', 'a.id_forum = f.id')
            ->where('a.titre LIKE :filtre OR a.contenu LIKE :filtre')
            ->orderBy($ordre, $direction)
            ->setParameter('filtre', '%' . $filtre . '%')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @return Article[]
     */
    public function findListForHome(): array
    {
        return $this->createQueryBuilder('a')
            ->join(Rubrique::class, 'r', 'WITH', 'a.idRubrique = r.id')
            ->where('a.etat = :etat')
            ->setParameter('etat', ArticleEtat::EnLigne)
            ->andWhere('a.datePublication <= CURRENT_TIMESTAMP()')
            ->andWhere('r.idParent <> 52')
            ->andWhere('r.id <> :idAssociation')
            ->andWhere('r.id <> :idAntennes')
            ->andWhere('r.id <> :idNosActions')
            ->setParameter('idAssociation', Rubrique::ID_RUBRIQUE_ASSOCIATION)
            ->setParameter('idAntennes', Rubrique::ID_RUBRIQUE_ANTENNES)
            ->setParameter('idNosActions', Rubrique::ID_RUBRIQUE_NOS_ACTIONS)
            ->orderBy('a.datePublication', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}
