<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commande;
use App\Entity\Donation;
use App\Entity\ReminderCommande;
use App\Service\ReminderService;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    private $em;
    private $reminderService;

    public function __construct(EntityManagerInterface $em, ReminderService $reminderService)
    {
        $this->em = $em;
        $this->reminderService = $reminderService;
    }

    /**
     * 📊 Dashboard admin - Vue d'ensemble
     * 
     * @Route("/dashboard", name="dashboard", methods={"GET"})
     */
    public function dashboard(): Response
    {
        $commandesRepo = $this->em->getRepository(Commande::class);
        $donationsRepo = $this->em->getRepository(Donation::class);

        // Statistiques
        $stats = [
            'totalCommandes' => $commandesRepo->count([]),
            'totalDonations' => $donationsRepo->count([]),
            'totalMontant' => $donationsRepo->getTotalMontant(),
            'donationsAnonymes' => $donationsRepo->countAnonymes(),
            'cadeauxEnAttente' => $donationsRepo->countCadeauxEnAttente(),
            'remindersNonEnvoyes' => $this->em->getRepository(ReminderCommande::class)
                ->count(['statut' => 'brouillon']),
        ];

        // Dernières donations
        $donations = $donationsRepo->findBy([], ['dateDonation' => 'DESC'], 5);

        // Reminders à traiter
        $reminders = $this->em->getRepository(ReminderCommande::class)
            ->findBy(['statut' => 'brouillon'], ['dateCreation' => 'DESC'], 10);

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'donations' => $donations,
            'reminders' => $reminders,
        ]);
    }

    /**
     * 💰 Gestion des donations
     * 
     * @Route("/donations", name="donations_list", methods={"GET"})
     */
    public function donationsList(Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $perPage = 20;

        $donationsRepo = $this->em->getRepository(Donation::class);

        // Filtres
        $filters = [
            'anonyme' => $request->query->get('anonyme') === '1',
            'eligible_cadeau' => $request->query->get('eligible') === '1',
            'statut' => $request->query->get('statut') ?? 'tous',
        ];

        // Requête
        $qb = $donationsRepo->createQueryBuilder('d')
            ->orderBy('d.dateDonation', 'DESC');

        if ($filters['anonyme']) {
            $qb->andWhere('d.nomDonateur IS NULL');
        }

        if ($filters['eligible_cadeau']) {
            $qb->andWhere('d.eligibleCadeau = true');
        }

        if ($filters['statut'] !== 'tous') {
            $qb->andWhere('d.statutSuivi = :statut')
                ->setParameter('statut', $filters['statut']);
        }

        $total = count($qb->getQuery()->getResult());
        $donations = $qb
            ->setMaxResults($perPage)
            ->setFirstResult(($page - 1) * $perPage)
            ->getQuery()
            ->getResult();

        return $this->render('admin/donations_list.html.twig', [
            'donations' => $donations,
            'page' => $page,
            'totalPages' => ceil($total / $perPage),
            'total' => $total,
            'filters' => $filters,
        ]);
    }

    /**
     * 🔍 Détail d'une donation
     * 
     * @Route("/donation/{id}", name="donation_detail", methods={"GET"})
     */
    public function donationDetail(Donation $donation): Response
    {
        $commande = $donation->getCommande();
        $reminders = $commande->getReminders();

        return $this->render('admin/donation_detail.html.twig', [
            'donation' => $donation,
            'commande' => $commande,
            'reminders' => $reminders,
        ]);
    }

    /**
     * 📋 Gestion des commandes
     * 
     * @Route("/commandes", name="commandes_list", methods={"GET"})
     */
    public function commandesList(Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $perPage = 15;

        $commandesRepo = $this->em->getRepository(Commande::class);

        // Filtres
        $statut = $request->query->get('statut') ?? 'tous';
        $type = $request->query->get('type') ?? 'tous';

        $qb = $commandesRepo->createQueryBuilder('c')
            ->leftJoin('c.reminders', 'r')
            ->leftJoin('c.donations', 'd')
            ->addSelect('r', 'd')
            ->orderBy('c.dateCreation', 'DESC');

        if ($statut !== 'tous') {
            $qb->andWhere('c.statut = :statut')
                ->setParameter('statut', $statut);
        }

        if ($type !== 'tous') {
            $qb->andWhere('c.typeProjet = :type')
                ->setParameter('type', $type);
        }

        $total = count($qb->getQuery()->getResult());
        $commandes = $qb
            ->setMaxResults($perPage)
            ->setFirstResult(($page - 1) * $perPage)
            ->getQuery()
            ->getResult();

        return $this->render('admin/commandes_list.html.twig', [
            'commandes' => $commandes,
            'page' => $page,
            'totalPages' => ceil($total / $perPage),
            'statut' => $statut,
            'type' => $type,
        ]);
    }

    /**
     * 📝 Gestion des reminders
     * 
     * @Route("/reminders", name="reminders_list", methods={"GET"})
     */
    public function remindersList(Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $perPage = 20;

        $remindersRepo = $this->em->getRepository(ReminderCommande::class);

        $statut = $request->query->get('statut') ?? 'brouillon';

        $qb = $remindersRepo->createQueryBuilder('r')
            ->leftJoin('r.commande', 'c')
            ->addSelect('c')
            ->where('r.statut = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('r.dateCreation', 'DESC');

        $total = count($qb->getQuery()->getResult());
        $reminders = $qb
            ->setMaxResults($perPage)
            ->setFirstResult(($page - 1) * $perPage)
            ->getQuery()
            ->getResult();

        return $this->render('admin/reminders_list.html.twig', [
            'reminders' => $reminders,
            'page' => $page,
            'totalPages' => ceil($total / $perPage),
            'statut' => $statut,
        ]);
    }

    /**
     * ✉️ Envoyer un reminder
     * 
     * @Route("/reminder/{id}/send", name="reminder_send", methods={"POST"})
     */
    public function sendReminder(ReminderCommande $reminder, Request $request): Response
    {
        try {
            // Récupérer l'email du donateur
            $commande = $reminder->getCommande();
            $donations = $commande->getDonations();
            
            if ($donations->isEmpty()) {
                return new JsonResponse(['error' => 'Pas de donation liée'], 400);
            }

            $donation = $donations->first();
            
            if ($donation->isAnonyme()) {
                // Email non disponible pour donation anonyme
                $reminder->setStatut('non_applicable');
            } else {
                // Envoyer le reminder
                $this->reminderService->sendReminder($reminder, $donation->getEmailDonateur());
                $reminder->marquerEnvoye($donation->getEmailDonateur());
            }

            $this->em->flush();

            $this->addFlash('success', 'Reminder traité avec succès');
            return $this->redirectToRoute('admin_reminders_list');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur: ' . $e->getMessage());
            return $this->redirectToRoute('admin_reminder_detail', ['id' => $reminder->getId()]);
        }
    }

    /**
     * 🎁 Assigner un cadeau à une donation
     * 
     * @Route("/donation/{id}/assign-cadeau", name="donation_assign_cadeau", methods={"POST"})
     */
    public function assignCadeau(Donation $donation, Request $request): Response
    {
        try {
            if (!$donation->isEligibleCadeau()) {
                return new JsonResponse(['error' => 'Donation ineligible'], 400);
            }

            $cadeauId = (int) $request->request->get('cadeau_id');
            $donation->setCadeauId($cadeauId);

            // Créer reminder de preparation
            $reminder = new ReminderCommande();
            $reminder->setCommande($donation->getCommande());
            $reminder->setTypeReminder('preparation');
            $reminder->setMessage('Cadeau ' . $cadeauId . ' assigné à la donation');

            $this->em->persist($reminder);
            $this->em->flush();

            $this->addFlash('success', 'Cadeau assigné');
            return $this->redirectToRoute('admin_donation_detail', ['id' => $donation->getId()]);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('admin_donation_detail', ['id' => $donation->getId()]);
        }
    }

    /**
     * 📬 Marquer cadeau comme envoyé
     * 
     * @Route("/donation/{id}/cadeau-envoye", name="donation_cadeau_envoye", methods={"POST"})
     */
    public function marquerCadeauEnvoye(Donation $donation): Response
    {
        try {
            $donation->setCadeauEnvoye(new \DateTime());

            // Reminder: cadeau envoyé
            $reminder = new ReminderCommande();
            $reminder->setCommande($donation->getCommande());
            $reminder->setTypeReminder('envoye');
            $reminder->setMessage('Cadeau envoyé le ' . date('d/m/Y H:i'));

            $this->em->persist($reminder);
            $this->em->flush();

            $this->addFlash('success', 'Cadeau marqué comme envoyé');
            return $this->redirectToRoute('admin_donation_detail', ['id' => $donation->getId()]);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('admin_donation_detail', ['id' => $donation->getId()]);
        }
    }

    /**
     * 📊 Rapport IP (sécurité)
     * 
     * @Route("/security/ips", name="security_ips", methods={"GET"})
     */
    public function securityIPs(Request $request): Response
    {
        $donationsRepo = $this->em->getRepository(Donation::class);
        
        // IPs suspectes (multiples donations différentes, même IP)
        $allDonations = $donationsRepo->findAll();
        $ipPattern = [];

        foreach ($allDonations as $donation) {
            $ip = $donation->getIpAdresse();
            if (!isset($ipPattern[$ip])) {
                $ipPattern[$ip] = [];
            }
            $ipPattern[$ip][] = $donation;
        }

        // Filtrer les IPs avec plus de 2 donations
        $suspiciousIPs = array_filter($ipPattern, function($donations) {
            return count($donations) > 2;
        });

        return $this->render('admin/security_ips.html.twig', [
            'allIPs' => $ipPattern,
            'suspiciousIPs' => $suspiciousIPs,
        ]);
    }

    /**
     * 📥 Suivi anonyme - Interface publique
     * 
     * @Route("/track/{codeSuivi}", name="track_donation_public")
     */
    public function trackDonation(String $codeSuivi): Response
    {
        $donation = $this->em->getRepository(Donation::class)
            ->findOneBy(['codeSuivi' => $codeSuivi]);

        if (!$donation) {
            throw $this->createNotFoundException('Code de suivi introuvable');
        }

        return $this->render('admin/track_donation.html.twig', [
            'donation' => $donation,
            'commande' => $donation->getCommande(),
            'reminders' => $donation->getCommande()->getReminders(),
        ]);
    }
}
