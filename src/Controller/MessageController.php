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
use Symfony\Component\Messenger\MessageBusInterface;

class MessageController extends AbstractController
{
    /**
     * @Route("/message", name="message", methods={"POST"})
     */
    public function sendMessage(Request $request, ChannelRepository $channelRepository, SerializerInterface $serializer, PublisherInterface $publisher, MessageBusInterface $bus)
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

        /**
         * 
         *     BUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUG A CE NIVEAU : CEST AU MOMENT DE UPDATE QUE CA MERDE
         *  Les infos "data" ($jsonMessage) créées dans l'update existent bien mais les array user et channel semblent vides
         *  le serialize 'groups' pue la merde il rend user et channel en empty array
         * 
         * Le insomnia contient le bon format d'info pour afficher l'update correctement dans le tchat, surement possible d'enlever Id et Channel de l'update
         * 
         */
        $update = new Update(
            'ping',
            json_encode(['content'=>'bonjour','from'=>'Benjam','channel'=>'1'])
        );

        // dd($update);
        dump($update);

        $bus->dispatch($update);

        dd($bus);

        $publisher($update);
        
        dd($publisher);

        return new JsonResponse(
            $jsonMessage,
            Response::HTTP_OK,
            [],
            true
        );
    }
}
