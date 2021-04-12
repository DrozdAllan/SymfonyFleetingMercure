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
    public function sendMessage(Request $request, ChannelRepository $channelRepository, SerializerInterface $serializer, PublisherInterface $publisher)
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

        // dump($message);

        // $em->persist($message);
        // $em->flush();


        $jsonMessage = $serializer->serialize($message, 'json', [
            'groups' => ['message']
        ]);

        dump($jsonMessage);

        $receivedContent = $dataArray['content'];
        $receivedFrom = $dataArray['from'];
        $receivedChannel = $dataArray['channel'];

        $messagequimarche = json_encode(['content' => $receivedContent, 'from'=> $receivedFrom, 'channel' => $receivedChannel]);

        /**
         * 
         *     BUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUG A CE NIVEAU : CEST AU MOMENT DE UPDATE QUE CA MERDE
         *  Les infos "data" ($jsonMessage) créées dans l'update existent bien mais les array user et channel semblent vides
         *  le serialize 'groups' pue la merde il rend user et channel en empty array
         * 
         * todo: si je n'arrive pas, virer le groups et recréer les infos à renvoyer dans l'update avec le controller et les repository
         * 
         */
        $update = new Update(
            'ping',
            // $jsonMessage
            $messagequimarche
        );

        // dd($update);
        dump($update);

        $publisher($update);

        return new JsonResponse(
            $jsonMessage,
            Response::HTTP_OK,
            [],
            true
        );
    }
}
