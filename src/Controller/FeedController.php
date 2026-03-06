<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FeedController extends AbstractController
{
    #[Route('/feed', name: 'app_feed')]
    public function index(Request $request): Response
    {
        $newPost = null;

        $form = $this->createFormBuilder()
            ->add('author', TextType::class, [
                'label' => 'Ваше ім’я',
                'attr' => ['class' => 'block w-full border rounded p-2 mb-3', 'placeholder' => 'Введіть нікнейм']
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Текст публікації',
                'attr' => ['class' => 'block w-full border rounded p-2 mb-3', 'rows' => 3, 'placeholder' => 'Що у вас нового?']
            ])
            ->add('publish', SubmitType::class, [
                'label' => 'Опублікувати',
                'attr' => ['class' => 'bg-indigo-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-indigo-700 transition']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Отримуємо дані з форми для миттєвого відображення
            $data = $form->getData();
            $newPost = [
                'author' => $data['author'],
                'content' => $data['content'],
                'date' => date('Y-m-d H:i')
            ];
            
            $this->addFlash('success', 'Допис опубліковано!');
            // В реальному проекті тут був би редирект, але для ЛР ми покажемо результат одразу
        }

        return $this->render('feed/index.html.twig', [
            'postForm' => $form->createView(),
            'newPost' => $newPost
        ]);
    }
}
