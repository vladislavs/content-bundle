<?php

namespace Arcana\Bundle\ContentBundle\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Arcana\Bundle\ContentBundle\Entity\Content;

class ContentRepository extends EntityRepository
{
    /**
     * @param integer|array $ids
     * @return Content|ArrayCollection
     */
    public function findById($ids)
    {
        return parent::findById($ids);
    }

    /**
     * @param string $name
     * @param string $locale
     * @return Content
     */
    public function findByNameAndLocale($name, $locale)
    {
        return $this->findOneBy(array(
            'name' => $name,
            'locale' => $locale,
        ));
    }

    /**
     * @param string $name
     * @param string $text
     * @param string $locale
     * @return Content
     */
    public function create($name, $text, $locale)
    {
        $content = new Content();
        $content->setName($name);
        $content->setLocale($locale);
        $content->setText($text);
        
        $em = $this->getEntityManager();

        $em->persist($content);
        $em->flush($content);

        return $content;
    }
}
