<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site\Sheet;

use AppBundle\AuditLog\Audit;
use AppBundle\Site\Entity\Repository\FeuilleRepository;
use AppBundle\Site\Form\SheetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditSheetAction extends AbstractController
{
    public function __construct(
        private readonly FeuilleRepository $feuilleRepository,
        private readonly Audit $audit,
        #[Autowire('%kernel.project_dir%/../htdocs/templates/site/images')]
        private readonly string $storageDir,
    ) {}

    public function __invoke(int $id, Request $request): Response
    {
        $sheet = $this->feuilleRepository->find($id);
        $form = $this->createForm(SheetType::class, $sheet);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file instanceof UploadedFile) {
                $file->move($this->storageDir, $file->getClientOriginalName());
                $sheet->image = $file->getClientOriginalName();
            }
            $this->feuilleRepository->save($sheet);
            $this->audit->log('Modification de la feuille ' . $sheet->nom);
            $this->addFlash('notice', 'La feuille ' . $sheet->nom . ' a été modifiée');
            return $this->redirectToRoute('admin_site_sheets_list');
        }
        $image = $sheet->image !== null ? '/templates/site/images/' . $sheet->image : false;

        return $this->render('admin/site/sheet_form.html.twig', [
            'form' => $form->createView(),
            'image' => $image,
            'formTitle' => 'Modifier une feuille',
            'submitLabel' => 'Modifier',
        ]);
    }
}
