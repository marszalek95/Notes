<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteFormType;
use App\Repository\FriendshipRepository;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NoteController extends AbstractController
{
    #[Route('/notes', name: 'app_notes', methods: ['GET'])]
    public function index(NoteRepository $noteRepository, Request $request): Response
    {
        $user = $this->getUser();
        $queryBuilder = $noteRepository->findAllNotesQueryBuilder($user);
        $page = $request->query->get('page', 1);

        $pagerfanta = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pagerfanta->setMaxPerPage(6);
        $pagerfanta->setCurrentPage($page);

        return $this->render('note/index.html.twig', [
            'notes' => $pagerfanta,
        ]);
    }
    
    #[Route('/notes/add', name: 'app_note_create', methods: ['GET', 'POST'])]
    public function create(Request $request ,EntityManagerInterface $entityManager, FriendshipRepository $friendshipRepository): Response
    {
        $note = new Note();

        $user = $this->getUser();
        $friends = $friendshipRepository->listAllAcceptedFriendships($user);
        $form = $this->createForm(NoteFormType::class, $note, [
            'friends' => $friends,
        ]);

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

    #[Route('/note/delete/{id}', name: 'app_note_delete', methods: ['POST'])]
    public function delete(Note $note, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($note->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot delete this note.');
        }

        $submittedToken = $request->getPayload()->get('token');

        if (!$this->isCsrfTokenValid('delete-item', $submittedToken)) {
            $this->addFlash('danger', 'Wrong CSRF token');
            return $this->redirectToRoute('app_notes');
        }

        $entityManager->remove($note);
        $entityManager->flush();
        $this->addFlash('success', 'Note deleted successfully');
        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }

    #[Route('note/unshare/{id}', name: 'app_note_unshare', methods: ['POST'])]
    public function removeSharedNote(Note $note, Request $request, EntityManagerInterface $entityManager): Response
    {
        $submittedToken = $request->getPayload()->get('token');

        if (!$this->isCsrfTokenValid('unshare-item', $submittedToken)) {
            $this->addFlash('danger', 'Wrong CSRF token');
            return $this->redirectToRoute('app_notes');
        }

        $note->removeSharedWith($this->getUser());
        $entityManager->flush();
        $this->addFlash('success', 'Shared note deleted successfully');
        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }

    #[Route('/notes/edit/{id}' , name: 'app_note_edit', methods: ['GET', 'POST'])]
    public function edit(Note $note, Request $request, EntityManagerInterface $entityManager, FriendshipRepository $friendshipRepository): Response
    {
        if ($note->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot edit this note.');
        }

        $user = $this->getUser();
        $friends = $friendshipRepository->listAllAcceptedFriendships($user);
        $form = $this->createForm(NoteFormType::class, $note, [
            'friends' => $friends,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $note->setOwner($user);

            $entityManager->persist($note);
            $entityManager->flush();

            $this->addFlash('success', 'Note edited successfully');

            return $this->redirectToRoute('app_notes');
        }

        return $this->render('note/edit.html.twig', [
            'form' => $form->createView(),
        ]);
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
