<?php

namespace  MagicWordBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Request;
use MagicWordBundle\Entity\Round;
use MagicWordBundle\Entity\Activity;

/**
 * @DI\Service("mw_manager.activity")
 */
class ActivityManager
{
    protected $em;
    protected $tokenStorage;
    protected $foundFormManager;
    protected $currentUser;

    /**
     * @DI\InjectParams({
     *      "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
     *      "tokenStorage" = @DI\Inject("security.token_storage"),
     *      "foundFormManager" = @DI\Inject("mw_manager.foundform"),
     * })
     */
    public function __construct($entityManager, $tokenStorage, $foundFormManager)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->foundFormManager = $foundFormManager;
        $this->currentUser = $this->tokenStorage->getToken()->getUser();
    }

    public function init(Round $round)
    {
        $activity = $this->getActivity($round);
        if (!$activity) {
            $this->create($round);
            $delta = 0;
        } else {
            $start = strtotime($activity->getStartDate()->format('Y-m-d H:i:s'));
            $now = new \DateTime();
            $now = strtotime($now->format('Y-m-d H:i:s'));

            $delta = $now - $start;
        }

        return $delta;
    }

    public function addFoundForm(Round $round, Request $request)
    {
        $activity = $this->getActivity($round);
        $foundForm = $this->foundFormManager->create($activity, $request);

        $activity->addFoundForm($foundForm);

        $this->em->persist($activity);
        $this->em->flush();
    }

    private function create(Round $round)
    {
        $activity = new Activity();
        $activity->setRound($round);
        $activity->setPlayer($this->currentUser);
        $activity->setStartDate(new \DateTime());

        $this->em->persist($activity);
        $this->em->flush();

        return $activity;
    }

    public function getActivity(Round $round)
    {
        return $activity = $this->em->getRepository('MagicWordBundle:Activity')->findOneBy(['player' => $this->currentUser, 'round' => $round]);
    }
}