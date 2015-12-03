<?php

namespace ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use ArticleBundle\Entity\Article;

class ApiController extends Controller
{
    public function getArticleAction($id){


        $article = $this->getDoctrine()->getRepository('ArticleBundle:Article')->findOneBy(['id' => $id]);

        if ( $article == FALSE ){
            return new JsonResponse([]);
        }

        $articleArrayData = [];
        $articleArrayData['title'] = $article->getTitle();
        $articleArrayData['author'] = $article->getAuthor();
        $articleArrayData['content'] = $article->getContent();
        $articleArrayData['tags'] = $article->getTags();
        $articleArrayData['slug'] = $article->getSlug();

        return new JsonResponse( $articleArrayData );
    }

    public function getRamdomArticleAction(){
        $repo = $this   ->getDoctrine()
            ->getManager()
            ->getRepository('ArticleBundle:Article');

        $qb = $repo->createQueryBuilder('title');
        $qb->select('COUNT(title)');

        $total = $qb->getQuery()->getSingleScalarResult();

        return $this->getArticleAction(rand(1, $total));
    }
}
