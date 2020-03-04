<?php

namespace App\Controller;

use App\Entity\Wagers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WagersController
 * @package App\Controller
 */
class WagersController extends BaseController
{
    /**
     * @Route("/wagers")
     * @param Request $request
     * @return object|JsonResponse
     */
    public function create(Request $request)
    {
        $totalWagerValue        = $request->get('total_wager_value');
        $oddsValue              = $request->get('odds');
        $sellingPercentageValue = $request->get('selling_percentage');
        $sellingPriceValue      = $request->get('selling_price');

        /**
         * Validate
         * @TODO Move to validator to entity instead controller
         */

        if (!is_numeric($totalWagerValue)) {
            return $this->respondBad('Wager is not numeric or missed');
        } else {
            $totalWagerValue = (int)$totalWagerValue;
            if ($totalWagerValue < 0) {
                return $this->respondBad('Wager is lower than 0');
            }
        }

        if (!is_numeric($oddsValue)) {
            return $this->respondBad('Odd is not numeric or missed');
        } else {
            $oddsValue = (int)$oddsValue;
            if ($oddsValue < 0) {
                return $this->respondBad('Odd is lower than 0');
            }
        }

        if (!is_numeric($sellingPercentageValue)) {
            return $this->respondBad('Selling percentage is not numeric or missed');
        } else {
            $sellingPercentageValue = (int)$sellingPercentageValue;
            if ($sellingPercentageValue <= 0 || $sellingPercentageValue > 100) {
                return $this->respondBad('Selling percentage must be larger than 0 and lower or equal 100');
            }
        }

        if (!is_numeric($sellingPriceValue)) {
            return $this->respondBad('Selling price is not numeric or missed');
        } else {
            $sellingPriceValue = round((double)$sellingPriceValue, 2);
            $minSellingPrice   = $totalWagerValue * ($sellingPercentageValue / 100);

            if ($sellingPriceValue <= $minSellingPrice) {
                return $this->respondBad('Selling price too low');
            }
        }

        $em = $this->getDoctrine()->getManager();

        $wager = new Wagers();
        $wager->setTotalWagerValue($totalWagerValue);
        $wager->setOdds($oddsValue);
        $wager->setSellingPercentage($sellingPercentageValue);
        $wager->setSellingPrice($sellingPriceValue);
        $wager->setCurrentSellingPrice($sellingPriceValue);

        $em->persist($wager);

        try {
            $em->flush();

            /**
             * Really stupid concept but only for demo
             */

            $object                        = new \stdClass();
            $object->total_wager_value     = $wager->getTotalWagerValue();
            $object->odds                  = $wager->getOdds();
            $object->selling_percentage    = $wager->getSellingPercentage();
            $object->selling_price         = $wager->getSellingPrice();
            $object->id                    = $wager->getId();
            $object->current_selling_price = $wager->getCurrentSellingPrice();
            $object->percentage_sold       = $wager->getPercentageSold();
            $object->amount_sold           = $wager->getAmountSold();
            $object->placed_at             = $wager->getPlacedAt();

            return $this->respondSucceedData($object);
        } catch (\Exception $exception) {
            return $this->respondBad($exception->getMessage());
        }
    }

    /**
     * @Route("/buy/{id}")
     * @param int $id
     * @return object|JsonResponse
     */
    public function buy(int $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity = $em->getRepository(Wagers::class)->find($id)) {
            return $this->respondBad('Wager not exists');
        }

        $buyingPriceValue = $request->get('buying_price');

        if (!is_numeric($buyingPriceValue)) {
            return $this->respondBad('Buying price value is not valid');
        } else {

        }
    }
}