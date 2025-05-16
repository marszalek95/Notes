<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteFormType;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
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
    public function show(NoteRepository $noteRepository, Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $queryBuilder = $noteRepository->findAllNotesQueryBuilder($user);

        $pagerfanta = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pagerfanta->setMaxPerPage(3);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('note/show.html.twig', [
            'notes' => $pagerfanta,
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

    #[Route('/notes/search', name: 'app_note_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        return $this->render('note/search.html.twig', [
            'query' => (string) $request->query->get('q', ''),
            'page' => (int) $request->query->get('page', 1),
        ]);
    }
}
