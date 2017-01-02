<?php

/* index.twig */
class __TwigTemplate_a60cb21f8de99e7adf2fc4fe212591471c41ba69f70a77aab2c1c8328b12758e extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("app.twig", "index.twig", 1);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "app.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_content($context, array $blocks = array())
    {
        // line 3
        echo "
hello ";
        // line 4
        echo twig_escape_filter($this->env, $this->getAttribute(($context["data"] ?? null), "name", array()), "html", null, true);
        echo ", your mobile is ";
        echo twig_escape_filter($this->env, $this->getAttribute(($context["data"] ?? null), "mobile", array()), "html", null, true);
        echo "

";
    }

    public function getTemplateName()
    {
        return "index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  34 => 4,  31 => 3,  28 => 2,  11 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends 'app.twig' %}
{% block content %}

hello {{ data.name }}, your mobile is {{ data.mobile }}

{% endblock %}", "index.twig", "/Users/EvaiChen/PHP/Aier/app/Views/index.twig");
    }
}
