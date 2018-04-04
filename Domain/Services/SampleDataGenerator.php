<?php

namespace aBrCa\SampleDataGenerator\Domain\Services;

use DomainException;
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
     * @param CartDataGenerator $cart_data_generator
     * @param EventDataGenerator $event_data_generator
     */
    public function __construct(CartDataGenerator $cart_data_generator, EventDataGenerator $event_data_generator)
    {
        $this->cart_data_generator  = $cart_data_generator;
        $this->event_data_generator = $event_data_generator;
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
