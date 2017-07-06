<?php

/* themes/cypress_store/templates/page.html.twig */
class __TwigTemplate_af18a0507ff5e815bafd78863c52a5c41987ec046a24d0ecba42830155ae6d33 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'navbar' => array($this, 'block_navbar'),
            'main' => array($this, 'block_main'),
            'header' => array($this, 'block_header'),
            'highlighted' => array($this, 'block_highlighted'),
            'sidebar_first' => array($this, 'block_sidebar_first'),
            'breadcrumb' => array($this, 'block_breadcrumb'),
            'action_links' => array($this, 'block_action_links'),
            'help' => array($this, 'block_help'),
            'content' => array($this, 'block_content'),
            'sidebar_second' => array($this, 'block_sidebar_second'),
            'footer' => array($this, 'block_footer'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $tags = array("set" => 59, "if" => 61, "block" => 62);
        $filters = array("clean_class" => 67, "t" => 79);
        $functions = array();

        try {
            $this->env->getExtension('Twig_Extension_Sandbox')->checkSecurity(
                array('set', 'if', 'block'),
                array('clean_class', 't'),
                array()
            );
        } catch (Twig_Sandbox_SecurityError $e) {
            $e->setSourceContext($this->getSourceContext());

            if ($e instanceof Twig_Sandbox_SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof Twig_Sandbox_SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof Twig_Sandbox_SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

        // line 59
        $context["container"] = (($this->getAttribute($this->getAttribute(($context["theme"] ?? null), "settings", array()), "fluid_container", array())) ? ("container-fluid") : ("container"));
        // line 61
        if (($this->getAttribute(($context["page"] ?? null), "navigation", array()) || $this->getAttribute(($context["page"] ?? null), "navigation_collapsible", array()))) {
            // line 62
            echo "  ";
            $this->displayBlock('navbar', $context, $blocks);
        }
        // line 100
        echo "    
";
        // line 102
        $this->displayBlock('main', $context, $blocks);
        // line 197
        echo "
";
        // line 198
        if ($this->getAttribute(($context["page"] ?? null), "footer", array())) {
            // line 199
            echo "  ";
            $this->displayBlock('footer', $context, $blocks);
        }
    }

    // line 62
    public function block_navbar($context, array $blocks = array())
    {
        // line 63
        echo "    ";
        // line 64
        $context["navbar_classes"] = array(0 => "navbar", 1 => (($this->getAttribute($this->getAttribute(        // line 66
($context["theme"] ?? null), "settings", array()), "navbar_inverse", array())) ? ("navbar-inverse") : ("navbar-default")), 2 => (($this->getAttribute($this->getAttribute(        // line 67
($context["theme"] ?? null), "settings", array()), "navbar_position", array())) ? (("navbar-" . \Drupal\Component\Utility\Html::getClass($this->getAttribute($this->getAttribute(($context["theme"] ?? null), "settings", array()), "navbar_position", array())))) : (($context["container"] ?? null))));
        // line 70
        echo "    <header";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["navbar_attributes"] ?? null), "addClass", array(0 => ($context["navbar_classes"] ?? null)), "method"), "html", null, true));
        echo " id=\"navbar\" role=\"banner\">
      ";
        // line 71
        if ( !$this->getAttribute(($context["navbar_attributes"] ?? null), "hasClass", array(0 => "container"), "method")) {
            // line 72
            echo "        <div class=\"";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["container"] ?? null), "html", null, true));
            echo "\">
      ";
        }
        // line 74
        echo "      <div class=\"navbar-header\">
        ";
        // line 75
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["page"] ?? null), "navigation", array()), "html", null, true));
        echo "
        ";
        // line 77
        echo "        ";
        if ($this->getAttribute(($context["page"] ?? null), "navigation_collapsible", array())) {
            // line 78
            echo "          <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\"#navbar-collapse\">
            <span class=\"sr-only\">";
            // line 79
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->renderVar(t("Toggle navigation")));
            echo "</span>
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
          </button>
        ";
        }
        // line 85
        echo "      </div>

      ";
        // line 88
        echo "      ";
        if ($this->getAttribute(($context["page"] ?? null), "navigation_collapsible", array())) {
            // line 89
            echo "        <div id=\"navbar-collapse\" class=\"navbar-collapse collapse\">
          ";
            // line 90
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["page"] ?? null), "navigation_collapsible", array()), "html", null, true));
            echo "
        </div>
      ";
        }
        // line 93
        echo "      ";
        if ( !$this->getAttribute(($context["navbar_attributes"] ?? null), "hasClass", array(0 => "container"), "method")) {
            // line 94
            echo "        </div>
      ";
        }
        // line 96
        echo "    </header>

  ";
    }

    // line 102
    public function block_main($context, array $blocks = array())
    {
        // line 103
        echo "    
  <div role=\"main\" class=\"js-quickedit-main-content\">
    <div class=\"\">

      ";
        // line 108
        echo "      ";
        if ($this->getAttribute(($context["page"] ?? null), "header", array())) {
            // line 109
            echo "        ";
            $this->displayBlock('header', $context, $blocks);
            // line 114
            echo "      ";
        }
        // line 115
        echo "
      <div class=\"submenu\">
        ";
        // line 117
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["page"] ?? null), "dropdown_menu", array()), "html", null, true));
        echo "
      </div>

        ";
        // line 121
        echo "        ";
        if ($this->getAttribute(($context["page"] ?? null), "highlighted", array())) {
            // line 122
            echo "          ";
            $this->displayBlock('highlighted', $context, $blocks);
            // line 131
            echo "        ";
        }
        // line 132
        echo "      <div class=\"container\">
        <div class=\"row\">
          ";
        // line 135
        echo "          ";
        if ($this->getAttribute(($context["page"] ?? null), "sidebar_first", array())) {
            // line 136
            echo "            ";
            $this->displayBlock('sidebar_first', $context, $blocks);
            // line 141
            echo "          ";
        }
        // line 142
        echo "
          ";
        // line 144
        echo "          ";
        // line 145
        $context["content_classes"] = array(0 => ((($this->getAttribute(        // line 146
($context["page"] ?? null), "sidebar_first", array()) && $this->getAttribute(($context["page"] ?? null), "sidebar_second", array()))) ? ("col-sm-6") : ("")), 1 => ((($this->getAttribute(        // line 147
($context["page"] ?? null), "sidebar_first", array()) && twig_test_empty($this->getAttribute(($context["page"] ?? null), "sidebar_second", array())))) ? ("col-sm-8 col-md-9") : ("")), 2 => ((($this->getAttribute(        // line 148
($context["page"] ?? null), "sidebar_second", array()) && twig_test_empty($this->getAttribute(($context["page"] ?? null), "sidebar_first", array())))) ? ("col-sm-8 col-md-9") : ("")), 3 => (((twig_test_empty($this->getAttribute(        // line 149
($context["page"] ?? null), "sidebar_first", array())) && twig_test_empty($this->getAttribute(($context["page"] ?? null), "sidebar_second", array())))) ? ("col-sm-12") : ("")));
        // line 152
        echo "          <section";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["content_attributes"] ?? null), "addClass", array(0 => ($context["content_classes"] ?? null)), "method"), "html", null, true));
        echo ">



            ";
        // line 157
        echo "            ";
        if (($context["breadcrumb"] ?? null)) {
            // line 158
            echo "              ";
            $this->displayBlock('breadcrumb', $context, $blocks);
            // line 161
            echo "            ";
        }
        // line 162
        echo "
            ";
        // line 164
        echo "            ";
        if (($context["action_links"] ?? null)) {
            // line 165
            echo "              ";
            $this->displayBlock('action_links', $context, $blocks);
            // line 168
            echo "            ";
        }
        // line 169
        echo "
            ";
        // line 171
        echo "            ";
        if ($this->getAttribute(($context["page"] ?? null), "help", array())) {
            // line 172
            echo "              ";
            $this->displayBlock('help', $context, $blocks);
            // line 175
            echo "            ";
        }
        // line 176
        echo "
            ";
        // line 178
        echo "            ";
        $this->displayBlock('content', $context, $blocks);
        // line 182
        echo "          </section>

          ";
        // line 185
        echo "          ";
        if ($this->getAttribute(($context["page"] ?? null), "sidebar_second", array())) {
            // line 186
            echo "            ";
            $this->displayBlock('sidebar_second', $context, $blocks);
            // line 191
            echo "          ";
        }
        // line 192
        echo "        </div>
      </div>
    </div>
  </div>
";
    }

    // line 109
    public function block_header($context, array $blocks = array())
    {
        // line 110
        echo "          <div class=\"container header\" role=\"heading\">
            ";
        // line 111
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["page"] ?? null), "header", array()), "html", null, true));
        echo "
          </div>
        ";
    }

    // line 122
    public function block_highlighted($context, array $blocks = array())
    {
        // line 123
        echo "            <div class=\"highlighted container\">
              <div class=\"row\">
                <div class=\"col-sm-12\">
                  ";
        // line 126
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["page"] ?? null), "highlighted", array()), "html", null, true));
        echo "
                </div>
              </div>
            </div>
          ";
    }

    // line 136
    public function block_sidebar_first($context, array $blocks = array())
    {
        // line 137
        echo "              <aside class=\"col-sm-4 col-md-3\" role=\"complementary\">
                ";
        // line 138
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["page"] ?? null), "sidebar_first", array()), "html", null, true));
        echo "
              </aside>
            ";
    }

    // line 158
    public function block_breadcrumb($context, array $blocks = array())
    {
        // line 159
        echo "                ";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["breadcrumb"] ?? null), "html", null, true));
        echo "
              ";
    }

    // line 165
    public function block_action_links($context, array $blocks = array())
    {
        // line 166
        echo "                <ul class=\"action-links\">";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["action_links"] ?? null), "html", null, true));
        echo "</ul>
              ";
    }

    // line 172
    public function block_help($context, array $blocks = array())
    {
        // line 173
        echo "                ";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["page"] ?? null), "help", array()), "html", null, true));
        echo "
              ";
    }

    // line 178
    public function block_content($context, array $blocks = array())
    {
        // line 179
        echo "              <a id=\"main-content\"></a>
              ";
        // line 180
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["page"] ?? null), "content", array()), "html", null, true));
        echo "
            ";
    }

    // line 186
    public function block_sidebar_second($context, array $blocks = array())
    {
        // line 187
        echo "              <aside class=\"col-sm-4 col-md-3 sidebar-right\" role=\"complementary\">
                ";
        // line 188
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["page"] ?? null), "sidebar_second", array()), "html", null, true));
        echo "
              </aside>
            ";
    }

    // line 199
    public function block_footer($context, array $blocks = array())
    {
        // line 200
        echo "    <footer class=\"footer ";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["container"] ?? null), "html", null, true));
        echo "\" role=\"contentinfo\">
      ";
        // line 201
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["page"] ?? null), "footer", array()), "html", null, true));
        echo "
    </footer>
  ";
    }

    public function getTemplateName()
    {
        return "themes/cypress_store/templates/page.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  386 => 201,  381 => 200,  378 => 199,  371 => 188,  368 => 187,  365 => 186,  359 => 180,  356 => 179,  353 => 178,  346 => 173,  343 => 172,  336 => 166,  333 => 165,  326 => 159,  323 => 158,  316 => 138,  313 => 137,  310 => 136,  301 => 126,  296 => 123,  293 => 122,  286 => 111,  283 => 110,  280 => 109,  272 => 192,  269 => 191,  266 => 186,  263 => 185,  259 => 182,  256 => 178,  253 => 176,  250 => 175,  247 => 172,  244 => 171,  241 => 169,  238 => 168,  235 => 165,  232 => 164,  229 => 162,  226 => 161,  223 => 158,  220 => 157,  212 => 152,  210 => 149,  209 => 148,  208 => 147,  207 => 146,  206 => 145,  204 => 144,  201 => 142,  198 => 141,  195 => 136,  192 => 135,  188 => 132,  185 => 131,  182 => 122,  179 => 121,  173 => 117,  169 => 115,  166 => 114,  163 => 109,  160 => 108,  154 => 103,  151 => 102,  145 => 96,  141 => 94,  138 => 93,  132 => 90,  129 => 89,  126 => 88,  122 => 85,  113 => 79,  110 => 78,  107 => 77,  103 => 75,  100 => 74,  94 => 72,  92 => 71,  87 => 70,  85 => 67,  84 => 66,  83 => 64,  81 => 63,  78 => 62,  72 => 199,  70 => 198,  67 => 197,  65 => 102,  62 => 100,  58 => 62,  56 => 61,  54 => 59,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{#
/**
 * @file
 * Default theme implementation to display a single page.
 *
 * The doctype, html, head and body tags are not in this template. Instead they
 * can be found in the html.html.twig template in this directory.
 *
 * Available variables:
 *
 * General utility variables:
 * - base_path: The base URL path of the Drupal installation. Will usually be
 *   \"/\" unless you have installed Drupal in a sub-directory.
 * - is_front: A flag indicating if the current page is the front page.
 * - logged_in: A flag indicating if the user is registered and signed in.
 * - is_admin: A flag indicating if the user has permission to access
 *   administration pages.
 *
 * Site identity:
 * - front_page: The URL of the front page. Use this instead of base_path when
 *   linking to the front page. This includes the language domain or prefix.
 *
 * Navigation:
 * - breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.html.twig):
 * - title_prefix: Additional output populated by modules, intended to be
 *   displayed in front of the main title tag that appears in the template.
 * - title: The page title, for use in the actual content.
 * - title_suffix: Additional output populated by modules, intended to be
 *   displayed after the main title tag that appears in the template.
 * - messages: Status and error messages. Should be displayed prominently.
 * - tabs: Tabs linking to any sub-pages beneath the current page (e.g., the
 *   view and edit tabs when displaying a node).
 * - action_links: Actions local to the page, such as \"Add menu\" on the menu
 *   administration interface.
 * - node: Fully loaded node, if there is an automatically-loaded node
 *   associated with the page and the node ID is the second argument in the
 *   page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - page.header: Items for the header region.
 * - page.navigation: Items for the navigation region.
 * - page.navigation_collapsible: Items for the navigation (collapsible) region.
 * - page.highlighted: Items for the highlighted content region.
 * - page.help: Dynamic help text, mostly for admin pages.
 * - page.content: The main content of the current page.
 * - page.sidebar_first: Items for the first sidebar.
 * - page.sidebar_second: Items for the second sidebar.
 * - page.footer: Items for the footer region.
 *
 * @ingroup templates
 *
 * @see template_preprocess_page()
 * @see html.html.twig
 */
#}
{% set container = theme.settings.fluid_container ? 'container-fluid' : 'container' %}
{# Navbar #}
{% if page.navigation or page.navigation_collapsible %}
  {% block navbar %}
    {%
      set navbar_classes = [
        'navbar',
        theme.settings.navbar_inverse ? 'navbar-inverse' : 'navbar-default',
        theme.settings.navbar_position ? 'navbar-' ~ theme.settings.navbar_position|clean_class : container,
      ]
    %}
    <header{{ navbar_attributes.addClass(navbar_classes) }} id=\"navbar\" role=\"banner\">
      {% if not navbar_attributes.hasClass('container') %}
        <div class=\"{{ container }}\">
      {% endif %}
      <div class=\"navbar-header\">
        {{ page.navigation }}
        {# .btn-navbar is used as the toggle for collapsed navbar content #}
        {% if page.navigation_collapsible %}
          <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\"#navbar-collapse\">
            <span class=\"sr-only\">{{ 'Toggle navigation'|t }}</span>
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
          </button>
        {% endif %}
      </div>

      {# Navigation (collapsible) #}
      {% if page.navigation_collapsible %}
        <div id=\"navbar-collapse\" class=\"navbar-collapse collapse\">
          {{ page.navigation_collapsible }}
        </div>
      {% endif %}
      {% if not navbar_attributes.hasClass('container') %}
        </div>
      {% endif %}
    </header>

  {% endblock %}
{% endif %}
    
{# Main #}
{% block main %}
    
  <div role=\"main\" class=\"js-quickedit-main-content\">
    <div class=\"\">

      {# Header #}
      {% if page.header %}
        {% block header %}
          <div class=\"container header\" role=\"heading\">
            {{ page.header }}
          </div>
        {% endblock %}
      {% endif %}

      <div class=\"submenu\">
        {{ page.dropdown_menu }}
      </div>

        {# Highlighted #}
        {% if page.highlighted %}
          {% block highlighted %}
            <div class=\"highlighted container\">
              <div class=\"row\">
                <div class=\"col-sm-12\">
                  {{ page.highlighted }}
                </div>
              </div>
            </div>
          {% endblock %}
        {% endif %}
      <div class=\"container\">
        <div class=\"row\">
          {# Sidebar First #}
          {% if page.sidebar_first %}
            {% block sidebar_first %}
              <aside class=\"col-sm-4 col-md-3\" role=\"complementary\">
                {{ page.sidebar_first }}
              </aside>
            {% endblock %}
          {% endif %}

          {# Content #}
          {%
            set content_classes = [
              page.sidebar_first and page.sidebar_second ? 'col-sm-6',
              page.sidebar_first and page.sidebar_second is empty ? 'col-sm-8 col-md-9',
              page.sidebar_second and page.sidebar_first is empty ? 'col-sm-8 col-md-9',
              page.sidebar_first is empty and page.sidebar_second is empty ? 'col-sm-12'
            ]
          %}
          <section{{ content_attributes.addClass(content_classes) }}>



            {# Breadcrumbs #}
            {% if breadcrumb %}
              {% block breadcrumb %}
                {{ breadcrumb }}
              {% endblock %}
            {% endif %}

            {# Action Links #}
            {% if action_links %}
              {% block action_links %}
                <ul class=\"action-links\">{{ action_links }}</ul>
              {% endblock %}
            {% endif %}

            {# Help #}
            {% if page.help %}
              {% block help %}
                {{ page.help }}
              {% endblock %}
            {% endif %}

            {# Content #}
            {% block content %}
              <a id=\"main-content\"></a>
              {{ page.content }}
            {% endblock %}
          </section>

          {# Sidebar Second #}
          {% if page.sidebar_second %}
            {% block sidebar_second %}
              <aside class=\"col-sm-4 col-md-3 sidebar-right\" role=\"complementary\">
                {{ page.sidebar_second }}
              </aside>
            {% endblock %}
          {% endif %}
        </div>
      </div>
    </div>
  </div>
{% endblock %}

{% if page.footer %}
  {% block footer %}
    <footer class=\"footer {{ container }}\" role=\"contentinfo\">
      {{ page.footer }}
    </footer>
  {% endblock %}
{% endif %}
", "themes/cypress_store/templates/page.html.twig", "/home/manoj/public_html/cypress-store/docroot/themes/cypress_store/templates/page.html.twig");
    }
}
