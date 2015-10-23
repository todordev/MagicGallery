<?php
/**
 * @package      MagicGallery
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class MagicGalleryViewEntity extends JViewLegacy
{
    /**
     * @var JApplicationAdministrator
     */
    public $app;

    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    protected $state;
    protected $item;
    protected $form;

    protected $documentTitle;
    protected $option;

    protected $galleryId;
    protected $gallery;
    protected $mediaUri;

    public function display($tpl = null)
    {
        $this->app    = JFactory::getApplication();
        $this->option = $this->app->input->get('option');
        
        $this->state = $this->get('State');
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');

        $this->params = $this->state->get('params');

        $this->galleryId = (int)$this->app->getUserState('com_magicgallery.entities.filter.gallery_id');

        $this->gallery    = new Magicgallery\Gallery\Gallery(JFactory::getDbo());
        $this->gallery->load($this->galleryId);

        $this->mediaUri   = MagicGalleryHelper::getMediaUri($this->params, $this->gallery);
        if (!$this->mediaUri) {
            throw new Exception(JText::_('COM_MAGICGALLERY_ERROR_INVALID_MEDIA_FOLDER'));
        }

        $this->mediaUri = JUri::root() . $this->mediaUri . '/';

        $this->addToolbar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        $this->app->input->set('hidemainmenu', true);

        $isNew               = ((int)$this->item->id === 0);
        $this->documentTitle = $isNew ? JText::_('COM_MAGICGALLERY_ENTITY_ADD') : JText::_('COM_MAGICGALLERY_ENTITY_EDIT');

        JToolBarHelper::title($this->documentTitle);

        JToolBarHelper::apply('entity.apply');
        JToolBarHelper::save2new('entity.save2new');
        JToolBarHelper::save('entity.save');

        if (!$isNew) {
            JToolBarHelper::cancel('entity.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolBarHelper::cancel('entity.cancel', 'JTOOLBAR_CLOSE');
        }
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle($this->documentTitle);

        // Load language string in JavaScript
        JText::script('COM_MAGICGALLERY_CHOOSE_FILE');
        JText::script('COM_MAGICGALLERY_REMOVE');

        // Script
        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.keepalive');
        JHtml::_('behavior.formvalidation');

        JHtml::_('formbehavior.chosen', 'select');

        JHtml::_('Prism.ui.bootstrap2FileInput');

        $this->document->addScript('../media/' . $this->option . '/js/admin/' . Joomla\String\String::strtolower($this->getName()) . '.js');
    }
}