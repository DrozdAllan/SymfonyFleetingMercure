<?php

namespace App\Controller;

use App\Form\AdminAnnouncerType;
use App\Repository\UserRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGenerator;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function home(UserRepository $userRepository)
    {
        $adminqueue = count($userRepository->findBy(['Announcer' => '1', 'validadmin' => null]));

        return $this->render('admin/index.html.twig', [
            'adminqueue' => $adminqueue
        ]);
    }

    /**
     * @Route("/admin/announcers", name="adminAnnouncers")
     */
    public function adminAnnouncers(UserRepository $userRepository)
    {

        $criteria = ['Announcer' => '1', 'validadmin' => null];

        $waitingAnnouncers = $userRepository->findBy($criteria, ['id' => 'ASC']);


        return $this->render('admin/adminAnnouncers.html.twig', [
            'waitingAnnouncers' => $waitingAnnouncers
        ]);
    }

    /**
     * @Route("/admin/announcer-validate/{id}", name="announcerValidate")
     */
    public function announcerValidate($id, UserRepository $userRepository, EntityManagerInterface $em)
    {
        //1 Récupérer l'id validé
        $announcer = $userRepository->find($id);
        //2 Changer le statut validadmin de null à 1 avec l'entity manager
        $announcer->setValidadmin('1');
        $em->flush();
        //3 Retour à la même page
        return $this->redirectToRoute('adminAnnouncers');
    }

    /**
     * @Route("/admin/announcer-modify/{id}", name="announcerModify")
     */
    public function announcerModify($id, UserRepository $userRepository, Request $request, EntityManagerInterface $em)
    {
        //1 Récupérer l'id de l'annonceur à modifier
        $announcer = $userRepository->find($id);

        //2 Renvoyer les champs pour l'admin dans un nouveau form avec handlerequest
        $form = $this->createForm(AdminAnnouncerType::class, $announcer);
        $form->handleRequest($request);

        //3 Action du submit
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('adminAnnouncers');
        }

        //4 Renvoi au formulaire car render toujours à la fin 
        $formView = $form->createView();
        return $this->render('admin/announcerModify.html.twig', [
            'announcer' => $announcer,
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/deleteimage/{imageid}", name="adminDeleteImage")
     * Apparemment pas safe mais ballec
     */
    public function adminDeleteImage($imageid, Request $request, ImageRepository $imageRepository, EntityManagerInterface $em)
    {
        $image = $imageRepository->findOneBy(['id' => $imageid]);


        if ($image) {
            $announcerId = $image->getUser()->getId();
            // dump($imageid);
            // 13 image id

            // dump($announcerId);
            // 14 announcer id 

            $urlBase = $this->generateUrl('announcerModify', ['id' => $announcerId]);

            // dd($urlBase);


            // Supprimer image du dossier Public/Uploads
            $nom = $image->getImageFilename();

            $filePath = "uploads\\images\\" . $nom;
            // dd($filePath);
            unlink($filePath);

            // Supprimer image de la DB
            $em->remove($image);
            $em->flush();
            // $this->addFlash("success", "Image ajoutée avec succès");

            //redirection vers le basePath
            return $this->redirect($urlBase);
        }

    }
}
