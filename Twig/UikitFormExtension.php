<?php
/**
 * This file is part of GunderjtUikitBundle.
 */

namespace Gunderjt\UikitBundle\Twig;

use Twig_Extension;
use Twig_SimpleFunction;


/**
 * UikitFormExtension
 *
 * @package    GunderjtUikitBundle
 * @subpackage Twig
 * @author     Jeffrey Gunderson <jeffrey.gunderson@gmail.com>
 * @copyright  2016 Jeffrey Gunderson
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
class UikitFormExtension extends Twig_Extension
{
    /** @var string */
    private $layoutModifier = false;

    /** @var string */
    private $inputClass = null;  //eg. 'uk-width-medium-1-2', or 'uk-width-1-1'

    /** @var bool */
    private $simpleRow = true;

    /** @var array */
    private $settingsStack = array();

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('uikit_set_layout_modifier', array($this, 'setLayoutModifier')),
            new Twig_SimpleFunction('uikit_get_layout_modifier', array($this, 'getLayoutModifier')),
            new Twig_SimpleFunction('uikit_set_input_class', array($this, 'setInputClass')),
            new Twig_SimpleFunction('uikit_get_input_class', array($this, 'getInputClass')),
            new Twig_SimpleFunction('uikit_set_simple_row', array($this, 'setSimpleRow')),
            new Twig_SimpleFunction('uikit_get_simple_row', array($this, 'getSimpleRow')),
            new Twig_SimpleFunction('uikit_backup_form_settings', array($this, 'backupFormSettings')),
            new Twig_SimpleFunction('uikit_restore_form_settings', array($this, 'restoreFormSettings')),
            new Twig_SimpleFunction(
                'checkbox_row',
                null,
                array('is_safe' => array('html'), 'node_class' => 'Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode')
            ),
            new Twig_SimpleFunction(
                'radio_row',
                null,
                array('is_safe' => array('html'), 'node_class' => 'Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode')
            ),
            new Twig_SimpleFunction(
                'global_form_errors',
                null,
                array('is_safe' => array('html'), 'node_class' => 'Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode')
            ),
            new Twig_SimpleFunction(
                'form_control_static',
                array($this, 'formControlStaticFunction'),
                array('is_safe' => array('html'))
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'gunderjt_uikit_form';
    }

    /**
     * Sets the layout modifier.
     *
     * @param string $layout_modifier Name of the layout_modifier ('horizontal', 'stacked')
     */
    public function setLayoutModifier($layoutModifier)
    {
        $this->layoutModifier = $layoutModifier;
    }

    /**
     * Returns the layout modifier.
     *
     * @return string Name of the layout modifier
     */
    public function getLayoutModifier()
    {
        return $this->layoutModifier;
    }

    /**
     * Sets the column size.
     *
     * @param string $class is added to input, ease for changing classes of input elements
     */
    public function setInputClass($inputClass)
    {
        $this->inputClass = $inputClass;
    }

    /**
     * Returns the column size.
     *
     * @return string Column size (xs, sm, md or lg)
     */
    public function getInputClass()
    {
        return $this->inputClass;
    }

    /**
     * Sets the number of columns of simple widgets.
     *
     * @param integer $simpleRow Number of columns.
     */
    public function setSimpleRow($simpleRow)
    {
        $this->simpleRow = $simpleRow ? true : false;
    }

    /**
     * Returns the number of columns of simple widgets.
     *
     * @return integer Number of columns.
     */
    public function getSimpleRow()
    {
        return $this->simpleRow;
    }

    /**
     * Backup the form settings to the stack.
     *
     * @internal Should only be used at the beginning of form_start. This allows
     *           a nested subform to change its settings without affecting its
     *           parent form.
     */
    public function backupFormSettings()
    {
        $settings = array(
            'layoutModifier' => $this->layoutModifier,
            'inputClass'   => $this->inputClass,
            'simpleRow' => $this->simpleRow,
        );

        array_push($this->settingsStack, $settings);
    }

    /**
     * Restore the form settings from the stack.
     *
     * @internal Should only be used at the end of form_end.
     * @see backupFormSettings
     */
    public function restoreFormSettings()
    {
        if (count($this->settingsStack) < 1) {
            return;
        }

        $settings = array_pop($this->settingsStack);

        $this->layoutModifier = $settings['layoutModifier'];
        $this->inputClass = $settings['inputClass'];
        $this->simpleRow = $settings['simpleRow'];
    }

    /**
     * @param string $label
     * @param string $value
     *
     * @return string
     */
    public function formControlStaticFunction($label, $value)
    {
        return  sprintf(
            '<div class="form-group"><label class="col-sm-%s control-label">%s</label><div class="col-sm-%s"><p class="form-control-static">%s</p></div></div>',
            $this->getLabelCol(), $label, $this->getWidgetCol(), $value
        );
    }
}
