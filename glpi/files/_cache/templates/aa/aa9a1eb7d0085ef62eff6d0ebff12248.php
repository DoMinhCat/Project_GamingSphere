<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* layout/page_card_notlogged.html.twig */
class __TwigTemplate_60dfbf250c08762790d45833b5c38b69 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content_block' => [$this, 'block_content_block'],
            'footer_block' => [$this, 'block_footer_block'],
            'javascript_block' => [$this, 'block_javascript_block'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 33
        yield "
";
        // line 34
        $context["theme"] = $this->extensions['Glpi\Application\View\Extension\ConfigExtension']->config("palette");
        // line 35
        if ( !array_key_exists("css_files", $context)) {
            // line 36
            yield "   ";
            $context["css_files"] = [["path" => "public/lib/base.css"], ["path" => (("css/palettes/" .             // line 38
($context["theme"] ?? null)) . ".scss")]];
            // line 40
            yield "   ";
        }
        // line 42
        if ( !array_key_exists("js_files", $context)) {
            // line 43
            yield "   ";
            $context["js_files"] = [["path" => "public/lib/base.js"], ["path" => "js/common.js"], ["path" => "public/lib/fuzzy.js"]];
        }
        // line 49
        if ( !array_key_exists("js_modules", $context)) {
            // line 50
            yield "   ";
            $context["js_modules"] = [];
        }
        // line 52
        if ( !array_key_exists("custom_header_tags", $context)) {
            // line 53
            yield "   ";
            $context["custom_header_tags"] = [];
        }
        // line 55
        yield "
";
        // line 57
        $context["js_files"] = Twig\Extension\CoreExtension::merge(($context["js_files"] ?? null), $this->extensions['Glpi\Application\View\Extension\PluginExtension']->getPluginsJsScriptsFiles(true));
        // line 58
        $context["js_modules"] = Twig\Extension\CoreExtension::merge(($context["js_modules"] ?? null), $this->extensions['Glpi\Application\View\Extension\PluginExtension']->getPluginsJsModulesFiles(true));
        // line 59
        yield "
";
        // line 60
        $context["is_anonymous_page"] = true;
        // line 61
        yield "
";
        // line 62
        yield Twig\Extension\CoreExtension::include($this->env, $context, "layout/parts/head.html.twig");
        yield "
<body class=\"welcome-anonymous\">
   <div class=\"page-anonymous\">
      <div class=\"flex-fill d-flex flex-column justify-content-center py-4 mt-4\">
         ";
        // line 66
        $context["style"] = null;
        // line 67
        yield "         ";
        if (array_key_exists("card_md_width", $context)) {
            // line 68
            yield "            ";
            $context["style"] = "max-width: 40rem";
            // line 69
            yield "         ";
        }
        // line 70
        yield "         ";
        if (array_key_exists("card_bg_width", $context)) {
            // line 71
            yield "            ";
            $context["style"] = "max-width: 60rem";
            // line 72
            yield "         ";
        }
        // line 73
        yield "
         <div class=\"container-tight py-6\" ";
        // line 74
        if ( !(null === ($context["style"] ?? null))) {
            yield "style=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["style"] ?? null), "html", null, true);
            yield "\"";
        }
        yield ">
            <div class=\"text-center\">
               <div class=\"col-md\">
                  <span class=\"glpi-logo mb-4\" title=\"GLPI\"></span>
               </div>
            </div>
            <div class=\"card card-md\">
               <div class=\"card-body\">
               ";
        // line 82
        yield from $this->unwrap()->yieldBlock('content_block', $context, $blocks);
        // line 83
        yield "               </div>
            </div>

            <div class=\"text-center text-muted mt-3\">
               ";
        // line 87
        yield from $this->unwrap()->yieldBlock('footer_block', $context, $blocks);
        // line 88
        yield "            </div>
         </div>
      </div>
   </div>

   ";
        // line 93
        yield from $this->unwrap()->yieldBlock('javascript_block', $context, $blocks);
        // line 94
        yield "</body>
</html>
";
        return; yield '';
    }

    // line 82
    public function block_content_block($context, array $blocks = [])
    {
        $macros = $this->macros;
        return; yield '';
    }

    // line 87
    public function block_footer_block($context, array $blocks = [])
    {
        $macros = $this->macros;
        return; yield '';
    }

    // line 93
    public function block_javascript_block($context, array $blocks = [])
    {
        $macros = $this->macros;
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "layout/page_card_notlogged.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  173 => 93,  166 => 87,  159 => 82,  152 => 94,  150 => 93,  143 => 88,  141 => 87,  135 => 83,  133 => 82,  118 => 74,  115 => 73,  112 => 72,  109 => 71,  106 => 70,  103 => 69,  100 => 68,  97 => 67,  95 => 66,  88 => 62,  85 => 61,  83 => 60,  80 => 59,  78 => 58,  76 => 57,  73 => 55,  69 => 53,  67 => 52,  63 => 50,  61 => 49,  57 => 43,  55 => 42,  52 => 40,  50 => 38,  48 => 36,  46 => 35,  44 => 34,  41 => 33,);
    }

    public function getSourceContext()
    {
        return new Source("", "layout/page_card_notlogged.html.twig", "/var/www/PA/glpi/templates/layout/page_card_notlogged.html.twig");
    }
}
