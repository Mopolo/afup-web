<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\CFP;

use AppBundle\CFP\PhotoStorage;
use AppBundle\CFP\SpeakerFactory;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\SpeakerType;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

class SpeakerAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly SpeakerFactory $speakerFactory,
        private readonly SpeakerRepository $speakerRepository,
        private readonly TranslatorInterface $translator,
        private readonly PhotoStorage $photoStorage,
        private readonly SidebarRenderer $sidebarRenderer,
    ) {}

    public function __invoke(Request $request): Response
    {
        $event = $this->eventActionHelper->getEvent($request->attributes->get('eventSlug'));

        $speaker = $this->speakerFactory->getSpeaker($event);
        if ($event->getDateEndCallForPapers() < new DateTime() && $speaker->getId() === null) {
            return $this->render('event/cfp/closed.html.twig', [
                'event' => $event,
            ]);
        }

        $form = $this->createForm(SpeakerType::class, $speaker, [
            SpeakerType::OPT_PHOTO_REQUIRED => null === $speaker->getPhoto(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->speakerRepository->save($speaker);

            $file = $speaker->getPhotoFile();
            if ($file instanceof UploadedFile) {
                $fileName = $this->photoStorage->store($file, $speaker);
                $speaker->setPhoto($fileName);
                $this->speakerRepository->save($speaker);
            }

            /** @var Session $session */
            $session = $request->getSession();
            $this->addFlash('success', $this->translator->trans('Profil sauvegardé.'));
            if ($session->has('pendingInvitation')) {
                $session->remove('pendingInvitation');
                return $this->redirectToRoute('cfp_invite', $session->get('pendingInvitation'));
            }

            return $this->redirectToRoute('cfp_speaker', [
                'eventSlug' => $event->getPath(),
            ]);
        }

        $photo = null;
        if (null !== $speaker->getPhoto()) {
            $photo = $this->photoStorage->getUrl($speaker, PhotoStorage::DIR_ORIGINAL);
        }

        return $this->render('event/cfp/speaker.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'photo' => $photo,
            'sidebar' => $this->sidebarRenderer->render($event),
        ]);
    }
}
