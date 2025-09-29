<?php

namespace App\DataFixtures;

use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\Module;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadInstructeurs($manager);
        $this->loadEtudiants($manager);
        $this->loadFormations($manager);
        $this->loadModules($manager);
        $this->loadInscriptions($manager);
    }

    private function loadInstructeurs(ObjectManager $manager): void
    {
        for($i = 1; $i <= 5; $i++) {
            $instructeur = new User();
            $instructeur->setEmail("instructeur$i@mail.dev");
            $instructeur->setRoles(['ROLE_INSTRUCTEUR']);
            $instructeur->setPassword($this->hasher->hashPassword($instructeur, 'instructeur'));
            $instructeur->setNomAffichage('Instructeur ' . $i);
            $manager->persist($instructeur);
        }

        $manager->flush();
    }

    private function loadEtudiants(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $etudiant = new User();
            $etudiant->setEmail("etudiant$i@mail.dev");
            $etudiant->setRoles(['ROLE_ETUDIANT']);
            $etudiant->setPassword($this->hasher->hashPassword($etudiant, 'etudiant'));
            $etudiant->setNomAffichage('Etudiant ' . $i);
            $manager->persist($etudiant);
        }

        $manager->flush();
    }

    private function loadFormations(ObjectManager $manager): void
    {
        $data = [
            [
                'titre' => 'Formation Symfony',
                'description' => 'Apprenez à créer des applications web avec Symfony.',
                'dureeHeures' => 40,
                'niveauDifficulte' => 'Intermédiaire',
                'prix' => 1500.00
            ],
            [
                'titre' => 'Formation React',
                'description' => 'Maîtrisez le développement d\'applications front-end avec React.',
                'dureeHeures' => 30,
                'niveauDifficulte' => 'Débutant',
                'prix' => 1200.00
            ],
            [
                'titre' => 'Formation Docker',
                'description' => 'Découvrez comment containeriser vos applications avec Docker.',
                'dureeHeures' => 20,
                'niveauDifficulte' => 'Avancé',
                'prix' => 1000.00
            ]
        ];

        foreach ($data as $item) {
            $formation = new Formation();
            $formation->setTitre($item['titre']);
            $formation->setDescription($item['description']);
            $formation->setDureeHeures($item['dureeHeures']);
            $formation->setNiveauDifficulte($item['niveauDifficulte']);
            $formation->setPrix($item['prix']);
            // Associer un instructeur aléatoire
            $instructeurs = $manager->getRepository(User::class)->findByRole("ROLE_INSTRUCTEUR");
            $instructeur = $instructeurs[array_rand($instructeurs)] ?? null;
            if ($instructeur) {
                $formation->addInstructeur($instructeur);
            }

            $manager->persist($formation);
        }
        $manager->flush();
    }

    private function loadModules(ObjectManager $manager): void
    {
        $data = [
            'Formation Symfony' => [
                [
                    'titre' => 'Introduction à Symfony',
                    'description' => 'Présentation du framework Symfony et de ses concepts clés.',
                    'ordre' => 1,
                    'dureeEstimee' => 5
                ],
                [
                    'titre' => 'Les bases de Symfony',
                    'description' => 'Apprenez à créer des routes, des contrôleurs et des vues avec Twig.',
                    'ordre' => 2,
                    'dureeEstimee' => 10
                ]
            ],
            'Formation React' => [
                [
                    'titre' => 'Introduction à React',
                    'description' => 'Présentation de la bibliothèque React et de ses concepts clés.',
                    'ordre' => 1,
                    'dureeEstimee' => 4
                ],
                [
                    'titre' => 'Composants et Props',
                    'description' => 'Apprenez à créer des composants réutilisables avec des props.',
                    'ordre' => 2,
                    'dureeEstimee' => 8
                ]
            ],
            'Formation Docker' => [
                [
                    'titre' => 'Introduction à Docker',
                    'description' => 'Présentation de Docker et de ses concepts clés.',
                    'ordre' => 1,
                    'dureeEstimee' => 3
                ],
                [
                    'titre' => 'Création de conteneurs',
                    'description' => 'Apprenez à créer et gérer des conteneurs Docker.',
                    'ordre' => 2,
                    'dureeEstimee' => 7
                ]
            ]
        ];

        foreach ($data as $item => $modules) {
            $formation = $manager->getRepository(Formation::class)->findOneBy(['titre' => $item]);
            if ($formation) {
                foreach ($modules as $moduleData) {
                    $module = new Module();
                    $module->setTitre($moduleData['titre']);
                    $module->setDescription($moduleData['description']);
                    $module->setOrdre($moduleData['ordre']);
                    $module->setDureeEstimee($moduleData['dureeEstimee']);
                    $module->setFormation($formation);
                    $manager->persist($module);
                }
            }
        }
        $manager->flush();
    }

    private function loadInscriptions(ObjectManager $manager): void
    {
        $etudiants = $manager->getRepository(User::class)->findByRole("ROLE_ETUDIANT");
        $formations = $manager->getRepository(Formation::class)->findAll();

        foreach ($etudiants as $etudiant) {
            // Chaque étudiant s'inscrit à 1 à 3 formations aléatoires
            $nbInscriptions = rand(1, 3);
            $formationsChoisies = (array)array_rand($formations, $nbInscriptions);
            foreach ($formationsChoisies as $index) {
                $formation = $formations[$index];
                $inscription = new Inscription();
                $inscription->setApprenant($etudiant);
                $inscription->setFormation($formation);
                $manager->persist($inscription);
            }
        }

        $manager->flush();
    }
}
