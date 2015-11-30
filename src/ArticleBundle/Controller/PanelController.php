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

                return $this->redirect('/' . $data['slug']);
            }else{
                $form->addError(new FormError("Tytuł ten występuje już w bazie wiedzy!"));
            }
        }

        return $this->render( 'ArticleBundle:Panel:form.html.twig', [ 'form' => $form->createView(), 'title'=> 'Dodaj nowy artykuł' ] );
    }

    public function editArticleAction(Request $request){
        $article = $this->getDoctrine()->getRepository('ArticleBundle:Article')->findOneBy([ 'slug' => $request->get('slug') ]);
        $form = $this->createFormBuilder($article)
            ->add('title', 'text')
            ->add('slug', 'hidden')
            ->add('author', 'text')
            ->add('tags', 'text')
            ->add('content', 'ckeditor', array('input_sync' => true))
            ->getForm();

        $form->handleRequest($request);

        if ( $form->isValid() ){
            $data = $form->getData();

            $baseArticle = $this->getDoctrine()->getRepository('ArticleBundle:Article')->findBySlug($request->get('slug'));

            if ( $baseArticle == FALSE || $data['slug'] == $request->get('slug') ){
                $article->setTitle( $data['title'] );
                $article->setSlug($data['slug']);
                $article->setAuthor( $data['author'] );
                $article->setTags( $data['tags'] );
                $article->setContent( $data['content'] );

                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();

                return $this->redirect('/' . $data['slug']);
            }else{
                $form->addError(new FormError("Tytuł ten występuje już w bazie wiedzy!"));
            }
        }

        return $this->render( 'ArticleBundle:Panel:form.html.twig', [ 'form' => $form->createView(), 'title'=> 'Edytuj artykuł' ] );
    }

    public function myArticleListAction(){
        return $this->redirectToRoute('article_list');
    }

    public function articlesListAction($page){
        $a = ( $page == 1 ) ? 0 : ($page -1) * 10;

        //pobieram artykuły wg strony
        $articles = $this->getDoctrine()->getRepository('ArticleBundle:Article')->findBy(
            [], ['id' => 'ASC'], 25, $a
        );

        //pobieram ilość atrykułów w db
        $count = count($this->getDoctrine()->getRepository('ArticleBundle:Article')->findBy(
            [], ['id' => 'ASC']
        ));
        return $this->render('ArticleBundle:Panel:list.html.twig', ['articles' => $articles, 'count' => intval($count / 10), 'page' => $page ]);
    }

    public function articlesDeleteAction($slug){
        $ob = $this->getDoctrine()->getRepository('ArticleBundle:Article')->findOneBy( ['slug' => $slug]);
        $em = $this->getDoctrine()->getManager();

        if ( $ob !== FALSE ){
            $em->remove($ob);
            $em->flush();
        }
        return $this->redirectToRoute('article_list');
    }
}
