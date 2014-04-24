<?php

namespace Arcana\Bundle\ContentBundle\Twig;

class ContentNode extends \Twig_Node
{
    /**
     * @param \Twig_NodeInterface $body
     * @param \Twig_Node_Expression $name
     * @param \Twig_Node_Expression $locale
     * @param integer $lineno
     * @param string $tag
     */
    public function __construct(\Twig_NodeInterface $body, \Twig_Node_Expression $name = null, \Twig_Node_Expression $type = null, \Twig_Node_Expression $locale = null, $lineno = 0, $tag = null)
    {
        parent::__construct(array('body' => $body, 'name' => $name, 'type' => $type, 'locale' => $locale), array(), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $body = $this->getNode('body');

        if ($body instanceof \Twig_Node_Expression_Constant) {
            $body = new \Twig_Node_Expression_Constant(trim($body->getAttribute('value')), $body->getLine());
        } elseif ($body instanceof \Twig_Node_Text) {
            $body = new \Twig_Node_Expression_Constant(trim($body->getAttribute('data')), $body->getLine());
        }

        $compiler
            ->write('echo $this->env->getExtension(\'arcana_content\')->content(')
            ->subcompile($body)
            ->raw(',')
            ->subcompile($this->getNode('name'))
            ->raw(', array(');
        
        $locale = $this->getNode('locale');

        if ($locale) {
            $compiler
                ->raw("'locale' => ")
                ->subcompile($locale)
                ->raw(',');
        }

        $type = $this->getNode('type');

        if ($type) {
            $compiler
                ->raw("'type' => ")
                ->subcompile($type);
        } else {
            $compiler->raw("'type' => 'block'");
        }

        $compiler->raw("));\n");
    }
}
