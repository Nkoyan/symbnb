<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use App\Service\Pagination;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/admin/comments", name="admin_comment_index")
     */
    public function index(CommentRepository $commentRepository, Request $request, Pagination $pagination)
    {
        $pagination = $pagination
            ->setEntityClass(Comment::class)
            ->setCurrentPage($request->query->get('page', 1))
            ->paginate();

        return $this->render('admin/comment/index.html.twig', [
            'comments' => $pagination->getData(),
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/admin/comments/{id}/edit", name="admin_comment_edit")
     */
    public function edit(Comment $comment, Request $request, ObjectManager $manager)
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

    /**
     * @Route("/admin/comments/{id}/delete", name="admin_comment_delete")
     */
    public function delete(Comment $comment, ObjectManager $manager)
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
