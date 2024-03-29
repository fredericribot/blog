<?php
// src/Controller/BlogController.php
namespace App\Controller;
use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/blog", name="blog_")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();
        if (!$articles) {
            throw $this->createNotFoundException(
                'No article found in article\'s table.'
            );
        }
        return $this->render(
            'blog/index.html.twig',
            ['articles' => $articles]
        );
    }
    /**
     * Getting a article with a formatted slug for title
     *
     * @param string $slug The slugger
     *
     * @Route("/show/{slug<^[a-z0-9-]+$>}",
     *     defaults={"slug" = null},
     *     name="show")
     *  @return Response A response instance
     */
    public function show(?string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find an article in article\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ',
            ucwords(trim(strip_tags($slug)), "-")
        );
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$article) {
            throw $this->createNotFoundException(
                'No article with ' . $slug . ' title, found in article\'s table.'
            );
        }
        return $this->render(
            'blog/show.html.twig',
            [
                'article' => $article,
                'slug' => $slug,
            ]
        );
    }
    /**
     * Getting all the articles of a category by name
     *
     * @param string $categoryName The categoryName
     *
     * @ @Route("/category/{categoryName<^[a-z0-9-]+$>}", name="show_category")",
     *     defaults={"slug" = null},
     *     name="show")
     *  @return Response A response instance
     */
    // public function showByCategory(string $categoryName)
    // {
    //     $category = $this->getDoctrine()
    //         ->getRepository(Category::class)
    //         ->findOneBy(['name' => mb_strtolower($categoryName)]);
    //     $articles = $this->getDoctrine()
    //         ->getRepository(Article::class)
    //         ->findBy(['category' => $category], ['id' => 'DESC'], 3);
    //     return $this->render(
    //         'blog/category.html.twig',
    //         [
    //             'category' => $category,
    //             'articles' => $articles,
    //         ]
    //     );
    // }
/**Meme chose avec methode "param converter" */
/**
 * @Route("/category/{name}", name="article_show")
 */
public function showByCategory(Category $category): Response
{    
    $articles = $category->getArticles();

    return $this->render(
        'blog/category.html.twig',
        [
            'category' => $category,
            'articles' => $articles,
        ]);
    }
}