<?php

namespace ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use ArticleBundle\Entity\Article;

class PanelController extends Controller
{
    public function panelAction(){
        //apki od tomka :D
    }

    public function addArticleAction(Request $request){
        $form = $this->createFormBuilder()
            ->add('title', 'text')
            ->add('slug', 'hidden')
            ->add('author', 'text')
            ->add('tags', 'text')
            ->add('content', 'ckeditor', array('input_sync' => true))
            ->getForm();

        $form->handleRequest($request);
        if ( $form->isValid() ){
            $data = $form->getData();
            $baseArticle = $this->getDoctrine()->getRepository('ArticleBundle:Article')->findBySlug($data['slug']);

            if ( $baseArticle == FALSE ){
                $article = new Article();
                $article->setTitle( $data['title'] );
                $article->setSlug($data['slug']);
                $article->setAuthor( $data['author'] );
                $article->setTags( $data['tags'] );
                $article->setContent( $data['content'] );

                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();

                return $this->redirect('/articles');
            }else{
                $form->addError(new FormError("Tytuł ten występuje już w bazie wiedzy!"));
            }
        }

        return $this->render( 'ArticleBundle:Panel:form.html.twig', [ 'form' => $form->createView() ] );
    }

    public function editArticleAction(Request $request){
        $slug = "jakis-tam-tytul";
        $baseArticle = $this->getDoctrine()->getRepository('ArticleBundle:Article')->findBySlug($slug);
        $form = $this->createFormBuilder($baseArticle)
            ->add('title', 'text')
            ->add('slug', 'hidden')
            ->add('author', 'text')
            ->add('tags', 'text')
            ->add('content', 'ckeditor', array('input_sync' => true))
            ->getForm();

        $form->handleRequest($request);

        if ( $form->isValid() ){
            $data = $form->getData();
            $baseArticle = $this->getDoctrine()->getRepository('ArticleBundle:Article')->findBySlug($data['slug']);

            if ( $baseArticle == FALSE || $data['slug'] == $slug ){
                $article = new Article();
                $article->setTitle( $data['title'] );
                $article->setSlug($data['slug']);
                $article->setAuthor( $data['author'] );
                $article->setTags( $data['tags'] );
                $article->setContent( $data['content'] );

                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();

                return $this->redirect('/articles');
            }else{
                $form->addError(new FormError("Tytuł ten występuje już w bazie wiedzy!"));
            }
        }

        return $this->render( 'ArticleBundle:Panel:form.html.twig', [ 'form' => $form->createView() ] );
    }

    public function myArticleListAction(){
        return $this->redirect('/articles');
    }
}
