<?php

namespace App\Controller;

use DateTime;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(ArticleRepository $articleRepo): Response
    {
        $article = $articleRepo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $article,
            'title' => 'Tous les articles'
        ]);
    }

    #[Route('/', name: 'home')]
    public function  home()
    {
        dump($_SERVER);
        return $this->render('blog/home.html.twig', [
            'title' => "Accueil"
        ]);
    }

    #[Route('/blog/new', name: 'blog_create')]
    #[Route('/blog/{id}/edit', name: 'blog_edit')]
    public function form(Article $article = null, Request $request, EntityManagerInterface $manager)
    {

        $title = "Édition article";

        if (!$article) {
            $article = new Article();
            $title = "Création Article";
        }


        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, [
                'attr' => [
                    "placeholder" => "Titre de l'article",
                    "class" => 'form-control'
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'attr' => ['class' => 'form-control']
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    "placeholder" => "Contenu de l'article",
                    "class" => 'form-control'
                ]
            ])
            ->add('image', TextType::class, [
                'attr' => [
                    "placeholder" => "Image de l'article",
                    "class" => 'form-control'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }


            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', [
                'title' => $title,
                'id' => $article->getId()
            ]);
        }


        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'EditMode' => $article->getId() !== null,
            'title' =>  $title
        ]);
    }



    #[Route('/blog/{id}', name: 'blog_show')]
    public function show($id, ArticleRepository $articleRepo, Request $request, EntityManagerInterface $manager, Article $article)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser()->getUserIdentifier();
            $comment->setCreatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            $comment->setArticle($article);
            $comment->setAuthor($user);
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        $article = $articleRepo->find($id);
        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView(),
            'title' => $article->getTitle()
        ]);
    }

    #[Route('/comment/{id}/delete/{idArticle}', name: 'delete_comment')]
    public function deleteComment($id, $idArticle, CommentRepository $commentRepo, EntityManagerInterface $manager, Comment $comment)
    {
        $comment = $commentRepo->find($id);
        $manager->remove($comment);
        $manager->flush();

        return $this->redirectToRoute('blog_show', ['id' => $idArticle]);
    }
}
