<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function add(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function search(UserInterface $participant, ?array $search ){

            $qb = $this->createQueryBuilder('s');
            $qb->select('s');
            $etatRepository = $this->getEntityManager()->getRepository(Etat::class);
            $ouverte = $etatRepository->findOneBy(['libelle' => 'Ouvert']);
            $creee = $etatRepository->findOneBy(['libelle' => 'En création']);
            $fermee = $etatRepository->findOneBy(['libelle' => 'Fermé']);
            $termine = $etatRepository->findOneBy(['libelle' => 'Terminé']);

            $qb->andWhere('
            s.etat = :ouverte OR s.etat = :fermee OR s.etat = :termine
            OR (s.etat  = :creee AND s.organisateur= :participant) 
        ')
                ->setParameter('ouverte', $ouverte)
                ->setParameter('fermee', $fermee)
                ->setParameter('participant', $participant)
                ->setParameter('creee', $creee)
                ->setParameter('termine', $termine);

            $qb->leftJoin('s.organisateur', 'o')
                ->addSelect('o')
                ->leftJoin('s.etat', 'e')
                ->addSelect('e');

            $qb->orderBy('s.dateHeureDebut', 'ASC');

            if ($search['campus'] != null) {
                $qb->andWhere('s.campus = :campus')
                    ->setParameter('campus', $search['campus']);
                $qb->leftJoin('s.campus', 'c')
                    ->addSelect('c');
            }

            if ($search['recherche'] != null) {
                $qb->andWhere('s.nom LIKE :search')
                    ->setParameter('search', $search['recherche'] . '%');
            }

            if ($search['date_minimum'] != null && $search['date_max'] != null) {
                $qb->andWhere('s.dateHeureDebut >= :dateDebut')
                    ->setParameter('dateDebut', $search['date_minimum']);

                $qb->andWhere('s.dateHeureDebut <= :dateFin')
                    ->setParameter('dateFin', $search['date_max']);
            }

            if ($search['organisateur'] != null) {
                $qb->andWhere('s.organisateur = :organisateur')
                    ->setParameter('organisateur', $participant);

            }

            if (!empty($search['inscrit']) and empty($search['not_inscrit'])){
                $qb->andWhere(':inscrit MEMBER OF s.participants')
                    ->setParameter('inscrit', $participant);
            }

            if (!empty($search['not_inscrit']) and empty($search['inscrit'])){
                $qb->andWhere(':nonInscrit NOT MEMBER OF s.participants')
                    ->setParameter('nonInscrit', $participant);
            }

            if (!empty($search['passe'])){
                $qb->andWhere("e.libelle = 'Terminé'");
            }
            return $qb->getQuery()->getResult();
    }

    public function displayByDefault(UserInterface $participant ){

        $qb = $this->createQueryBuilder('s');
        $qb->select('s');
        $etatRepository = $this->getEntityManager()->getRepository(Etat::class);
        $ouverte = $etatRepository->findOneBy(['libelle' => 'Ouvert']);
        $creee = $etatRepository->findOneBy(['libelle' => 'En création']);
        $fermee = $etatRepository->findOneBy(['libelle' => 'Fermé']);

        $qb->andWhere('
            s.etat = :ouverte OR s.etat = :fermee 
            OR (s.etat  = :creee AND s.organisateur= :participant) 
        ')
            ->setParameter('ouverte', $ouverte)
            ->setParameter('fermee', $fermee)
            ->setParameter('participant', $participant)
            ->setParameter('creee', $creee);

        $qb->leftJoin('s.organisateur', 'o')
            ->addSelect('o')
            ->leftJoin('s.etat', 'e')
            ->addSelect('e');

        $qb->andWhere('s.campus = :campus')
            ->setParameter('campus', $participant->getCampus());
        $qb->leftJoin('s.campus', 'c')
            ->addSelect('c');

        $qb->orderBy('s.dateHeureDebut', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
