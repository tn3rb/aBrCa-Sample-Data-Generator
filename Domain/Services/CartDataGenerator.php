<?php

namespace aBrCa\SampleDataGenerator\Domain\Services;

use EE_Cart;
use EE_Datetime;
use EE_Error;
use EE_Line_Item;
use EE_Ticket;
use EEH_Line_Item;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use InvalidArgumentException;
use ReflectionException;
use const WEEK_IN_SECONDS;

/**
 * Class CartDataGenerator
 * Description
 *
 * @package aBrCa\SampleDataGenerator\Domain\Services
 * @author  Brent Christensen
 * @since   $VID:$
 */
class CartDataGenerator extends DataGenerator
{

    /**
     * @var EE_Cart $cart
     */
    protected $cart;


    /**
     * CartDataGenerator constructor.
     *
     * @param EE_Cart     $cart
     * @param DataTracker $data_tracker
     */
    public function __construct(EE_Cart $cart, DataTracker $data_tracker)
    {
        parent::__construct($data_tracker);
        $this->cart = $cart;
    }


    /**
     * @param int $max
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws InvalidInterfaceException
     * @throws InvalidDataTypeException
     * @throws EE_Error
     */
    public function addSampleCartData($max = 100)
    {
        $created = $this->createCarts(mt_rand(1, $max));
        if ($created) {
            EE_Error::add_success("Generated {$created} line items");
        }
    }


    /**
     * @param int $cart_count
     * @return int
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws InvalidInterfaceException
     * @throws InvalidDataTypeException
     * @throws EE_Error
     */
    private function createCarts($cart_count = 0)
    {
        $created = 0;
        $now = time();
        $increment = WEEK_IN_SECONDS / $cart_count;
        $timestamp = $now - WEEK_IN_SECONDS - $increment;
        for ($x = 0; $x < $cart_count; $x++) {
            $created += $this->createCart($timestamp);
            $timestamp += $timestamp < $now
                ? $increment
                : 0;
        }
        return $created;
    }


    /**
     * @param int $timestamp
     * @return int
     * @throws EE_Error
     * @throws InvalidArgumentException
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     * @throws ReflectionException
     */
    private function createCart($timestamp = 0)
    {
        $this->populateObjects('Ticket');
        /** @var EE_Ticket $ticket */
        $ticket = $this->getAnyObject('Ticket');
        if ($ticket instanceof EE_Ticket && $ticket->first_datetime() instanceof EE_Datetime) {
            $total_line_item = EEH_Line_Item::create_total_line_item();
            $total_line_item->set_TXN_ID(0);
            EEH_Line_Item::add_ticket_purchase(
                $total_line_item,
                $ticket,
                mt_rand(1, 10)
            );
            $this->adjustLineItemTimestamps(
                $total_line_item,
                $timestamp,
                "<br />creating Cart {$total_line_item->name()} {$total_line_item->total_no_code()}<br />"
            );
            return $total_line_item->save_this_and_descendants();
        }
        return 0;
    }


    /**
     * @param EE_Line_Item $line_item
     * @param int          $timestamp
     * @param string       $log_note
     * @throws EE_Error
     */
    private function adjustLineItemTimestamps(EE_Line_Item $line_item, $timestamp, $log_note)
    {
        $this->log($log_note);
        $line_item->set('LIN_timestamp', $timestamp);
        foreach ($line_item->children() as $child_line_item) {
            $this->adjustLineItemTimestamps(
                $child_line_item,
                $timestamp,
                "&nbsp; . creating line item {$child_line_item->name()} {$child_line_item->total_no_code()}<br />"
            );
        }
    }
}
