<?php

namespace ExampleProject\Bundle\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use ExampleProject\Bundle\UserBundle\Entity\User;

/**
 * Class UserRepository
 * 
 * Only repository classes should contain the queries.
 * We could also implement a save() method in AbstractRepository, to have a simple way of getting and saving the data.
 * 
 */
class UserRepository extends EntityRepository
{
    /**
     * Find user with given username
     *
     * Comparison is done against username and e-mail
     *
     * @param string $username
     * @return User
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findByUsername($username)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('(STR_CMP(u.username, :username) = 1 OR u.email = :username)')
            ->andWhere('u.deleted IS NULL')
            ->setParameter('username', $username);

        $result = $qb->getQuery()->getSingleResult();

        return $result;
    }

    /**
     * Find online users (example when you require pagination)
     *
     * @param OnlineFilter $filter (contains all the data from request parameters)
     * @return User[]|array
     */
    public function findOnlineUsers(OnlineFilter $filter)
    {
        // We do not want deleted users here
        $this->enableFilters();

        $orderColumns = array(
            $filter::ORDER_DATA_USERNAME          => 'u.username',
            $filter::ORDER_DATA_ONLINE            => 'uap1.id',
            $filter::ORDER_DATA_AGE               => 'age',
            $filter::ORDER_DATA_MY_AGE_DIFFERENCE => 'age_difference',
            $filter::ORDER_DATA_RANDOM            => 'random',
        );

        $orderColumn = $orderColumns[$filter->getData()];

        $qb = $this->_em->createQueryBuilder();

        // When returning a lot of data, partial could be better for performance reasons
        $qb->addSelect('partial p.{id, ageMin, ageMax, born, sex, martialStatus, county, city, district, contentUpdated, photosUpdated}')
            ->addSelect('partial s.{id, onlineStatus}')
            ->addSelect('partial m.{id, filename, coordinatesString}')
            ->from('ExampleProjectUserBundle:User', 'u')
            ->leftJoin('u.profile', 'p')
            ->leftJoin('u.settings', 's')
            ->where('s.onlineStatus = :onlineStatus')
            ->andWhere($qb->expr()->isNotNull('u.active'))
            ->andWhere($qb->expr()->isNull('u.banned'))
            ->andWhere($qb->expr()->isNull('u.softBanned'))
            ->setParameter('onlineStatus', true);

        // Min age
        if ($filter->hasAgeMin()) {
            $qb->andWhere('AGE(p.born) >= :ageMin');
            $qb->setParameter('ageMin', $filter->getAgeMin());
        }

        // Max age
        if ($filter->hasAgeMax()) {
            $qb->andWhere('AGE(p.born) <= :ageMax');
            $qb->setParameter('ageMax', $filter->getAgeMax());
        }

        // Sex
        if ($filter->hasSpecificSex()) {
            $qb->andWhere('p.sex = :sex');
            $qb->setParameter('sex', $filter->getSex());
        }

        // Only with photos
        $filter->isOnlyWithPhotos()
            ? $qb->join('p.mainPhoto', 'm')
            : $qb->leftJoin('p.mainPhoto', 'm');

        $qb->setFirstResult($filter->getOffset())
            ->setMaxResults($filter->getLimit())
            ->orderBy($orderColumn, $filter->getOrder());

        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
