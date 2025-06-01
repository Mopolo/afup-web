<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Entity\Room;
use AppBundle\Event\Entity\RoomRepository;
use Symfony\Component\Form\FormView;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\RoomType;
use AppBundle\Event\Form\Support\EventSelectFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RoomAction extends AbstractController implements AdminActionWithEventSelector
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly FormFactoryInterface $formFactory,
        private readonly RoomRepository $roomRepository,
        private readonly EventSelectFactory $eventSelectFactory,
    ) {}

    public function __invoke(Request $request)
    {
        $id = $request->query->get('id');

        $event = $this->eventActionHelper->getEventById($id);
        $rooms = $this->roomRepository->findByEvent($event);
        $editForms = $this->getFormsForRooms($rooms);

        foreach ($editForms as $form) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                /** @var Room $newRoom */
                $room = $form->getData();
                if ($request->request->has('delete')) {
                    $this->roomRepository->delete($room);
                    $this->addFlash('notice', sprintf('La salle "%s" a été supprimée.', $room->name));
                } else {
                    $this->roomRepository->save($room);
                    $this->addFlash('notice', sprintf('La salle "%s" a été sauvegardée.', $room->name));
                }

                return $this->redirectToRoute('admin_event_room', [
                    'id' => $event->getId(),
                ]);
            }
        }

        $newRoom = new Room($event->getId());

        $addForm = $this->createForm(RoomType::class, $newRoom);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            /** @var Room $newRoom */
            $newRoom = $addForm->getData();
            $this->roomRepository->save($newRoom);
            $this->addFlash('notice', sprintf('La salle "%s" a été ajoutée.', $newRoom->name));

            return $this->redirectToRoute('admin_event_room', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('admin/event/rooms.html.twig', [
            'event' => $event,
            'rooms' => $rooms,
            'addForm' => $addForm === null ? null : $addForm->createView(),
            'editForms' => $editForms === null ? null : array_map(static fn(Form $form): FormView => $form->createView(), $editForms),
            'title' => 'Gestion des salles',
            'event_select_form' => $this->eventSelectFactory->create($event, $request)->createView(),
        ]);
    }

    /**
     * @param array<Room> $rooms
     * @return FormInterface[]
     */
    private function getFormsForRooms(array $rooms): array
    {
        $forms = [];
        foreach ($rooms as $room) {
            $forms[] = $this->formFactory->createNamedBuilder('edit_room_' . $room->id, RoomType::class,
                $room)->getForm();
        }

        return $forms;
    }
}
