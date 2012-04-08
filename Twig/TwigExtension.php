<?php

namespace BFOS\PagseguroBundle\Twig;

use \Symfony\Component\Translation\TranslatorInterface;
use \Symfony\Component\Routing\RouterInterface;
use \Symfony\Component\DependencyInjection\Container;


use \Doctrine\ORM\EntityManager;


class TwigExtension extends \Twig_Extension
{

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Duo\CMSBundle\Security\SecurityHandler
     */
    private $handler;

    /**
     * @var \Twig_Environment
     */
    protected $env;

    /**
     *
     * @var  \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     *
     * @var  \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     *
     * @var  \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     *
     * @var  \Duo\CMSBundle\Model\BlockManager
     */
    protected $block_manager;

    /**
     * @var $securityHandler \Duo\CMSBundle\Security\SecurityHandler
     */
    protected $securityHandler;

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->env = $environment;
    }

    public function __construct(EntityManager $em,
                                TranslatorInterface $translator,
                                RouterInterface $router,
                                Container $container,
                                BlockManager $block_manager
    )
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->router = $router;
        $this->container = $container;
        $this->block_manager = $block_manager;
        $this->securityHandler = $this->container->get('duo_cms.security_handler');
    }

    public function setSecurityHandler(SecurityHandler $handler)
    {
        $this->handler = $handler;
    }

    public function getGlobals()
    {
        // Retrieve the Request object form the container and get the hostname
        $hostname = $this->container->get('request')->getHost();
        return array('hostname' => $hostname);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'duocms' => new \Twig_Filter_Method($this, 'content', array('is_safe' => array('html'))),
            'duocms_collection' => new \Twig_Filter_Method($this, 'contentCollection', array('is_safe' => array('html'))),
            'duocms_remove_array_element' => new \Twig_Filter_Method($this, 'removeArrayElement', array('is_safe' => array('html'))),
            'duocms_iseditmode' => new \Twig_Filter_Method($this, 'isGranted', array('is_safe' => array('html'))),
            'duocms_metatags' => new \Twig_Filter_Method($this, 'metaTags', array('is_safe' => array('html'))),
        );
    }

    public function getFunctions()
    {
        return array(
            'duocms_panel' => new \Twig_Function_Method($this, 'panel', array('is_safe' => array('html'))),
            'duocms_block' => new \Twig_Function_Method($this, 'block', array('is_safe' => array('html'))),
            'duocms_media_library' => new \Twig_Function_Method($this, 'mediaLibrary'),
            'duocms_navigation' => new \Twig_Function_Method($this, 'navigation'),
            'duocms_page_tree' => new \Twig_Function_Method($this, 'pageTree'),
        );
    }

    public function removeArrayElement(array $argument, $index)
    {
        unset($argument[$index]);
        return $argument;
    }

    /*public static function initMetaTagString()
    {
        return "<!--duocms-metatags-->";
    }

    public static function generatePathInfoFromMetaTagKey($key)
    {
        $arr = \Duo\CMSBundle\Model\ContentManager::splitLocaledKeyword($key);
        $key = str_replace('_', '/', $arr[1]);
        $key = str_replace('-00-', '_', $key);
        return $key;
    }*/

    /*public static function generateMetaTagKeyFromPathInfo($pathinfo, $locale)
    {

        $key = str_replace('_', '-00-', $pathinfo); // replace / replacement
        $key = str_replace('/', '_', $key); //replace /
        $key = \Duo\CMSBundle\Model\ContentManager::generateLocaledKeyword($key, $locale);
        return $key;
    }*/

    /*public static function generateMetaTagKey(\Symfony\Component\HttpFoundation\Request $request, $locale)
    {

        return self::generateMetaTagKeyFromPathInfo($request->getPathInfo(), $locale);
    }*/

    /*public static function wrapOutputEdit(\Symfony\Component\Routing\RouterInterface $router, $out, $key, $type, array $arguments = array(), $default='')
    {
        $class = '';
        if (isset($arguments['inline']) && $arguments['inline'] == true) {
            $class = 'inline';
        }

        $editpath = $router->generate('duo_cms_content_edit_key', array('key' => $key, 'type' => $type));
        $editpath .="?args=" . urlencode(serialize($arguments));
        $editpath .="&default=" . $default;
        $out = '<a href="' . $editpath . '" class="duocms-editlink" ></a>' . $out;
        $out = '<a href="' . $router->generate('duo_cms_content_delete_key', array('key' => $key, 'type' => $type)) . '" class="duocms-deletelink" > </a>' . $out;
        $out = "<div class=\"duocms-$key-$type duocms-edit $class\" id=\"duocms-$key-$type\" >$out</div>";

        return $out;
    }

    private function wrapOutputForEdit($out, $key, $type, array $arguments = array(), $default='')
    {
        return self::wrapOutputEdit($this->router, $out, $key, $type, $arguments, $default);
    }*/

    /*public function content($key, $type, array $arguments = array(), $locale = null, $default=null)
    {

        $debugmessage = '';

        if ($this->env->isDebug()) {
            $debugmessage .= "<!--debug DuoCMS\n";
            $debugmessage .= "type=$type \n";
            $debugmessage .= "key=$key \n";
            $debugmessage .= "default=$default \n";
            $debugmessage .= "arguments=" . print_r($arguments, true) . " \n";
            $debugmessage .= '-->';

            if ($default == '' || $default == null) {
                $default = "$key-$type";
            }
        }
        if ($locale == null) {
            $locale = $this->translator->getLocale();
        }
        $obj = $this->em->find($type, $key, $locale);
        if ($obj) {
            $key = $obj->getKeyword();
            $out = $debugmessage . $obj->toHTML($this, $arguments);
        } else {
            $key = $this->em->generateLocaledKeyword($key, $locale);
            $out = $default;
        }
        if (isset($arguments['before'])) {
            $out = $arguments['before'] . $out;
        }
        if (isset($arguments['after'])) {
            $out .= $arguments['after'];
        }

        $grant = $this->handler->isGranted('duo_cms_content_edit_key', array('key' => $key, 'type' => $type));
        //$grant = $this->handler->isGranted('duo_cms_content');
        if (!$grant) {
            return $out;
        }
        if ($out == '') {
            $out = "$key-$type";
        }

        return $this->wrapOutputForEdit($out, $key, $type, $arguments, $default);
    }*/

    public function mediaLibrary($name = null){
        $em = $this->em;

        $rmedia   = $this->container->get('doctrine')->getRepository('DuoCMSBundle:MediaItem');
        $entities = $rmedia->findAll();

        $entity = new MediaItem();
        $form   = $this->container->get('form.factory')->create(new MediaItemType(), $entity);
        $form_new_view = $form->createView();

        $items = $rmedia->findAll();
        MediaItem::$router = $this->container->get('router');

        return $this->env->render('DuoCMSBundle:MediaItem:media_library.html.twig',
            array(
                'entities' => $entities,
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'entity' => $entity,
                'mediaLibrary_form_new'   => $form_new_view,
                'items'=>$items
            )
        );
    }

    public function panel($name, $page = null, $edit_mode = null, array $arguments = array(), $locale = null)
    {
        $debug_mode = $this->env->isDebug();
        $options = array();
        $options['debug_mode'] = $debug_mode;

        if(is_null($edit_mode)){
            if($this->securityHandler->isCMSAdmin()){
                $options['edit_mode'] = true;
            } else {
                $options['edit_mode'] = false;
            }
        } else {
            $options['edit_mode'] = (boolean) $edit_mode;
        }

        $is_singleton = (isset($arguments['singleton']) && is_bool($arguments['singleton']))?$arguments['singleton']:false;
        $options['singleton'] = $is_singleton;

        /**
         * @var $block_manager \Duo\CMSBundle\Model\BlockManager
         */
        $block_manager = $this->container->get('duo_cms.block_manager');
        $strategies = $block_manager->getBlockStrategies();

        // determine the allowed block types
        if(!isset($arguments['allowedBlockTypes'])){
            $allowedBlockTypes = array_keys($strategies);
        } else {
            $allowedBlockTypes = $arguments['allowedBlockTypes'];
        }
        // exclude the block types
        if(isset($arguments['excludeBlockTypes'])){
            $allowedBlockTypes = array_diff($allowedBlockTypes, $arguments['excludeBlockTypes']);
        }
        // validate the block type options
        foreach($allowedBlockTypes as $block_type){
            if(isset($strategies[$block_type])){
                $blockTypeOptions = isset($arguments['blockTypesOptions'][$block_type]) ? $arguments['blockTypesOptions'][$block_type] : array();
                $blockTypeOptions = array_merge($strategies[$block_type]->getDefaultOptions(), $blockTypeOptions);
                $strategies[$block_type]->validateOptions($blockTypeOptions);
                $arguments['blockTypesOptions'][$block_type] = $blockTypeOptions;
            } else {
                $this->container->get('logger')->err(sprintf('Could not find the block type `%s`', $block_type));
                return null;
            }
        }

        // set the locale
        if ($locale == null) {
            $locale = $this->translator->getLocale();
        }
        if ($locale == null) {
            throw new \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException('The default locale was not set');
        }
        $blocks_to_view = array();
        /**
         * @var $mpanel \Duo\CMSBundle\Entity\PanelManager
         */
        $mpanel = $this->container->get('duo_cms.panel_manager');
        $panel = $mpanel->getPanelOrCreate($name, $locale, $page);
        if($options['edit_mode'] && serialize($panel->getOptions())!=serialize($arguments)){
            $panel->setOptions($arguments);
            $mpanel->persist($panel);
        }
        $mpanel->flush();

        $blocks = $mpanel->getBlocks($panel);

        if($is_singleton && $options['edit_mode']){

            if(count($blocks)==0){

                $block = new \Duo\CMSBundle\Entity\Block();
                $block->setType(reset($arguments['allowedBlockTypes']));
                $block->setValue('');

                $mpanel->addNewBlockToPanel($panel, $block);
                $mpanel->refresh($panel);
                $blocks = $mpanel->getBlocks($panel);
            }
        }

        $out = '';
        if ($blocks) {
            foreach ($blocks as $block) {
                /**
                 * @var $blockStrategy \Duo\CMSBundle\CMS\Block\BlockStrategyInterface
                 */
                $blockStrategy = $this->block_manager->getBlockStrategy($block->getType());

                if(!$blockStrategy) {
                    $this->container->get('logger')->err(sprintf('Could not find the block type `%s`', $block->getType()));
                    continue;
                }

                /*$block_options = array();
                $value = $block->getValue();

                $block_options['value'] = $value;
                $block_options['id'] = $block->getId();
                $block_options['blockTypeOptions'] = ;
                $value = $this->env->getExtension('actions')->renderAction($blockStrategy->getNormalViewActionName(), array('options' => $block_options));
                unset($block_options['value']);*/
                $value = $block_manager->getBlockNormalViewValue($blockStrategy, $block, isset($arguments['blockTypesOptions'][$block->getType()])?$arguments['blockTypesOptions'][$block->getType()]:array());

                $b = array();
                $b['id'] = $block->getId();
                $b['value'] = $value;
                $b['edit_mode'] = $options['edit_mode'];
//                $b['options'] = $block_options;
                $b['normal_view_template'] = 'DuoCMSBundle:Content:block_wrapper.html.twig';
                $b['block_normal_view_action'] = $blockStrategy->getNormalViewActionName();

                // delete form
                $b['form_delete'] = $mpanel->getBlockForm('delete', $panel, $block);
                // move first form
                $b['form_move_first'] = $mpanel->getBlockForm('move_first', $panel, $block);
                // move last form
                $b['form_move_last'] = $mpanel->getBlockForm('move_last', $panel, $block);
                // move up form
                $b['form_move_up'] = $mpanel->getBlockForm('move_up', $panel, $block);
                // move down form
                $b['form_move_down'] = $mpanel->getBlockForm('move_down', $panel, $block);

                $blocks_to_view[] = $b;

            }
        }

        return $this->env->render('DuoCMSBundle:Content:panel_edit_mode.html.twig', array('panel'=>$panel, 'blocks'=>$blocks_to_view, 'options' => $options, 'strategies'=>$strategies));

    }

    public function block($name, $type, $edit_mode = null, $page = null, array $arguments = array(), $locale = null)
    {
        $arguments['singleton'] = true;
        $arguments['allowedBlockTypes'] = array($type);
        unset($arguments['excludeBlockTypes']);
        return $this->panel($name, $page, $edit_mode, $arguments, $locale);
    }

    /**
     * @param \Duo\CMSBundle\Entity\Page $page The current active page
     */
    public function navigation(Page $page){
        $active = $page;
        $children = $page->getChildren();
        if(count($children)){
            $pages = $children;
        } else {
            $parent = $page->getParent();
            if($parent){
                $pages = $parent->getChildren();
            } else {
                $pages = array($page);
            }
        }
        return $this->env->render('DuoCMSBundle:Page:navigation.html.twig', array('pages'=>$pages, 'active_page'=>$page));
    }

    /**
     * @param \Duo\CMSBundle\Entity\PageTree $page_tree The page tree object
     */
    public function pageTree(PageTree $page_tree){

        return $this->env->render('DuoCMSBundle:Page:pageTree.html.twig', array('page_tree'=>$page_tree));
    }

    public function isGranted($key, $type)
    {
        $grant = $this->handler->isGranted('duo_cms_content');
        if ($grant) {
            $grant = $this->handler->isGranted('duo_cms_content_edit_key', array('key' => $key, 'type' => $type));
        }
        return $grant;
    }

    public function filterHtml($string)
    {
        return twig_escape_filter($this->env, $string);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'duocms';
    }


    /**
     * Returns the node visitor instances to add to the existing list.
     *
     * @return array An array of Twig_NodeVisitorInterface instances
     */
    public function getNodeVisitors()
    {
        return array('duocms'=>new Teste());
    }


}

class Teste implements \Twig_NodeVisitorInterface{
    /**
     * Called before child nodes are visited.
     *
     * @param Twig_NodeInterface $node The node to visit
     * @param Twig_Environment   $env  The Twig environment instance
     *
     * @return Twig_NodeInterface The modified node
     */
    function enterNode(\Twig_NodeInterface $node, \Twig_Environment $env)
    {
        // TODO: Implement enterNode() method.
        $teste = $node;
        return $node;
    }

    /**
     * Called after child nodes are visited.
     *
     * @param Twig_NodeInterface $node The node to visit
     * @param Twig_Environment   $env  The Twig environment instance
     *
     * @return Twig_NodeInterface The modified node
     */
    function leaveNode(\Twig_NodeInterface $node, \Twig_Environment $env)
    {
        // TODO: Implement leaveNode() method.
        return $node;
    }

    /**
     * Returns the priority for this visitor.
     *
     * Priority should be between -10 and 10 (0 is the default).
     *
     * @return integer The priority level
     */
    function getPriority()
    {
        // TODO: Implement getPriority() method.
        return 10;
    }

}