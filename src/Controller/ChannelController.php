<?php

namespace App\Controller;

use App\Entity\Channel;
use App\Entity\User;
use App\Repository\ChannelRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ChannelController extends AbstractController
{
    /**
     * @Route("/chat", name="chat")
     */
    public function getChannels(ChannelRepository $channelRepository): Response
    {
        $channels = $channelRepository->findAll();


        return $this->render('channel/index.html.twig', [
            'channels' => $channels ?? []
        ]);
    }


    /**
     * @Route("/chat/new/{targetId}", name="newChannel")
     */
    public function newChannel(Request $request, $targetId, UserRepository $userRepository, ChannelRepository $channelRepository)
    {
        // Recup id de l'user
        $User = $this->getUser();

        dump($User);

        $UserId = $User->getId();
        dump($UserId);

        $Chacha = $User->getChannels();
        dump($Chacha);



        // Recup de l'id de celui a qui il veut parler
        dd($targetId);
    
        // Verif que channel pas deja existant
        $channelRepository->find();

        // Sinon, creation nouveau channel avec ces deux users




        return $this->redirectToRoute('home');
    }
}
