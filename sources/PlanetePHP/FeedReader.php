<?php

declare(strict_types=1);

namespace PlanetePHP;

use Laminas\Feed\Reader\Reader;

final class FeedReader
{
    private GuzzleFeedClient $httpClient;
    private FeedRepository $feedRepository;
    private FeedArticleRepository $feedArticleRepository;

    public function __construct(GuzzleFeedClient $httpClient, FeedRepository $feedRepository, FeedArticleRepository $feedArticleRepository)
    {
        $this->httpClient = $httpClient;
        $this->feedRepository = $feedRepository;
        $this->feedArticleRepository = $feedArticleRepository;

        Reader::setHttpClient($this->httpClient);
    }

    public function crawl(): void
    {
        $feeds = $this->feedRepository->findActive();

        foreach ($feeds as $feed) {
            $items = Reader::import($feed->getUrl());

            foreach ($items as $item) {
                if ($item->getDateCreated() > new \DateTime('-7 days')) {
                    continue;
                }

                $article = new FeedArticle(
                    null,
                    $feed->getId(),
                    $item->getId(),
                    $item->getTitle(),
                    $item->getLink(),
                    $item->getDateCreated()->getTimestamp(),
                    $item->getAuthor(),
                    $item->getDescription(),
                    $item->getContent(),
                    $this->feedArticleRepository->isRelevant($item->getTitle() . ' ' . $item->getContent()),
                );

                $this->feedArticleRepository->save($article);
            }
        }
    }
}
