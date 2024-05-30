<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use App\Repository\UserProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    private array $messages = [
        ['message' => 'Hello', 'created' => '2023/09/27'],
        ['message' => 'Hi', 'created' => '2023/10/12'],
        ['message' => 'Bye!', 'created' => '2021/05/09']
    ];

    #[Route('/', name: 'app_index')]
    public function index(UserProfileRepository $profiles, CommentRepository $comments, MicroPostRepository $posts): Response
    {
//        $user = new User();
//        $user->setEmail('email@email.com');
//        $user->setPassword('12345678');
//
//        $profile = new UserProfile();
//        $profile->setUser($user);
//        // add & remove method were added manually in UserRepository & UserProfileRepository
//        $profiles->add($profile, true);

//        $profile = $profiles->find(1);
//        $profiles->remove($profile, true);


        // connect 2 entity at the same time, post & comment
//        $post = new MicroPost(); // this is independent
//        $post->setTitle('Hello');
//        $post->setText('Hello');
//        $post->setCreated(new DateTime());

//        $post = $posts->find(9);
//        $comment = $post->getComments()[0];
//         $comment->setPost(null);
//         $comments->add($comment, true);
//        $post->getComments()->count();
//        $post->removeComment($comment); // only way to remove a comment

//        $comment = new Comment(); // using a separate repository we saved the comment
//        $comment->setText('Hello');
//        $comment->setPost($post);
////        $post->addComment($comment);
//        $posts->add($post, true);
//        $comments->add($comment, true);

//        dd($post);


        return $this->render('hello/index.html.twig',
            ['messages' => $this->messages,
                'limit' => 3]);
    }

    #[Route('/messages/{id<\d+>}', name: 'app_show_one')]
    public function showOne(int $id): Response
    {
        return $this->render('hello/show_one.html.twig',
            [
                'message' => $this->messages[$id]
            ]);
        //return new Response($this->messages[$id]);
    }
}
