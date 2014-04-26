<?php

namespace Arcana\Bundle\ContentBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use JMS\DiExtraBundle\Annotation as DI;
use Arcana\Bundle\ContentBundle\Twig\ContentExtension;

/**
 * @DI\Service("arcana.content.separately_editable_content_listener")
 * @DI\Tag("kernel.event_subscriber")
 */
class SeparatelyEditableContentListener implements EventSubscriberInterface
{
    protected $twig;
    protected $security;
    protected $extension;

    /**
     * @DI\InjectParams({
     *     "twig"=@DI\Inject("twig"),
     *     "security"=@DI\Inject("security.context"),
     *     "extension"=@DI\Inject("arcana.content.twig.content_extension")
     * })
     *
     * @param \Twig_Environment $twig
     * @param SecurityContext $security
     * @param ContentExtension $extension
     */
    public function __construct(\Twig_Environment $twig, SecurityContext $security, ContentExtension $extension)
    {
        $this->twig = $twig;
        $this->security = $security;
        $this->extension = $extension;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        if (!$event->isMasterRequest()) {
            return;
        }

        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

        $contents = $this->extension->getSeparateContents();

        if (!$this->security->getToken()
            || !$this->security->isGranted('ROLE_ADMIN')
            || empty($contents)
            || $response->isRedirection()
            || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
            || 'html' !== $request->getRequestFormat()
        ) {
            return;
        }

        $this->injectSeparatelyEditableContents($response, $contents);
    }

    /**
     * @param Response $response A Response instance
     * @param array $contents
     */
    protected function injectSeparatelyEditableContents(Response $response, array $contents)
    {
        if (function_exists('mb_stripos')) {
            $posrFunction   = 'mb_strripos';
            $substrFunction = 'mb_substr';
        } else {
            $posrFunction   = 'strripos';
            $substrFunction = 'substr';
        }

        $html = $response->getContent();
        $pos = $posrFunction($html, '</body>');

        if (false !== $pos) {
            $contentsHtml = "\n".str_replace("\n", '', $this->twig->render(
                '@ArcanaContent/separately_editable_contents.html.twig',
                array('contents' => $contents)
            ))."\n";
            $html = $substrFunction($html, 0, $pos).$contentsHtml.$substrFunction($html, $pos);
            $response->setContent($html);
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -128),
        );
    }
}
