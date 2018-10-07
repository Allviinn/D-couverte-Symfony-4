<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;

class ArticleFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = \Faker\Factory::create('fr_FR');

        //Créer 3 catégories fake

        for ($i=0; $i < 3; $i++) { 
            $category = new Category();
            $category->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph());
            $manager->persist($category);

            $content = '<p>'.join($faker->paragraphs(5), '</p><p>').'</p>';

            //Créer 4-6 articles dans cette catégorie
            for ($j=0; $j < mt_rand(4, 6); $j++) { 
                $article = new Article();
                $article->setTitle($faker->sentence())
                        ->setContent($content)
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                        ->setCategory($category);
                $manager->persist($article);


                for ($k=0; $k < mt_rand(4, 10); $k++) { 
                    $comment = new Comment();

                    $content = '<p>'.join($faker->paragraphs(2), '</p><p>').'</p>';

                    $now = new \DateTime();
                    $interval = $now->diff($article->getCreatedAt());
                    $days = $interval->days;
                    $minimum = '-'.$days.' days';


                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween($minimum))
                            ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}
