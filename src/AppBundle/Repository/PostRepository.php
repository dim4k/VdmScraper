<?php

namespace AppBundle\Repository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
	public function findByDateAndAuthor($author,$from,$to)
	{
		$qb = $this->createQueryBuilder('a');

		if($author != null){
			$qb->where('a.author = :author')
				->setParameter('author', $author);
		}

		if($from != null){
			$qb->andWhere('a.date <= :from')
				->setParameter('from', $from);
		}

		if($to != null){
			$qb->andWhere('a.date <= :to')
				->setParameter('to', $to);
		}


		return $qb
			->getQuery()
			->getResult()
			;
	}
}
