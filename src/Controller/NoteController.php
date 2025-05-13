<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteFormType;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NoteController extends AbstractController
{
    #[Route('/notes/add', name: 'app_note_create')]
    public function create(Request $request ,EntityManagerInterface $entityManager): Response
    {
        $note = new Note();

        $form = $this->createForm(NoteFormType::class, $note);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $note->setOwner($user);

            $entityManager->persist($note);
            $entityManager->flush();

            $this->addFlash('success', 'Note added successfully');

            return $this->redirectToRoute('app_notes');
        }

        return $this->render('note/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/notes', name: 'app_notes')]
    public function show(NoteRepository $noteRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $notes = $noteRepository->findBy(['owner' => $user], ['createdAt' => 'DESC']);

        return $this->render('note/show.html.twig', [
            'notes' => $notes,
        ]);
    }

    #[Route('/deletenote/{id}', name: 'app_note_delete', methods: ['POST'])]
    public function delete(Note $note,Request $request, EntityManagerInterface $entityManager): Response
    {
        $submittedToken = $request->getPayload()->get('token');

        if ($this->isCsrfTokenValid('delete-item', $submittedToken)) {
            $entityManager->remove($note);
            $entityManager->flush();
            $this->addFlash('success', 'Note deleted successfully');
        }

        return $this->redirectToRoute('app_notes');
    }
}
