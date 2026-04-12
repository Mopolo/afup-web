<?php

declare(strict_types=1);

namespace AppBundle\Site\Entity;

use AppBundle\Site\Enum\ArticleContentType;
use AppBundle\Site\Enum\ArticleEtat;
use AppBundle\Site\Enum\ArticleTheme;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_site_article')]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(name: 'id_site_rubrique', nullable: false)]
    public int $idRubrique; // todo relation

    #[ORM\Column(length: 255, nullable: false)]
    public string $titre;

    #[ORM\Column(length: 255, nullable: false)]
    public string $raccourci;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $chapeau = null;

    #[ORM\Column(type: 'text', nullable: false)]
    public string $contenu;

    #[ORM\Column(length: 255, nullable: false, enumType: ArticleContentType::class)]
    public ArticleContentType $typeContenu;

    #[ORM\Column(nullable: false)]
    public int $position = 0;

    #[ORM\Column(name: 'date', type: 'unix_timestamp', nullable: true)]
    public ?\DateTime $datePublication = null;

    #[ORM\Column(nullable: false, enumType: ArticleEtat::class)]
    public ArticleEtat $etat;

    #[ORM\Column(nullable: true)]
    public ?int $idPersonnePhysique = null;

    #[ORM\Column(nullable: true, enumType: ArticleTheme::class)]
    public ?ArticleTheme $theme = null;

    #[ORM\Column(name: 'id_forum', nullable: true)]
    public ?int $idEvenement = null;

    public function __construct()
    {
        $this->position = 0;
        $this->etat = ArticleEtat::EnAttente;
        $this->typeContenu = ArticleContentType::Markdown;
        $this->datePublication = new \DateTime();
    }

    public function formatedChapeau(): ?string
    {
        $chapeau = $this->chapeau;

        if ($this->typeContenu === ArticleContentType::Markdown) {
            $parseDown = new \Parsedown();
            $chapeau = $parseDown->parse($chapeau);
        }

        return $chapeau;
    }

    public function formatedContenu(): ?string
    {
        $contenu = $this->contenu;

        if ($this->typeContenu === ArticleContentType::Markdown) {
            $parseDown = new \Parsedown();
            $contenu = $parseDown->parse($contenu);
        }

        return $contenu;
    }

    public function getApercu(): string
    {
        if (strlen((string) $this->chapeau) !== 0) {
            return strip_tags((string) $this->chapeau);
        }

        return substr(strip_tags($this->contenu), 0, 200);
    }

    public function getTextApercu(): string
    {
        return html_entity_decode($this->getApercu());
    }

    public function getSlug(): string
    {
        return $this->id . '-' . $this->raccourci;
    }
}
