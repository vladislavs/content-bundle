<?php

namespace Arcana\Bundle\ContentBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\SecurityContext;
use Arcana\Bundle\ContentBundle\EntityManager\ContentManager;
use Arcana\Bundle\ContentBundle\Entity\Content;

/**
 * Provides integration of ArcanaContentBundle with Twig.
 *
 * @DI\Service("arcana.content.twig.content_extension")
 * @DI\Tag("twig.extension")
*/
class ContentExtension extends \Twig_Extension
{
    /**
     * @var ContentManager
     */
    private $manager;

    /**
     * @var SecurityContext
     */
    private $security;
    
    /**
     * @var array 
     */
    private $separateContents;


    /**
     * @DI\InjectParams({
     *     "manager"=@DI\Inject("arcana.content.manager"),
     *     "security"=@DI\Inject("security")
     * })
     *
     * @param ContentManager $manager
     */
    public function __construct(ContentManager $manager, SecurityContext $security)
    {
        $this->manager = $manager;
        $this->security = $security;
        $this->separateContents = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('content', array($this, 'content'), array('pre_escape' => 'html', 'is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return array(
            // {% content "example_title", locale="en" %}Lorem ipsum dolor sit amet!{% endcontent %}
            new ContentTokenParser(),
        );
    }

    /**
     * @param string $default
     * @param string $name
     * @param array $options (locale, editable_separately)
     * @return string
     */
    public function content($default, $name = null, array $options = array())
    {
        if (null === $name) {
            $name = $default;
        }

        if (!isset($options['locale'])) {
            $options['locale'] = null;
        }

        if (!isset($options['type'])) {
            $options['type'] = 'inline';
        }

        $content = $this->manager->get($name, $default, $options['locale']);

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            return $content->getText();
        }

        if (isset($options['editable_separately']) && $options['editable_separately']) {
            $this->addSeparateContent($content, $options);

            return $content->getText();
        }

        /**
         * Raptor editor does not work with plain text inside the editable
         * container. The "<s></s>" is a hack/fix for this. You should manually
         * strip this tag out when saving content.
         */
        return '<span data-content="' . $options['type'] . '" data-content-id="' . $content->getId() . '"><s></s>' . $content->getText() . '</span>';
    }

    /**
     * @return ContentManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'arcana_content';
    }

    /**
     * @param Content $content
     * @param array $options (type)
     */
    public function addSeparateContent(Content $content, array $options = array())
    {
        $this->separateContents[$content->getId()] = array(
            'entity' => $content,
            'options' => $options,
        );
    }

    /**
     * @return array
     */
    public function getSeparateContents()
    {
        return $this->separateContents;
    }
}
