<?php 


// namespace App\Service;

// use Symfony\Component\HttpFoundation\File\Exception\FileException;
// use Symfony\Component\HttpFoundation\File\UploadedFile;
// use Symfony\Component\String\Slugger\SluggerInterface;

// class ResearchAnnouncer
// {
//     private $targetDirectory;
//     private $slugger;

//     public function __construct($targetDirectory, SluggerInterface $slugger)
//     {
//         $this->targetDirectory = $targetDirectory;
//         $this->slugger = $slugger;
//     }

//     public function upload(UploadedFile $file)
//     {
//         $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
//         $safeFilename = $this->slugger->slug($originalFilename);
//         $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

//         try {
//             $file->move($this->getTargetDirectory(), $fileName);
//         } catch (FileException $e) {
//             // ... handle exception if something happens during file upload
//         }

//         return $fileName;
//     }

//     //1 Entrer le nom d'utilisateur dans un form
//     $form = $this->createForm(ResearchFormType::class);
//     // $form->handleRequest($request);

//     //2 Action du submit
//     if ($form->isSubmitted() && $form->isValid()) {
//         return $this->redirectToRoute('adminAnnouncers');
//     }

//     //4 Renvoi au formulaire car render toujours à la fin 
//     $formView = $form->createView();
//     return $this->render('admin/announcerModify.html.twig', [
//         'formView' => $formView
//     ]);

// }
