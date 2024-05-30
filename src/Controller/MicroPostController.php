<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(EntityManagerInterface $entityManager, MicroPostRepository $posts): Response
    {
        // adding an new object in the DB
//        $microPost = new MicroPost();
//        $microPost->setTitle('It comes from controller');
//        $microPost->setText('Hi!');
//        $microPost->setCreated(new DateTime());

//        tell Doctrine you want to (eventually) save the Product (no queries yet)
//        $entityManager->persist($microPost);
//
//        actually executes the queries (i.e. the INSERT query)
//        $entityManager->flush();

        // // updating an object from DB
//        $microPost = $entityManager->getRepository(MicroPost::class)->find(1);
//        $microPost -> setTitle('Welcome to Romania!');
//        $entityManager->flush();

        return $this->render('micro_post/index.html.twig', [
//            'posts' => $posts->findAll(), // this makes a lot of queries, but we can use instead a query builder to make just 1 query for optimization
            'posts' => $posts->findAllWithComments(),
        ]);
    }

    #[Route('/micro-post/top-liked', name: 'app_micro_post_topliked')]
    public function topLiked(MicroPostRepository $posts): Response
    {
        return $this->render(
            'micro_post/top_liked.html.twig',
            [
                'posts' => $posts->findAllWithMinLikes(1),
            ]
        );
    }

    #[Route('/micro-post/follows', name: 'app_micro_post_follows')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function follows(MicroPostRepository $posts): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        return $this->render(
            'micro_post/follows.html.twig',
            [
                'posts' => $posts->findAllByAuthors(
                    $currentUser->getFollows()
                ),
            ]
        );
    }

    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function showOne(MicroPost $post): Response
    {
        // The bundle uses the {id} from the route to query for the post by the id column. If it's not found, a 404 page is generated.

        return $this->render('micro_post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/micro-post/{post}/edit', name: 'app_micro_post_edit')]
    #[IsGranted(MicroPost::EDIT, 'post')]
    public function edit(MicroPost $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MicroPostType::class, $post);
        $form->handleRequest($request);

        //$this->denyAccessUnlessGranted(MicroPost::EDIT, 'post');

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Your micro post have been updated.');

            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render(
            'micro_post/edit.html.twig',
            [
                'form' => $form,
                'post' => $post
            ]
        );
    }

    #[Route('/micro-post/{post}/comment', name: 'app_micro_post_comment')]
    #[IsGranted('ROLE_COMMENTER')]
    public function addComment(MicroPost $post, Request $request, EntityManagerInterface $entityManager, CommentRepository $comments): Response
    {
        $form = $this->createForm(CommentType::class, new Comment());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());
            $comments->add($comment, true);

//            $entityManager->persist($comment);
//            $entityManager->flush();

            $this->addFlash('success', 'Your comment have been updated.');

            return $this->redirectToRoute('app_micro_post_show',
                ['post' => $post->getId()]);
        }

        return $this->render(
            'micro_post/comment.html.twig',
            [
                'form' => $form,
                'post' => $post
            ]
        );
    }

    #[Route('/micro-post/add', name: 'app_micro_post_add', priority: 2)]
    //#[IsGranted('IS_AUTHENTICATED_FULLY')]  // the access is denied immediately
    #[IsGranted('ROLE_WRITER')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        // the access is denied whenever you want, for e.g. u can make a request or something before
//        $this->denyAccessUnlessGranted(
//            'IS_AUTHENTICATED_FULLY'
//            //'PUBLIC_ACCESS'
//        );

        $form = $this->createForm(MicroPostType::class, new MicroPost());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setAuthor($this->getUser());

            //persist and add data to DB
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Your micro post have been added.');

            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render(
            'micro_post/add.html.twig',
            [
                'form' => $form
            ]
        );
    }
}
