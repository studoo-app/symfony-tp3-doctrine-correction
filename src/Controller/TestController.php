<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\Module;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/test')]
final class TestController extends AbstractController
{
    #[Route('/affichage', name: 'app_test_affichage')]
    public function affichage(EntityManagerInterface $em): Response
    {
        $formations = $em->getRepository(Formation::class)->findAll();
        $inscriptions = $em->getRepository(Inscription::class)->findAll();


        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
            'formations' => $formations,
            'inscriptions' => $inscriptions,
        ]);
    }

    #[Route('/creation', name: 'app_test_creation')]
    public function creation(UserPasswordHasherInterface $hasher, EntityManagerInterface $em): JsonResponse
    {
        $instructeur = new User();
        $instructeur->setEmail('instructeur@mail.dev');
        $instructeur->setRoles(['ROLE_INSTRUCTEUR']);
        $instructeur->setPassword($hasher->hashPassword($instructeur, 'instructeur'));
        $instructeur->setNomAffichage('Jean Bon');

        $em->persist($instructeur);

        $etudiant = new User();
        $etudiant->setEmail('etudiant@mail.dev');
        $etudiant->setRoles(['ROLE_ETUDIANT']);
        $etudiant->setPassword($hasher->hashPassword($etudiant, 'etudiant'));
        $etudiant->setNomAffichage('Paul Dupont');

        $em->persist($etudiant);




        $formation = new Formation();
        $formation->setTitre('Formation Symfony');
        $formation->setDescription('Apprenez à créer des applications web avec Symfony.');
        $formation->setDureeHeures(40);
        $formation->setNiveauDifficulte("Intermédiaire");
        $formation->setPrix(1500.00);
        $formation->addInstructeur($instructeur);
        $em->persist($formation);

        $module1 = new Module();
        $module1->setTitre('Introduction à Symfony');
        $module1->setDescription('Présentation du framework Symfony et de ses concepts clés.');
        $module1->setOrdre(1);
        $module1->setDureeEstimee(5);
        $module1->setFormation($formation);
        $em->persist($module1);

        $module2 = new Module();
        $module2->setTitre('Les bases de Symfony');
        $module2->setDescription('Apprenez à créer des routes, des contrôleurs et des vues avec Twig.');
        $module2->setOrdre(2);
        $module2->setDureeEstimee(10);
        $module2->setFormation($formation);
        $em->persist($module2);

        $inscription = new Inscription();
        $inscription->setApprenant($etudiant);
        $inscription->setFormation($formation);
        $em->persist($inscription);

        $em->flush();

        return new JsonResponse(['status' => 'ok']);
    }
}
