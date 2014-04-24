<?php

namespace Arcana\Bundle\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="content_unique", columns={"name", "locale"})
 * })
 * @ORM\Entity(repositoryClass="Arcana\Bundle\ContentBundle\EntityRepository\ContentRepository")
 */
class Content
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=5)
     */
    private $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;


    /**
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return Content
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $locale
     * @return Content
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string 
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $text
     * @return Content
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }
}
