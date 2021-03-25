<?php

namespace App\Controller;

use Exception;
use App\Entity\Participant;
use App\Entity\Conversation;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConversationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/conversations", name="conversations.")
 */
class ConversationController extends AbstractController
{

    /**
     * @Route("/{id}", name="newConversations")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function index(Request $request, int $id, UserRepository $userRepository, ConversationRepository $conversationRepository, EntityManagerInterface $em)
    {
        $otherUser = $request->get('otherUser', 0);
        $otherUser = $userRepository->find($id);

        if (is_null($otherUser)) {
            throw new \Exception("The user was not found");
        }

        // cannot create a conversation with myself
        if ($otherUser->getId() === $this->getUser()->getId()) {
            throw new \Exception("That's deep but you cannot create a conversation with yourself");
        }

        // check if conversation already exists
        $conversation = $conversationRepository->findConversationByParticipants(
            $otherUser->getId(),
            $this->getUser()->getId()
        );

        if (count($conversation)) {
            throw new \Exception("The conversation already exists");
        }

        $conversation = new Conversation();

        $participant = new Participant();
        $participant->setUser($this->getUser());
        $participant->setConversation($conversation);


        $otherParticipant = new Participant();
        $otherParticipant->setUser($otherUser);
        $otherParticipant->setConversation($conversation);


        $em->getConnection()->beginTransaction();
        try {
            $em->persist($conversation);
            $em->persist($participant);
            $em->persist($otherParticipant);

            $em->flush();
            $em->commit();

        } catch (\Exception $e) {
            $em->rollback();
            throw $e;
        }



        return $this->json([
            'id' => $conversation->getId()
        ], Response::HTTP_CREATED, [], []);
    }
}
