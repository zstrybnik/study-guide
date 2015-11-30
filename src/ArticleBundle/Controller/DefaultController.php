<?php

namespace ArticleBundle\Controller;

use ArticleBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**
     * @param $page numer strony
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAllArticlesAction($page){
        $a = ( $page == 1 ) ? 0 : ($page -1) * 10;

        //pobieram artykuły wg strony
        $articles = $this->getDoctrine()->getRepository('ArticleBundle:Article')->findBy(
          [], ['id' => 'DESC'], 10, $a
        );

        //pobieram ilość atrykułów w db
        $count = count($this->getDoctrine()->getRepository('ArticleBundle:Article')->findBy(
            [], ['id' => 'DESC']
        ));
        $article_count = ( intval($count / 10) == 0 ) ? 1 : intval($count / 10);
       return $this->render('ArticleBundle:Default:list.html.twig', ['articles' => $articles, 'article_count' => $article_count, 'page' => $page ]);
    }

    /**
     * @param $slug szukaj artykułu po formie skróconej
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewSelectArticleAction($slug){
        $article = $this->getDoctrine()->getRepository('ArticleBundle:Article')->findBySlug($slug);

        if ( $article == FALSE ){
            throw $this->createNotFoundException('Taki artykół nie istnieje!');
        }

        return $this->render('ArticleBundle:Default:simpleArticle.html.twig', ['article' => $article[0] ]);
    }
}
