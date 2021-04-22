<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Channel;
use App\Repository\UserRepository;
use Symfony\Component\WebLink\Link;
use App\Repository\ChannelRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChannelController extends AbstractController
{
    /**
     * @Route("/chat/new/{targetId}", name="checkChannel")
     */
    public function newChannel($targetId, ChannelRepository $channelRepository, UserRepository $userRepository, EntityManagerInterface $em)
    {
        // Recup id de l'user
        $User = $this->getUser();
        $UserId = $User->getId();
 
        // Recup de l'id de celui a qui il veut parler
        // dump($targetId);
        
        // Verif que channel pas deja existant
        $recup = $channelRepository->findChannelByUsers($UserId, $targetId);
        
        
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
    public function getChannels(Request $request)
    {

        $userChannels = $this->getUser()->getChannels();

        $hubUrl = $this->getParameter('mercure.default_hub'); // Recup de l'url du hub mercure à envoyer en Link dans le header

        $this->addLink($request, new Link('mercure', $hubUrl)); // Utilisation de WebLink component pour créer le Link et envoie auto dans le render

        
        return $this->render('channel/chat.html.twig', [
            'channels' => $userChannels
        ]);
    }
}
