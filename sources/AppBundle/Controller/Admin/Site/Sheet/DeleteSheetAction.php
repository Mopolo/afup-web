<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site\Sheet;

use AppBundle\AuditLog\Audit;
use AppBundle\Site\Entity\Repository\FeuilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class DeleteSheetAction extends AbstractController
{
    public function __construct(
        private readonly FeuilleRepository $feuilleRepository,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly Audit $audit,
    ) {}

    public function __invoke(int $id, string $token): RedirectResponse
    {
        if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('sheet_delete', $token))) {
            $this->addFlash('error', 'Token invalide');
            return $this->redirectToRoute('admin_site_sheets_list');
        }
        $sheet = $this->feuilleRepository->find($id);
        $name = $sheet->nom;
        $this->feuilleRepository->delete($sheet);
        $this->audit->log('Suppression de la feuille ' . $name);
        $this->addFlash('notice', 'La feuille ' . $name . ' a été supprimée');
        return $this->redirectToRoute('admin_site_sheets_list');
    }
}
