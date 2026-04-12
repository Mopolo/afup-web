<?php

declare(strict_types=1);

namespace AppBundle\Site\Form;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Site\Entity\Article;
use AppBundle\Site\Entity\Repository\RubriqueRepository;
use AppBundle\Site\Enum\ArticleContentType;
use AppBundle\Site\Enum\ArticleEtat;
use AppBundle\Site\Enum\ArticleTheme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleType extends AbstractType
{
    public const POSITIONS_RUBRIQUES = 9;

    public function __construct(
        private readonly RubriqueRepository $rubriqueRepository,
        private readonly EventRepository $eventRepository,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $positions = [];
        for ($i = self::POSITIONS_RUBRIQUES; $i >= -(self::POSITIONS_RUBRIQUES); $i--) {
            $positions[$i] = $i;
        }
        $rubriques = [];
        foreach ($this->rubriqueRepository->findAll() as $rubrique) {
            $rubriques[$rubrique->nom] = $rubrique->id;
        }

        $events = [];
        foreach ($this->eventRepository->getAll() as $event) {
            $events[$event->getTitle()] = $event->getId();
        }

        /** @var Article|null $article */
        $article = $builder->getData();
        $textareaCssClass = 'simplemde';
        if ($article !== null && $article->typeContenu !== ArticleContentType::Markdown) {
            $textareaCssClass = 'tinymce';
        }

        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l\'article',
                'required' => true,
                'attr' => [
                    'maxlength' => 255,
                    'size' => 60,
                ],
                'constraints' => [
                    new Assert\Length(max: 255),
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
            ])
            ->add('chapeau', TextareaType::class, [
                'label' => 'Chapeau',
                'required' => false,
                'attr' => [
                    'cols' => 42,
                    'rows' => 10,
                    'class' => $textareaCssClass,
                ],
                'constraints' => [
                    new Assert\Type('string'),
                ],
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu',
                // Désactive la validation HTML5, nécessaire à cause du wysiwyg qui masque l'input
                // tout en le mettant à required, ce qui bloque la soumission du formulaire.
                'required' => false,
                'attr' => [
                    'cols' => 42,
                    'rows' => 20,
                    'class' => $textareaCssClass,
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Ce champ est obligatoire'),
                    new Assert\Type('string'),
                ],
            ])
            ->add('raccourci', TextType::class, [
                'required' => true,
                'label' => 'Raccourci',
                'attr' => [
                    'maxlength' => 255,
                    'size' => 60,
                ],
                'constraints' => [
                    new Assert\Length(max: 255),
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                    new Assert\Regex('/(\s)/', 'Ne doit pas contenir d\'espaces', null, false),
                ],
            ])
            ->add('idRubrique', ChoiceType::class, [
                'required' => true,
                'label' => 'Rubrique',
                'choices' => $rubriques,
                'constraints' => [
                    new Assert\Type("integer"),
                ],
            ])
            ->add('datePublication', DateTimeType::class, [
                'required' => false,
                'html5' => true,
                'label' => 'Date',
                'input' => 'datetime',
                'widget' => 'single_text',
                'years' => range(2001, date('Y')),
                'attr' => [
                    'style' => 'display: flex;',
                ],
                'constraints' => [
                    new Assert\Type("datetime"),
                ],
            ])
            ->add('position', ChoiceType::class, [
                'required' => false,
                'label' => 'Position',
                'choices' => $positions,
                'translation_domain' => false,
                'placeholder' => false,
                'constraints' => [
                    new Assert\Type("integer"),
                ],
            ])
            ->add('etat', EnumType::class, [
                'label' => 'Etat',
                'class' => ArticleEtat::class,
                'choice_label' => fn(ArticleEtat $etat) => $etat->label(),
            ])
            ->add('theme', EnumType::class, [
                'label' => 'Thème',
                'class' => ArticleTheme::class,
                'required' => false,
            ])
            ->add('idEvenement', ChoiceType::class, [
                'label' => 'Événement',
                'required' => false,
                'choices' => $events,
                'constraints' => [
                    new Assert\Type("integer"),
                ],
            ])
        ;
    }
}
