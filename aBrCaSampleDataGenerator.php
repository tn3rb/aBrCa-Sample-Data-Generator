<?php
/*
Plugin Name: aBrCa Sample Data Generator
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Generates sample data for Event Espresso
Version: 1.0.0
Author: aBrCa
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

use aBrCa\SampleDataGenerator\Domain\Services\SampleDataGenerator;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\loaders\LoaderFactory;

/**
 * Class aBrCaSampleDataGenerator
 * Generates Sample Data for Event Espresso
 *
 * @package aBrCa\SampleDataGenerator
 * @author  Brent R Christensen
 * @since   1.0.0
 */
class aBrCaSampleDataGenerator
{

    const OPTION_KEY_ACTIVATED = 'aBrCa-activated';

    /**
     * @var SampleDataGenerator $sample_data_generator
     */
    private $sample_data_generator;


    /**
     * SampleDataGenerator constructor.
     */
    public function __construct()
    {
        if (isset($_REQUEST['add_sample_data'])) {
            add_action('AHEE__EE_System__initialize', array($this, 'registerDependencies'), 9);
            add_action('AHEE__EE_System__initialize', array($this, 'loadSampleDataGenerator'));
            add_action('AHEE__EE_System__initialize_last', array($this, 'addSampleData'));
        }
    }


    public function registerDependencies()
    {
        EE_Dependency_Map::register_dependencies(
            'aBrCa\SampleDataGenerator\Domain\Services\SampleDataGenerator',
            array(
                'aBrCa\SampleDataGenerator\Domain\Services\CartDataGenerator'  => EE_Dependency_Map::load_from_cache,
                'aBrCa\SampleDataGenerator\Domain\Services\EventDataGenerator' => EE_Dependency_Map::load_from_cache,
            )
        );
        EE_Dependency_Map::register_dependencies(
            'aBrCa\SampleDataGenerator\Domain\Services\CartDataGenerator',
            array(
                'EE_Cart'                                                  => EE_Dependency_Map::load_from_cache,
                'aBrCa\SampleDataGenerator\Domain\Services\DataTracker'    => EE_Dependency_Map::load_from_cache,
                'EventEspresso\core\domain\values\session\SessionLifespan' => EE_Dependency_Map::load_from_cache,
            )
        );
        EE_Dependency_Map::register_dependencies(
            'aBrCa\SampleDataGenerator\Domain\Services\EventDataGenerator',
            array(
                'aBrCa\SampleDataGenerator\Domain\Services\DataTracker' => EE_Dependency_Map::load_from_cache,
            )
        );
    }


    /**
     * @throws InvalidArgumentException
     * @throws InvalidInterfaceException
     * @throws InvalidDataTypeException
     * @throws DomainException
     */
    public function loadSampleDataGenerator()
    {
        EE_Psr4AutoloaderInit::psr4_loader()->addNamespace('aBrCa\SampleDataGenerator', __DIR__);
        $this->sample_data_generator = LoaderFactory::getLoader()->getShared(
            'aBrCa\SampleDataGenerator\Domain\Services\SampleDataGenerator'
        );
    }


    /**
     * @throws DomainException
     * @throws EE_Error
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     */
    public function addSampleData()
    {
        $this->sample_data_generator->addSampleData();
    }


    /**
     * @return void
     */
    public static function activation()
    {
        if(get_option(self::OPTION_KEY_ACTIVATED)) {
            return;
        }
        // get the Primary menu object by its name
        // $menu = get_term_by('name', 'Primary', 'nav_menu');
        $menu = wp_get_nav_menu_object('Primary');
        if(! is_nav_menu($menu)) {
            return;
        }
        // add menu item
        $menu_id = wp_update_nav_menu_item(
            $menu->term_id,
            0,
            array(
                'menu-item-title'   => esc_html__('Add Sample Data', 'aBrCa'),
                'menu-item-classes' => 'aBrCa-add-sample-data',
                'menu-item-url'     => '#',
                'menu-item-status'  => 'publish'
            )
        );
         wp_update_nav_menu_item(
            $menu->term_id,
            0,
            array(
                'menu-item-parent-id' => $menu_id,
                'menu-item-title'   => esc_html__('Add Sample Event Data', 'aBrCa'),
                'menu-item-classes' => 'aBrCa-add-sample-event-data',
                'menu-item-url'     => add_query_arg(
                    array('add_sample_data' => 'event'),
                    home_url('/events/')
                ),
                'menu-item-status'  => 'publish'
            )
        );
         wp_update_nav_menu_item(
            $menu->term_id,
            0,
            array(
                'menu-item-parent-id' => $menu_id,
                'menu-item-title'   => esc_html__('Add Sample Cart Data', 'aBrCa'),
                'menu-item-classes' => 'aBrCa-add-sample-cart-data',
                'menu-item-url'     => add_query_arg(
                    array('add_sample_data' => 'cart'),
                    home_url('/events/')
                ),
                'menu-item-status'  => 'publish'
            )
        );
        // then update the menu_check option to make sure this code only runs once
        update_option(self::OPTION_KEY_ACTIVATED, true);
    }
}
new aBrCaSampleDataGenerator();
register_activation_hook(__FILE__, array('aBrCaSampleDataGenerator', 'activation'));

