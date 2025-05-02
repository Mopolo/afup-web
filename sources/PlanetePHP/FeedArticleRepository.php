<?php

declare(strict_types=1);

namespace PlanetePHP;

use Assert\Assertion;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

class FeedArticleRepository
{
    const RELEVANT = 1;
    const IRRELEVANT = 0;
    const PERTINENCE_LIST = 'php|afup|pear|pecl|symfony|copix|jelix|wampserver|simpletest|simplexml|zend|pmo|drupal|ovidentia|mvc|magento|chrome|spip|PDO|mock|cake|hiphop|CMS|Framework|typo3|photon|pattern';
    private readonly string $pertinenceRegex;

    public function __construct(private readonly Connection $connection)
    {
        $this->pertinenceRegex = '/' . self::PERTINENCE_LIST . '/i';
    }

    public function count(): int
    {
        $query = $this->connection->prepare('SELECT COUNT(b.id) FROM afup_planete_billet b');

        return intval($query->executeQuery()->fetchOne());
    }

    public function countRelevant(): int
    {
        $query = $this->connection->prepare('SELECT COUNT(b.id) FROM afup_planete_billet b WHERE b.etat = :status');
        $query->bindValue('status', self::RELEVANT);

        return intval($query->executeQuery()->fetchOne());
    }

    /**
     * @param string $sort
     * @param string $direction
     * @param int    $limit
     *
     * @return FeedArticle[]
     */
    public function search($sort, $direction, $limit = 20): array
    {
        $sorts = [
            'title' => 'b.titre',
            'content' => 'b.contenu',
            'status' => 'b.etat',
        ];
        Assertion::integer($limit);
        Assertion::keyExists($sorts, $sort);
        Assertion::inArray($direction, ['asc', 'desc']);
        $qb = $this->connection->createQueryBuilder();
        $qb->from('afup_planete_billet', 'b')
            ->select('b.*')
            ->orderBy($sorts[$sort], $direction)
            ->setMaxResults($limit);

        return $this->hydrate($qb->executeQuery()->fetchAllAssociative());
    }

    public function save(FeedArticle $billet)
    {
        $id = $this->findIdByKey($billet->getKey());
        if (null !== $id) {
            return $this->update($billet, $id);
        }

        return $this->insert($billet);
    }

    /**
     * @return array<DisplayableFeedArticle>
     */
    public function findLatest($page = 0, $format = DATE_ATOM, $nombre = 10): array
    {
        $query = $this->connection->prepare('SELECT b.titre, b.url, b.maj, b.auteur, b.contenu, f.nom feed_name, f.url feed_url
            FROM afup_planete_billet b
            INNER JOIN afup_planete_flux f on b.afup_planete_flux_id = f.id
            WHERE b.etat = :status
            ORDER BY b.maj DESC
            LIMIT :start, :length
        ');
        $query->bindValue('status', self::RELEVANT);
        $query->bindValue('start', $page * $nombre, ParameterType::INTEGER);
        $query->bindValue('length', $nombre, ParameterType::INTEGER);

        return $this->hydrateDisplayable($query->executeQuery()->fetchAllAssociative(), $format);
    }

    public function isRelevant($content): int
    {
        $content = strip_tags((string) $content);
        $relevant = self::IRRELEVANT;
        if (preg_match($this->pertinenceRegex, $content)) {
            $relevant = self::RELEVANT;
        }

        return $relevant;
    }

    private function findIdByKey($key)
    {
        $query = $this->connection->prepare('SELECT id FROM afup_planete_billet WHERE clef = :key');
        $query->bindValue('key', $key);
        $row = $query->executeQuery()->fetchAssociative();

        return is_array($row) ? $row['id'] : null;
    }

    private function update(FeedArticle $billet, $id = null)
    {
        return $this->connection->update(
            'afup_planete_billet',
            [
                'afup_planete_flux_id' => $billet->getFeedId(),
                'clef' => $billet->getKey(),
                'titre' => $billet->getTitle(),
                'url' => $billet->getUrl(),
                'maj' => $billet->getUpdate(),
                'auteur' => $billet->getAuthor(),
                'resume' => $billet->getSummary(),
                'contenu' => $billet->getContent(),
                'etat' => $billet->getStatus(),
            ],
            [
                'id' => $id ?: $billet->getId(),
            ],
        );
    }

    private function insert(FeedArticle $billet)
    {
        return $this->connection->insert('afup_planete_billet', [
            'afup_planete_flux_id' => $billet->getFeedId(),
            'clef' => $billet->getKey(),
            'titre' => $billet->getTitle(),
            'url' => $billet->getUrl(),
            'maj' => $billet->getUpdate(),
            'auteur' => $billet->getAuthor(),
            'resume' => $billet->getSummary(),
            'contenu' => $billet->getContent(),
            'etat' => $billet->getStatus(),
        ]);
    }

    private function hydrate(array $rows): array
    {
        return array_map(static fn (array $row): FeedArticle => new FeedArticle(
            $row['id'],
            $row['afup_planete_flux_id'],
            $row['clef'],
            $row['titre'],
            $row['url'],
            $row['maj'],
            $row['auteur'],
            $row['resume'],
            $row['contenu'],
            $row['etat']
        ), $rows);
    }

    /**
     * @return array<DisplayableFeedArticle>
     */
    private function hydrateDisplayable(array $rows, $format = DATE_ATOM): array
    {
        return array_map(static fn (array $row): DisplayableFeedArticle => new DisplayableFeedArticle(
            $row['titre'],
            $row['url'],
            date($format, (int) $row['maj']),
            $row['auteur'],
            $row['contenu'],
            $row['feed_name'],
            $row['feed_url']
        ), $rows);
    }
}
