<?php

namespace App\Controller;

use App\Entity\Channel;
use App\Entity\User;
use App\Repository\ChannelRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ChannelController extends AbstractController
{
    /**
     * @Route("/chat/new/{targetId}", name="checkChannel")
     */
    public function newChannel(Request $request, $targetId, ChannelRepository $channelRepository, UserRepository $userRepository, EntityManagerInterface $em)
    {
        // Recup id de l'user
        $User = $this->getUser();
        $UserId = $User->getId();

        dump($UserId);
 
        // Recup de l'id de celui a qui il veut parler
        dump($targetId);
        
        // Verif que channel pas deja existant
        $recup = $channelRepository->findChannelByUsers($UserId, $targetId);
        
        dump($recup);
        
        
        // Si déjà existant renvoyer à la page de chat dans la bonne conv
        if ($recup != null) {
            // dd("channel déjà existant");
            return $this->redirectToRoute('chatHub');
        }

        // Sinon, creation nouveau channel avec ces deux users

        else {

        $targetUser = $userRepository->find($targetId);
        
        $channel = new Channel;

        $channel->addUser($User);
        $channel->addUser($targetUser);

        // dd($channel);

        $em->persist($channel);
        $em->flush();

        }

        return $this->redirectToRoute('chatHub');
    }


    /**
     * @Route("/chat", name="chatHub")
     */
    public function getChannels(ChannelRepository $channelRepository, UserRepository $userRepository)
    {

        $userChannels = $this->getUser()->getChannels();

        // $values = $userChannels->getValues();

        // dd($values);

        return $this->render('channel/chat.html.twig', [
            'channels' => $userChannels
        ]);
    }
}
