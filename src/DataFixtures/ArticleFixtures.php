<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Comment;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 1; $i < 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence())
                ->setDescription("<p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Esse, corporis?</p>");

            $manager->persist($category);




            for ($j = 1; $j <= mt_rand(4,6); $j++) {
                $article = new Article();

                $content = '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quas ipsam magnam dolor incidunt minima officiis doloribus voluptatem, voluptatum alias! Aspernatur, sapiente rerum veritatis quam aliquam ab minima. Quasi, explicabo nesciunt?</p>
                <p>Atque consequuntur sapiente et cumque repellendus molestiae. Delectus dignissimos tempora minus quibusdam, corporis eum. Inventore recusandae maiores adipisci! Labore, laudantium. Ad aut quas dicta, soluta nesciunt alias saepe quos nisi?</p>
                <p>Dolore, nostrum est corrupti voluptatum reiciendis dolorum fuga obcaecati sunt eius earum perferendis, similique mollitia nulla quia deleniti tempore! Nam cum eius dolores praesentium rerum, delectus quos sed quidem quis!</p>
                <p>Quidem, maiores soluta! Dolorum voluptatem perspiciatis perferendis consequatur quaerat esse ipsa. Consectetur pariatur sit itaque labore delectus at corporis, temporibus, perspiciatis non, fuga similique error laboriosam eius vero provident. Aut.</p>
                <p>Sapiente nihil eius fuga magnam unde. Explicabo ipsum, optio quasi ullam, aliquam, quisquam dolor hic accusantium nobis excepturi repudiandae! Asperiores repudiandae placeat officiis voluptatem esse aliquam, ad pariatur libero ipsam!</p>';


                $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-6 mounths'))
                    ->setCategory($category);

                $manager->persist($article);

                for($k = 1 ; $k <= mt_rand(4,10); $k++) {

                  

                $now = new \DateTime();
                $interval = $now->diff($article->getCreatedAt());
                $days = $interval->days;
                $min = '-' . $days . ' days';
                    $comment = new Comment();
                    $comment->setAuthor($faker->name())
                            ->setContent('<p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Esse, corporis?</p>')
                            ->setCreatedAt($faker->dateTimeBetween($min))
                            ->setArticle($article);
                    $manager->persist($comment);
                }
            }
        }




        $manager->flush();
    }
}
