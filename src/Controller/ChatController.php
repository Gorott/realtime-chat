<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Chat;
use App\Form\CreateChatFormType;
use App\Form\SendMessageFormType;
use App\Repository\ChatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{


    public function __construct(
        protected ChatRepository $chatRepository
    )
    {
    }

    /**
     * @Route("/chat/{id}", name="app_chat_room")
     */
    public function __invoke(string $id): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if (!$this->chatRepository->isUserMemberOfChat($this->getUser(), $id)) {
            return $this->redirectToRoute('app_chat');
        }



        $chat = $this->chatRepository->find($id);
        $createChatForm = $this->createForm(CreateChatFormType::class);
        $sendMessageForm = $this->createForm(SendMessageFormType::class);

        $chatRooms = $this->chatRepository->findAllChatsByUser($this->getUser());

        return $this->render('chat.html.twig', [
            'messages' => [],
            'chatRooms' => $chatRooms,
            'currentRoom' => $chat,
            'createChatForm' => $createChatForm->createView(),
            'sendMessageForm' => $sendMessageForm->createView(),
        ]);
    }
}
