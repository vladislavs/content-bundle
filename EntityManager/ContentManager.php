<?php

namespace Arcana\Bundle\ContentBundle\EntityManager;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use JMS\DiExtraBundle\Annotation as DI;
use Arcana\Bundle\ContentBundle\Exception\MissingLocaleException;

/**
 * @DI\Service("arcana.content.manager")
 */
class ContentManager
{
    /**
     * @var \Arcana\Bundle\ContentBundle\EntityRepository\ContentRepository
     */
    private $repository;

    /**
     * @var RequestStack
     */
    private $requests;

    /**
     * @DI\InjectParams
     *
     * @param EntityManager $em
     * @param RequestStack $request_stack
     */
    public function __construct(EntityManager $em, RequestStack $request_stack = null)
    {
        $this->repository = $em->getRepository('ArcanaContentBundle:Content');
        $this->requests = $request_stack;
    }

    /**
     * @param array $texts
     */
    public function updateTexts(array $texts)
    {
        $contents = $this->repository->findById(array_keys($texts));

        foreach ($contents as $content) {
            $content->setText(str_replace('<s></s>', '', $texts[$content->getId()]));
        }
    }

    /**
     * Gets content by name. If content does not exist in current or specified
     * locale it will be created with default text. If default text is null then
     * name is used instead.
     *
     * @param string $name
     * @param string $default
     * @param string $locale
     * @throws MissingLocaleException
     */
    public function get($name, $default = null, $locale = null)
    {
        if (null === $locale) {
            if (null === $this->requests || !$request = $this->requests->getCurrentRequest()) {
                throw new MissingLocaleException('Missing locale, you probably need to pass it as a parameter.');
            }

            $locale = $request->getLocale();
        }

        $content = $this->repository->findByNameAndLocale($name, $locale);

        if (!$content) {
            if (null === $default) {
                $default = $name;
            }

            $content = $this->repository->create($name, $default, $locale);
        }

        return $content;
    }
}
