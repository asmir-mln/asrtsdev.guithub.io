<?php
/* AsArt'sDev | ClientRepository | Doctrine Queries | Signature invisible | ASmir Milia */

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * Trouver les clients actifs
     */
    public function findActifs()
    {
        return $this->createQueryBuilder('c')
            ->where('c.statut = :statut')
            ->setParameter('statut', 'actif')
            ->orderBy('c.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver par type
     */
    public function findByType(string $type)
    {
        return $this->createQueryBuilder('c')
            ->where('c.typeClient = :type')
            ->setParameter('type', $type)
            ->orderBy('c.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver récemment enregistrés (7 derniers jours)
     */
    public function findRecent()
    {
        $date = new \DateTime('-7 days');
        return $this->createQueryBuilder('c')
            ->where('c.dateInscription >= :date')
            ->setParameter('date', $date)
            ->orderBy('c.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche par mot-clé
     */
    public function search(string $keyword)
    {
        return $this->createQueryBuilder('c')
            ->where('c.nom LIKE :keyword')
            ->orWhere('c.prenom LIKE :keyword')
            ->orWhere('c.email LIKE :keyword')
            ->orWhere('c.entreprise LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->orderBy('c.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Clients avec documents
     */
    public function findWithDocuments()
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.documents', 'd')
            ->addSelect('d')
            ->where('d.id IS NOT NULL')
            ->orderBy('d.dateUpload', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Statistiques globales
     */
    public function getStatistiques(): array
    {
        $result = $this->createQueryBuilder('c')
            ->select('
                COUNT(c.id) as totalClients,
                SUM(CASE WHEN c.statut = \'actif\' THEN 1 ELSE 0 END) as clientsActifs,
                SUM(CASE WHEN c.typeClient = \'entreprise\' THEN 1 ELSE 0 END) as entreprises
            ')
            ->getQuery()
            ->getOneOrNullResult();

        return $result ?: [];
    }
}
