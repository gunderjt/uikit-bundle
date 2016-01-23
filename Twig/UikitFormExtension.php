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
    private $layoutModifier = null;

    /** @var string */
    private $widthModifier = null;

    /** @var string */
    private $sizeModifier = null;

    /** @var string */
    private $colSize = null;  //eg. 'medium-1-2', or '1-1'  its appended to 'uk-width-'

    /** @var bool */
    private $simpleCol = false;

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
            new Twig_SimpleFunction('uikit_set_width_modifier', array($this, 'setWidthModifier')),
            new Twig_SimpleFunction('uikit_get_width_modifier', array($this, 'getWidthModifier')),
            new Twig_SimpleFunction('uikit_get_size_modifier', array($this, 'getSizeModifier')),
            new Twig_SimpleFunction('uikit_set_size_modifier', array($this, 'setSizeModifier')),
            new Twig_SimpleFunction('uikit_set_col_size', array($this, 'setColSize')),
            new Twig_SimpleFunction('uikit_get_col_size', array($this, 'getColSize')),
            new Twig_SimpleFunction('uikit_set_simple_col', array($this, 'setSimpleCol')),
            new Twig_SimpleFunction('uikit_get_simple_col', array($this, 'getSimpleCol')),
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
     * @param string $layout_modifier Name of the layout_modifier ('horizontal', 'verticle')
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
     * Sets the uikit width modifier.
     *
     * @param string $widthModifier Name of the widthModifier ('large', 'medium', 'small', 'mini')
     */
    public function setWidthModifier($widthModifier)
    {
        $this->widthModifier = $widthModifier;
    }

    /**
     * Returns the width modifier.
     *
     * @return string Name of the width modifier
     */
    public function getWidthModifier()
    {
        return $this->widthModifier;
    }

    /**
     * Sets the uikit size modifier.
     *
     * @param string $sizeModifier Name of the sizeModifier ('horizontal', 'verticle')
     */
    public function setSizeModifier($sizeModifier)
    {
        $this->sizeModifier = $sizeModifier;
    }

    /**
     * Returns the size modifier.
     *
     * @return string Name of the size modifier
     */
    public function getSizeModifier()
    {
        return $this->sizeModifier;
    }

    /**
     * Sets the column size.
     *
     * @param string $colSize Column size (xs, sm, md or lg)
     */
    public function setColSize($colSize)
    {
        $this->colSize = $colSize;
    }

    /**
     * Returns the column size.
     *
     * @return string Column size (xs, sm, md or lg)
     */
    public function getColSize()
    {
        return $this->colSize;
    }

    /**
     * Sets the number of columns of simple widgets.
     *
     * @param integer $simpleCol Number of columns.
     */
    public function setSimpleCol($simpleCol)
    {
        $this->simpleCol = $simpleCol;
    }

    /**
     * Returns the number of columns of simple widgets.
     *
     * @return integer Number of columns.
     */
    public function getSimpleCol()
    {
        return $this->simpleCol;
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
            '$layoutModifier' => $this->layoutModifier,
            'colSize'   => $this->colSize,
            'simpleCol' => $this->simpleCol,
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
        $this->colSize   = $settings['colSize'];
        $this->simpleCol = $settings['simpleCol'];
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
