<?php
declare(strict_types=1);

namespace AppBundle\Command;

use PlanetePHP\FeedReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PlaneteCrawlFeeds extends Command
{
    private FeedReader $feedReader;

    public function __construct(FeedReader $feedReader)
    {
        parent::__construct();

        $this->feedReader = $feedReader;
    }

    protected function configure(): void
    {
        $this
            ->setName('indexing:planete')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->feedReader->crawl();

        return 0;
    }
}
