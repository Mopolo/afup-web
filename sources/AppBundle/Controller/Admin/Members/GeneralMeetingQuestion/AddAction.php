<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeetingQuestion;

use AppBundle\AssembleeGenerale\AssembleeGeneraleRepository;
use AppBundle\AssembleeGenerale\Question;
use AppBundle\AssembleeGenerale\QuestionFormType;
use AppBundle\AssembleeGenerale\QuestionRepository;
use AppBundle\Association\Model\GeneralMeetingQuestion;
use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use AppBundle\GeneralMeeting\GeneralMeetingQuestionFormType;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddAction extends AbstractController
{
    public function __construct(
        private readonly GeneralMeetingQuestionRepository $generalMeetingQuestionRepository,
        private readonly GeneralMeetingRepository $generalMeetingRepository,
        private readonly QuestionRepository $questionRepository,
        private readonly AssembleeGeneraleRepository $assembleeGeneraleRepository,
    ) {}

    public function __invoke(Request $request, string $date): Response
    {
//        $dates = $this->assembleeGeneraleRepository->getAllDates();
//        dd($dates);
//
        dump($this->questionRepository->getAttendeeRaw('Mopolo', DatePoint::createFromTimestamp(1738969200)));
        $this->questionRepository->getAttendee('Mopolo', DatePoint::createFromTimestamp(1738969200));
        $questions = $this->questionRepository->findAll();
        dd($questions);

        $date = \DateTime::createFromFormat('U', $date);
        $generalMeeting = $this->generalMeetingRepository->findOneByDate($date);
        if (!$generalMeeting) {
            throw $this->createNotFoundException(sprintf('L\'assemblée générale en date du %s n\'a pas été trouvée', $date->format('d/m/Y')));
        }

        $question = new Question();
        $question->date = $generalMeeting['date'];
        $question->createdAt = new \DateTime();

        $form = $this->createForm(QuestionFormType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->questionRepository->save($question);
//            $this->generalMeetingQuestionRepository->save($question);
            $this->addFlash('notice', 'La question a été ajoutée');

            return $this->redirectToRoute('admin_members_general_vote_list', [
                'date' =>  $question->date->format('U'),
            ]);
        }

        return $this->render('admin/members/general_meeting_question/add.html.twig', [
            'general_meeting' => $generalMeeting,
            'form' => $form->createView(),
        ]);
    }
}
