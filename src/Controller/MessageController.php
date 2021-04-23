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
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MessageController extends AbstractController
{
    /**
     * @Route("/message", name="message", methods={"POST"})
     */
    public function sendMessage(Request $request, ChannelRepository $channelRepository, NormalizerInterface $normalizer, SerializerInterface $serializer, PublisherInterface $publisher, EntityManagerInterface $em)
    {

        $data = json_decode($request->getContent()); //decodage du JSON en objet (add parameter true pour avoir en array)

        $receivedContent = htmlentities($data->content);

        if (empty($receivedContent)) {
            throw new AccessDeniedHttpException('no data sent');
        }

        $channel = $channelRepository->findOneBy([
            'id' => $data->channel // On cherche à savoir de quel channel provient le message
        ]);

        $message = new Message();
        $message->setContent($receivedContent);
        $message->setChannel($channel);
        $message->setUser($this->getUser());
        $message->setCreatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')));

        $em->persist($message);
        $em->flush();

        $jsonMessage = $serializer->serialize($message, 'json', [
            'groups' => ['chatmessage']
        ]);

        $publisher(new Update(
            ["$data->channel"],
            $jsonMessage
        ));

        return new JsonResponse(
            $jsonMessage,
            Response::HTTP_OK,
            [],
            true
        );
    }
}
