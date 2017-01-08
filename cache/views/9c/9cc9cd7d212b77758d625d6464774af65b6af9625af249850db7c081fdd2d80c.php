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
        // line 2
        echo "
<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <title>app</title>
</head>

<body>

<header>header</header>

";
        // line 14
        $this->displayBlock('content', $context, $blocks);
        // line 17
        echo "
<footer>footer</footer>

</body>
</html>";
    }

    // line 14
    public function block_content($context, array $blocks = array())
    {
        // line 15
        echo "
";
    }

    public function getTemplateName()
    {
        return "app.twig";
    }

    public function getDebugInfo()
    {
        return array (  47 => 15,  44 => 14,  36 => 17,  34 => 14,  20 => 2,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{#app.twig#}

<!DOCTYPE html>
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
