<?php

namespace Arcana\Bundle\ContentBundle\Twig;

class ContentTokenParser extends \Twig_TokenParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $name = null;
        $locale = null;
        $type = null;
        if (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            if ($stream->test('of')) {
                // {% content of "title" %}
                $stream->next();
                $name = $this->parser->getExpressionParser()->parseExpression();
            }

            if ($stream->test('as')) {
                // {% ... as "inline" %}
                $stream->next();
                $type = $this->parser->getExpressionParser()->parseExpression();
            }

            if ($stream->test('for')) {
                // {% ... for "ru" %}
                $stream->next();
                $locale =  $this->parser->getExpressionParser()->parseExpression();
            } elseif (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
                throw new \Twig_Error_Syntax('Unexpected token. Twig was looking for the "of" or "for" keyword.', $stream->getCurrent()->getLine(), $stream->getFilename());
            }
        }

        if (null === $name) {
            throw new \Twig_Error_Syntax('Missing required "of" keyword.', $stream->getCurrent()->getLine(), $stream->getFilename());
        }

        // {% content %}text{% endcontent %}
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideContentFork'), true);

        if (!$body instanceof \Twig_Node_Text && !$body instanceof \Twig_Node_Expression) {
            throw new \Twig_Error_Syntax('A text inside a content tag must be a simple text.', $body->getLine(), $stream->getFilename());
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new ContentNode($body, $name, $type, $locale, $lineno, $this->getTag());
    }

    /**
     * @param \Twig_Token $token
     * @return boolean
     */
    public function decideContentFork($token)
    {
        return $token->test(array('endcontent'));
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'content';
    }
}
