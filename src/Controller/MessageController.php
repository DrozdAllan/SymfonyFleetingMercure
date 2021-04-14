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

class MessageController extends AbstractController
{
    /**
     * @Route("/message", name="message", methods={"POST"})
     */
    public function sendMessage(Request $request, ChannelRepository $channelRepository, PublisherInterface $publisher, EntityManagerInterface $em)
    {
        //recup data POST
        $dataJSON = $request->getContent();

        $dataArray = json_decode($dataJSON, true); //decodage du JSON en array

        $msgContent = htmlentities($dataArray['content']);

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

        // dump($message);

        // $em->persist($message);
        // $em->flush();

        $receivedContent = htmlentities($dataArray['content']);
        $receivedFrom = htmlentities($dataArray['from']);
        $receivedChannel = htmlentities($dataArray['channel']);

        $receivedChannelToStr = strval($receivedChannel);

        $jsonToWebsocket = json_encode(['content' => $receivedContent, 'from'=> $receivedFrom, 'channel' => $receivedChannel]);

        /**
         * 
         * 
         * todo: essayer le serializer ou bien essayer l'authentification
         * 
         */

        $update = new Update(
            $receivedChannelToStr,
            $jsonToWebsocket
        );

        dump($update);

        $publisher($update);

        return new JsonResponse(
            $jsonToWebsocket,
            Response::HTTP_OK,
            [],
            true
        );
    }
}
