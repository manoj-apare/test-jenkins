<?php

/* modules/custom/cypress_custom_address/templates//commerce-order--default.html.twig */
class __TwigTemplate_2a733c3b1b7d5c9fd2824544e774d0e47b863772c9bc826b01c688444e449f11 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $tags = array("if" => 23);
        $filters = array("t" => 25);
        $functions = array();

        try {
            $this->env->getExtension('Twig_Extension_Sandbox')->checkSecurity(
                array('if'),
                array('t'),
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

        // line 20
        echo "<div";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["attributes"] ?? null), "html", null, true));
        echo ">
  <h2>Order Details</h2>
  <div class=\"customer-information order-summary col-xs-12\">
    ";
        // line 23
        if ($this->getAttribute(($context["order"] ?? null), "shipping_information", array())) {
            // line 24
            echo "      <div class=\"customer-information__shipping col-sm-4 col-xs-12\">
        <h4 class=\"field__label\">";
            // line 25
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->renderVar(t("Shipping information")));
            echo "</h4>
        ";
            // line 26
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["order"] ?? null), "shipping_information", array()), "html", null, true));
            echo "
      </div>
    ";
        }
        // line 29
        echo "    ";
        if ($this->getAttribute(($context["order"] ?? null), "billing_information", array())) {
            // line 30
            echo "      <div class=\"customer-billing col-sm-4 col-xs-12\">
        <h4 class=\"field__label\">";
            // line 31
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->renderVar(t("Billing information")));
            echo "</h4>
        ";
            // line 32
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["order"] ?? null), "billing_information", array()), "html", null, true));
            echo "
      </div>
    ";
        }
        // line 35
        echo "      <div class=\"customer-parts-info col-sm-4   col-xs-12\">
        <h4 class=\"field__label\">";
        // line 36
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->renderVar(t("Part End Products")));
        echo "</h4>
        ";
        // line 37
        if ($this->getAttribute($this->getAttribute(($context["order_entity"] ?? null), "get", array(0 => "field_primary_application"), "method"), "value", array())) {
            // line 38
            echo "          <div class=\"primary-application\"><b>Primary Application for Projects/Designs:</b> ";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($this->getAttribute(($context["order_entity"] ?? null), "get", array(0 => "field_primary_application"), "method"), "value", array()), "html", null, true));
            echo "</div>
        ";
        }
        // line 40
        echo "        ";
        if ($this->getAttribute($this->getAttribute(($context["order_entity"] ?? null), "get", array(0 => "field_name_product_system"), "method"), "value", array())) {
            // line 41
            echo "          <div class=\"name-product-system\"><b>Name of your end Product/system:</b> ";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($this->getAttribute(($context["order_entity"] ?? null), "get", array(0 => "field_name_product_system"), "method"), "value", array()), "html", null, true));
            echo "</div>
        ";
        }
        // line 43
        echo "        ";
        if ($this->getAttribute($this->getAttribute(($context["order_entity"] ?? null), "get", array(0 => "field_purpose_of_order"), "method"), "value", array())) {
            // line 44
            echo "          <div class=\"purpose-of-order\"><b>Purpose of order:</b> ";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($this->getAttribute(($context["order_entity"] ?? null), "get", array(0 => "field_purpose_of_order"), "method"), "value", array()), "html", null, true));
            echo "</div>
        ";
        }
        // line 46
        echo "        ";
        if ($this->getAttribute($this->getAttribute(($context["order_entity"] ?? null), "get", array(0 => "field_end_customer"), "method"), "value", array())) {
            // line 47
            echo "          <div class=\"end-customer\"><b>End Customer:</b> ";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($this->getAttribute(($context["order_entity"] ?? null), "get", array(0 => "field_end_customer"), "method"), "value", array()), "html", null, true));
            echo "</div>
        ";
        }
        // line 49
        echo "        </div>
  </div>
  <div class=\"order-information col-xs-12\">
    ";
        // line 52
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["order"] ?? null), "completed", array()), "html", null, true));
        echo "
    ";
        // line 53
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["order"] ?? null), "placed", array()), "html", null, true));
        echo "
    ";
        // line 54
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["order"] ?? null), "order_items", array()), "html", null, true));
        echo "
    ";
        // line 55
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["order"] ?? null), "total_price", array()), "html", null, true));
        echo "
  </div>
  <div class=\"order-links col-md-6 col-sm-12 col-xs-12 pull-right\">
    <div class=\"col-md-6 col-sm-6 col-xs-12 cancel-order\">
      ";
        // line 59
        if (($context["cancel_url"] ?? null)) {
            // line 60
            echo "          ";
            if (($context["role"] ?? null)) {
                // line 61
                echo "              <a href=\"";
                echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["cancel_url"] ?? null), "html", null, true));
                echo "\" class=\"cancel-order-link btn-info\">Cancel order</a>
          ";
            } else {
                // line 63
                echo "              <a href=\"";
                echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["cancel_url"] ?? null), "html", null, true));
                echo "\" class=\"cancel-order-link btn-info\"; target=\"_blank\">Cancel order</a>
          ";
            }
            // line 65
            echo "      ";
        }
        // line 66
        echo "      ";
        if (($context["cancel_description"] ?? null)) {
            // line 67
            echo "        <p>";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["cancel_description"] ?? null), "html", null, true));
            echo "</p>
      ";
        }
        // line 69
        echo "    </div>
    <div class=\"col-md-6 col-sm-6 col-xs-12\">
      <a href=\"/shipment/";
        // line 71
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["order_entity"] ?? null), "getOrderNumber", array()), "html", null, true));
        echo "\" class=\"track-shipment-link btn-primary\">Track Shipments</a>
    </div>
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "modules/custom/cypress_custom_address/templates//commerce-order--default.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  180 => 71,  176 => 69,  170 => 67,  167 => 66,  164 => 65,  158 => 63,  152 => 61,  149 => 60,  147 => 59,  140 => 55,  136 => 54,  132 => 53,  128 => 52,  123 => 49,  117 => 47,  114 => 46,  108 => 44,  105 => 43,  99 => 41,  96 => 40,  90 => 38,  88 => 37,  84 => 36,  81 => 35,  75 => 32,  71 => 31,  68 => 30,  65 => 29,  59 => 26,  55 => 25,  52 => 24,  50 => 23,  43 => 20,);
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
 * Template for orders in the 'user' view mode.
 *
 * Available variables:
 * - attributes: HTML attributes for the wrapper.
 * - order: The rendered order fields.
 *   Use 'order' to print them all, or print a subset such as
 *   'order.order_number'. Use the following code to exclude the
 *   printing of a given field:
 *   @code
 *   {{ order|without('order_number') }}
 *   @endcode
 * - order_entity: The order entity.
 *
 * @ingroup themeable
 */
#}
<div{{ attributes }}>
  <h2>Order Details</h2>
  <div class=\"customer-information order-summary col-xs-12\">
    {% if order.shipping_information %}
      <div class=\"customer-information__shipping col-sm-4 col-xs-12\">
        <h4 class=\"field__label\">{{ 'Shipping information'|t }}</h4>
        {{ order.shipping_information }}
      </div>
    {% endif %}
    {% if order.billing_information %}
      <div class=\"customer-billing col-sm-4 col-xs-12\">
        <h4 class=\"field__label\">{{ 'Billing information'|t }}</h4>
        {{ order.billing_information }}
      </div>
    {% endif %}
      <div class=\"customer-parts-info col-sm-4   col-xs-12\">
        <h4 class=\"field__label\">{{ 'Part End Products'|t }}</h4>
        {% if order_entity.get('field_primary_application').value %}
          <div class=\"primary-application\"><b>Primary Application for Projects/Designs:</b> {{ order_entity.get('field_primary_application').value }}</div>
        {% endif %}
        {% if order_entity.get('field_name_product_system').value %}
          <div class=\"name-product-system\"><b>Name of your end Product/system:</b> {{ order_entity.get('field_name_product_system').value }}</div>
        {% endif %}
        {% if order_entity.get('field_purpose_of_order').value %}
          <div class=\"purpose-of-order\"><b>Purpose of order:</b> {{ order_entity.get('field_purpose_of_order').value }}</div>
        {% endif %}
        {% if order_entity.get('field_end_customer').value %}
          <div class=\"end-customer\"><b>End Customer:</b> {{ order_entity.get('field_end_customer').value }}</div>
        {% endif %}
        </div>
  </div>
  <div class=\"order-information col-xs-12\">
    {{ order.completed }}
    {{ order.placed }}
    {{ order.order_items }}
    {{ order.total_price }}
  </div>
  <div class=\"order-links col-md-6 col-sm-12 col-xs-12 pull-right\">
    <div class=\"col-md-6 col-sm-6 col-xs-12 cancel-order\">
      {% if cancel_url %}
          {% if role %}
              <a href=\"{{ cancel_url }}\" class=\"cancel-order-link btn-info\">Cancel order</a>
          {% else %}
              <a href=\"{{ cancel_url }}\" class=\"cancel-order-link btn-info\"; target=\"_blank\">Cancel order</a>
          {% endif %}
      {% endif %}
      {% if cancel_description %}
        <p>{{ cancel_description }}</p>
      {% endif %}
    </div>
    <div class=\"col-md-6 col-sm-6 col-xs-12\">
      <a href=\"/shipment/{{ order_entity.getOrderNumber }}\" class=\"track-shipment-link btn-primary\">Track Shipments</a>
    </div>
  </div>
</div>
", "modules/custom/cypress_custom_address/templates//commerce-order--default.html.twig", "/home/manoj/public_html/cypress-store/docroot/modules/custom/cypress_custom_address/templates/commerce-order--default.html.twig");
    }
}
