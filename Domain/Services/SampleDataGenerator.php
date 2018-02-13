<?php

namespace aBrCa\SampleDataGenerator\Domain\Services;

use DomainException;
use EE_Cart;
use EE_Error;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use InvalidArgumentException;
use ReflectionException;

/**
 * Class SampleDataGenerator
 * Generates Sample Data for Event Espresso
 *
 * @package aBrCa\SampleDataGenerator
 * @author  Brent R Christensen
 * @since   $VID:$
 */
class SampleDataGenerator
{

    /**
     * @var EventDataGenerator $event_data_generator
     */
    protected $event_data_generator;

    /**
     * @var CartDataGenerator $cart_data_generator
     */
    protected $cart_data_generator;


    /**
     * SampleDataGenerator constructor.
     *
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws InvalidInterfaceException
     * @throws InvalidDataTypeException
     * @throws EE_Error
     * @throws DomainException
     */
    public function __construct()
    {
        $data_tracker = new DataTracker();
        $this->event_data_generator = new EventDataGenerator($data_tracker);
        $this->cart_data_generator = new CartDataGenerator(EE_Cart::instance(), $data_tracker);
    }


    /**
     * @throws DomainException
     * @throws EE_Error
     * @throws InvalidArgumentException
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     * @throws ReflectionException
     */
    public function addSampleData()
    {
        $add_sample_data = sanitize_text_field($_REQUEST['add_sample_data']);
        switch ($add_sample_data) {
            case 'event' :
                $this->event_data_generator->addSampleEventData();
                break;
            case 'cart' :
                $this->cart_data_generator->addSampleCartData();
                break;
        }
    }


}
