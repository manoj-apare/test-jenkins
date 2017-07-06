<?php

/* themes/cypress_store/templates/page--cart.html.twig */
class __TwigTemplate_bcfe728705a93919daf3e329d83950d0fcd14d936cecb4936bec823f166bb638 extends Twig_Template
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
        // line 215
        echo "
";
        // line 216
        if ($this->getAttribute(($context["page"] ?? null), "footer", array())) {
            // line 217
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
        // line 154
        echo "            ";
        if (($context["breadcrumb"] ?? null)) {
            // line 155
            echo "              ";
            $this->displayBlock('breadcrumb', $context, $blocks);
            // line 158
            echo "            ";
        }
        // line 159
        echo "
            ";
        // line 161
        echo "            ";
        if (($context["action_links"] ?? null)) {
            // line 162
            echo "              ";
            $this->displayBlock('action_links', $context, $blocks);
            // line 165
            echo "            ";
        }
        // line 166
        echo "
            ";
        // line 168
        echo "            ";
        if ($this->getAttribute(($context["page"] ?? null), "help", array())) {
            // line 169
            echo "              ";
            $this->displayBlock('help', $context, $blocks);
            // line 172
            echo "            ";
        }
        // line 173
        echo "
            ";
        // line 175
        echo "            ";
        $this->displayBlock('content', $context, $blocks);
        // line 179
        echo "            
          </section>
          ";
        // line 182
        echo "          ";
        if ($this->getAttribute(($context["page"] ?? null), "sidebar_second", array())) {
            // line 183
            echo "            ";
            $this->displayBlock('sidebar_second', $context, $blocks);
            // line 188
            echo "          ";
        }
        // line 189
        echo "          <div class=\"cart-page-blocks\">
              <div class=\"col-md-4\">
                <h3>";
        // line 191
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->renderVar("Request Samples"));
        echo "</h3>
                <strong>";
        // line 192
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->renderVar(t("Need free samples?")));
        echo "</strong>
                <p>New customers requesting free samples - Click <a href=\"http://www.cypress.com/about-us/sales-offices\">here</a> to contact your local Cypress Sales Office or Authorized Distributor.</p>
                <p>Cypress Sales Representative or Distributors - Click <a href=\"http://www.cypress.com/go/cart/samples\">here</a> to request a promotional code.</p>
                <p>University Student or Professor - Click <a href=\"http://www.cypress.com/university-alliance\">here</a> to request University samples.</p>
              </div>
              <div class=\"col-md-4\">
                <h3>";
        // line 198
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->renderVar("Next Day Shipping"));
        echo "</h3>
                <p>
                 Next Day Shipping* on all orders placed by 2PM PST (23:00 GMT) Monday through Friday. *Depending on stock availability at time of order.
                </p>
              </div>
              <div class=\"col-md-4\">
                <h3>";
        // line 204
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->renderVar("Help"));
        echo "</h3>
                <p>
                  Need help with your order? Start by visiting the <a href=\"http://www.cypress.com/cypress-store-help\">help page</a> for answers to the most frequently asked questions. For all other questions, you can contact us via email <a href=\"https://www.cypress.com/user/login?destination=mycases\">here.</a>
                </p>
              </div>
          </div>
        </div>
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

    // line 155
    public function block_breadcrumb($context, array $blocks = array())
    {
        // line 156
        echo "                ";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["breadcrumb"] ?? null), "html", null, true));
        echo "
              ";
    }

    // line 162
    public function block_action_links($context, array $blocks = array())
    {
        // line 163
        echo "                <ul class=\"action-links\">";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["action_links"] ?? null), "html", null, true));
        echo "</ul>
              ";
    }

    // line 169
    public function block_help($context, array $blocks = array())
    {
        // line 170
        echo "                ";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["page"] ?? null), "help", array()), "html", null, true));
        echo "
              ";
    }

    // line 175
    public function block_content($context, array $blocks = array())
    {
        // line 176
        echo "              <a id=\"main-content\"></a>
              ";
        // line 177
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["page"] ?? null), "content", array()), "html", null, true));
        echo "
            ";
    }

    // line 183
    public function block_sidebar_second($context, array $blocks = array())
    {
        // line 184
        echo "              <aside class=\"col-sm-4 col-md-3 sidebar-right\" role=\"complementary\">
                ";
        // line 185
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["page"] ?? null), "sidebar_second", array()), "html", null, true));
        echo "
              </aside>
            ";
    }

    // line 217
    public function block_footer($context, array $blocks = array())
    {
        // line 218
        echo "    <footer class=\"footer ";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["container"] ?? null), "html", null, true));
        echo "\" role=\"contentinfo\">
      ";
        // line 219
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["page"] ?? null), "footer", array()), "html", null, true));
        echo "
    </footer>
  ";
    }

    public function getTemplateName()
    {
        return "themes/cypress_store/templates/page--cart.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  416 => 219,  411 => 218,  408 => 217,  401 => 185,  398 => 184,  395 => 183,  389 => 177,  386 => 176,  383 => 175,  376 => 170,  373 => 169,  366 => 163,  363 => 162,  356 => 156,  353 => 155,  346 => 138,  343 => 137,  340 => 136,  331 => 126,  326 => 123,  323 => 122,  316 => 111,  313 => 110,  310 => 109,  295 => 204,  286 => 198,  277 => 192,  273 => 191,  269 => 189,  266 => 188,  263 => 183,  260 => 182,  256 => 179,  253 => 175,  250 => 173,  247 => 172,  244 => 169,  241 => 168,  238 => 166,  235 => 165,  232 => 162,  229 => 161,  226 => 159,  223 => 158,  220 => 155,  217 => 154,  212 => 152,  210 => 149,  209 => 148,  208 => 147,  207 => 146,  206 => 145,  204 => 144,  201 => 142,  198 => 141,  195 => 136,  192 => 135,  188 => 132,  185 => 131,  182 => 122,  179 => 121,  173 => 117,  169 => 115,  166 => 114,  163 => 109,  160 => 108,  154 => 103,  151 => 102,  145 => 96,  141 => 94,  138 => 93,  132 => 90,  129 => 89,  126 => 88,  122 => 85,  113 => 79,  110 => 78,  107 => 77,  103 => 75,  100 => 74,  94 => 72,  92 => 71,  87 => 70,  85 => 67,  84 => 66,  83 => 64,  81 => 63,  78 => 62,  72 => 217,  70 => 216,  67 => 215,  65 => 102,  62 => 100,  58 => 62,  56 => 61,  54 => 59,);
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
          <div class=\"cart-page-blocks\">
              <div class=\"col-md-4\">
                <h3>{{'Request Samples'}}</h3>
                <strong>{{ 'Need free samples?'|t }}</strong>
                <p>New customers requesting free samples - Click <a href=\"http://www.cypress.com/about-us/sales-offices\">here</a> to contact your local Cypress Sales Office or Authorized Distributor.</p>
                <p>Cypress Sales Representative or Distributors - Click <a href=\"http://www.cypress.com/go/cart/samples\">here</a> to request a promotional code.</p>
                <p>University Student or Professor - Click <a href=\"http://www.cypress.com/university-alliance\">here</a> to request University samples.</p>
              </div>
              <div class=\"col-md-4\">
                <h3>{{'Next Day Shipping'}}</h3>
                <p>
                 Next Day Shipping* on all orders placed by 2PM PST (23:00 GMT) Monday through Friday. *Depending on stock availability at time of order.
                </p>
              </div>
              <div class=\"col-md-4\">
                <h3>{{'Help'}}</h3>
                <p>
                  Need help with your order? Start by visiting the <a href=\"http://www.cypress.com/cypress-store-help\">help page</a> for answers to the most frequently asked questions. For all other questions, you can contact us via email <a href=\"https://www.cypress.com/user/login?destination=mycases\">here.</a>
                </p>
              </div>
          </div>
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
", "themes/cypress_store/templates/page--cart.html.twig", "/home/manoj/public_html/cypress-store/docroot/themes/cypress_store/templates/page--cart.html.twig");
    }
}