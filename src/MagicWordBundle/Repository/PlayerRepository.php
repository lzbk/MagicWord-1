<?php

namespace MagicWordBundle\Repository;

/**
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlayerRepository extends \Doctrine\ORM\EntityRepository
{
    public function countByGame($game)
    {
        $dql = 'SELECT DISTINCT count(p.id) FROM MagicWordBundle:Player p
                WHERE EXISTS(
                    SELECT a FROM MagicWordBundle:Activity a
                    LEFT JOIN a.round ar
                    LEFT JOIN ar.game g
                    WHERE a.player = p
                    AND g = :game
                )
        ';

        $query = $this->_em->createQuery($dql);
        $query->setParameter('game', $game);

        return $query->getSingleScalarResult();
    }

    public function getCandidates($currentUser)
    {
        $excluded = $this->getExcludedForChallenge($currentUser);
        $qb = $this->createQueryBuilder('p');
        $qb->where($qb->expr()->notIn('p', ':excluded'))
            ->setParameter('excluded', $excluded);

        return $qb;
    }

    public function getExcludedForChallenge($currentUser)
    {
        $dql = "SELECT p FROM MagicWordBundle:Player p
                LEFT JOIN p.startedGames started
                WHERE (
                    started INSTANCE OF 'MagicWordBundle\Entity\GameType\Challenge'
                    AND started.author = :currentUser
                ) OR p = :currentUser
        ";
        $query = $this->_em->createQuery($dql);
        $query->setParameter('currentUser', $currentUser);

        return $query->getResult();
    }
}
