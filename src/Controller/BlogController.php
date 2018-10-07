<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Doctrine\Common\Persistence\ObjectManager;
use App\Repository\ArticleRepository;

use App\Form\ArticleType;
use App\Form\CommentType;

use App\Entity\Article;
use App\Entity\Comment;


class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
    	$articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }


    /**
     * @Route("/", name="home")
     */
    public function home() {
    	return $this->render('blog/home.html.twig', ['title' => 'Hello world',]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $req, ObjectManager $manager) {
    	if (!$article) {
    		$article = new Article();
    	}   	

    	$form = $this->createForm(ArticleType::class, $article);

    	$form->handleRequest($req);

    	if ($form->isSubmitted() && $form->isValid()) {
    		if (!$article->getId()) {
    			$article->setCreatedAt(new \Datetime());
    		}    		
    		$manager->persist($article);
    		$manager->flush();

    		return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
    	}

    	return $this->render('blog/create.html.twig', [
    		'formArticle' => $form->createView(),
    		'editMode' => $article->getId() !== null
    	]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article, Request $req, ObjectManager $manager) {

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
                
            $comment->setCreatedAt(new \DateTime());
            $comment->setArticle($article);

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

       


    	return $this->render('blog/show.html.twig', ['article' => $article, 'formComment' => $form->createView()]);
    }


   
}
