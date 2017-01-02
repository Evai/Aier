<?php

/* app.twig */
class __TwigTemplate_4f76cfd51d430b65277a9889e4925e9d8edf7aa83a3954f3e66cc7db4a6aa1a1 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <title>app</title>
</head>

<body>

<header>header</header>

";
        // line 12
        $this->displayBlock('content', $context, $blocks);
        // line 15
        echo "
<footer>footer</footer>

</body>
</html>";
    }

    // line 12
    public function block_content($context, array $blocks = array())
    {
        // line 13
        echo "
";
    }

    public function getTemplateName()
    {
        return "app.twig";
    }

    public function getDebugInfo()
    {
        return array (  46 => 13,  43 => 12,  35 => 15,  33 => 12,  20 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <title>app</title>
</head>

<body>

<header>header</header>

{% block content %}

{% endblock %}

<footer>footer</footer>

</body>
</html>", "app.twig", "/Users/EvaiChen/PHP/Aier/app/Views/app.twig");
    }
}
