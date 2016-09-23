<?php

namespace MagicWordBundle\Repository;

/**
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlayerRepository extends \Doctrine\ORM\EntityRepository
{
    public function getBestForm($user)
    {
        $dql = 'SELECT f FROM MagicWordBundle:FoundableForm f
                WHERE EXISTS(
                    SELECT a FROM MagicWordBundle:Activity a
                    LEFT JOIN a.round ar
                    LEFT JOIN ar.game g
                    WHERE a.player = :user
                    AND g.language = :language
                ) ORDER BY f.points DESC';

        $query = $this->_em->createQuery($dql);
        $query->setParameter('user', $user);
        $query->setParameter('language', $user->getLanguage());
        $query->setMaxResults(1);

        $foundable = $query->getOneOrNullResult();

        return $foundable;
    }
}