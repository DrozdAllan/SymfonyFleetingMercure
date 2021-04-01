<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\ChannelRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class MessageController extends AbstractController
{
    /**
     * @Route("/message", name="message", methods={"POST"})
     */
    public function sendMessage(Request $request, ChannelRepository $channelRepository, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        //recup data POST
        $dataJSON = $request->getContent();

        $dataArray = json_decode($dataJSON, true); //decodage du JSON en array

        $msgContent = $dataArray['content'];

        if (empty($msgContent)) {
            throw new AccessDeniedHttpException('no data sent');
        }

        $channel = $channelRepository->findOneBy([
            'id' => $dataArray['channel'] // On cherche à savoir de quel channel provient le message
        ]);

        $message = new Message();
        $message->setContent($msgContent);
        $message->setChannel($channel);
        $message->setUser($this->getUser());
        $message->setCreatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')));

        // dd($message);

        $em->persist($message);
        $em->flush();


        $jsonMessage = $serializer->serialize($message, 'json', [
            'groups' => ['message']
        ]);

        return new JsonResponse(
            $jsonMessage,
            Response::HTTP_OK,
            [],
            true
        );
    }
}
