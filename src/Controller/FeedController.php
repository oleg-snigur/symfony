<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FeedController extends AbstractController
{
    #[Route('/feed', name: 'app_feed')]
    public function index(
        Request $request, 
        EntityManagerInterface $entityManager, 
        PostRepository $postRepository
    ): Response {

        $post = new Post();

        $form = $this->createFormBuilder($post)
            ->add('content', TextareaType::class, [
                'label' => 'Що у вас нового?',
                'attr' => ['placeholder' => 'Напишіть щось цікаве...']
            ])
            ->getForm();


        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $post->setAuthor($this->getUser()->getUserIdentifier());
            $post->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Допис успішно опубліковано!');
            return $this->redirectToRoute('app_feed');
        }


        $posts = $postRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('feed/index.html.twig', [
            'postForm' => $form->createView(),
            'posts' => $posts,
        ]);
    }

    #[Route('/post/delete/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Post $post, EntityManagerInterface $entityManager): Response
    {

        if ($post->getAuthor() !== $this->getUser()->getUserIdentifier()) {
            throw $this->createAccessDeniedException('Ви не можете видалити чужий допис!');
        }

        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'Допис видалено.');
        return $this->redirectToRoute('app_feed');
    }
}
