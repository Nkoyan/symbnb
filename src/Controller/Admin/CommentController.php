<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route(path: '/admin/comments', name: 'admin_comment_index')]
    public function index(CommentRepository $commentRepository, Request $request, Pagination $pagination): Response
    {
        $pagination = $pagination
            ->setEntityClass(Comment::class)
            ->setCurrentPage($request->query->get('page', 1))
            ->paginate()
        ;

        return $this->render('admin/comment/index.html.twig', [
            'comments' => $pagination->getData(),
            'pagination' => $pagination,
        ]);
    }

    #[Route(path: '/admin/comments/{id}/edit', name: 'admin_comment_edit')]
    public function edit(Comment $comment, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire numéro {$comment->getId()} a bien été modifié !"
            );
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/comments/{id}/delete', name: 'admin_comment_delete')]
    public function delete(Comment $comment, EntityManagerInterface $manager): RedirectResponse
    {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le commentaire de {$comment->getAuthor()->getFullName()} a bien été supprimé !"
        );

        return $this->redirectToRoute('admin_comment_index');
    }
}
