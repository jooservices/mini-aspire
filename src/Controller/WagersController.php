<?php

namespace App\Controller;

use App\Entity\Wagers;
use Knp\Component\Pager\PaginatorInterface;
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
     * @Route("/wagers", methods={"POST"})
     * @param Request $request
     * @return object|JsonResponse
     */
    public function create(Request $request)
    {
        // How much worth for this wager
        $totalWagerValue = $request->get('total_wager_value');
        $oddsValue       = $request->get('odds');
        // How many percentage we want to sell on this ticket
        $sellingPercentageValue = $request->get('selling_percentage');
        $sellingPriceValue      = $request->get('selling_price');

        /**
         * If we have wager 100 USD. And selling_percentage 10% with 15 USD. So wager final sell price is :  100% * 15 / 10% = 150 USD
         * But we are limited selling_percentage is 15% maximum
         */

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
             * We should respond entity object instead
             */

            $object                     = new \stdClass();
            $object->id                 = $wager->getId();
            $object->total_wager_value  = $wager->getTotalWagerValue();
            $object->odds               = $wager->getOdds();
            $object->selling_percentage = $wager->getSellingPercentage();
            $object->selling_price      = $wager->getSellingPrice();
            // How many price left after sold
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
     * @Route("/wagers", methods={"GET"})
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return JsonResponse
     */
    public function wagers(Request $request, PaginatorInterface $paginator)
    {
        $paginator = $paginator->paginate(
            $this->getDoctrine()->getRepository(Wagers::class)->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 1),
            );

        $items = $paginator->getItems();
        /**
         * @var Wagers $item
         */
        foreach ($items as $index => $item) {
            $object                        = new \stdClass();
            $object->id                    = $item->getId();
            $object->total_wager_value     = $item->getTotalWagerValue();
            $object->odds                  = $item->getOdds();
            $object->selling_percentage    = $item->getSellingPercentage();
            $object->selling_price         = $item->getSellingPrice();
            $object->current_selling_price = $item->getCurrentSellingPrice();
            $object->percentage_sold       = $item->getPercentageSold();
            $object->amount_sold           = $item->getAmountSold();
            $object->placed_at             = $item->getPlacedAt();
            $items[$index]                 = $object;
        }

        return $this->respondSucceedData($items);
    }

    /**
     * @Route("/buy/{id}")
     * @param int $id
     * @param Request $request
     * @return object|JsonResponse
     */
    public function buy(int $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var Wagers $wagerEntity
         */
        if (!$wagerEntity = $em->getRepository(Wagers::class)->find($id)) {
            return $this->respondBad('Wager not exists');
        }

        if ($wagerEntity->getAmountSold() == $wagerEntity->getSellingPrice()) {
            return $this->respondBad('Wager sold out');
        }

        $buyingPriceValue = $request->get('buying_price');

        if (!is_numeric($buyingPriceValue)) {
            return $this->respondBad('Buying price value is not valid');
        }
        $buyingPriceValue = round((double)$buyingPriceValue, 2);
        if ($buyingPriceValue > $wagerEntity->getCurrentSellingPrice()) {
            return $this->respondBad('Buying over price');
        }

        /**
         * Now we can buy. So let update wager
         */

        // Calculate current_selling_price
        $currentSellingPriceValue = $wagerEntity->getCurrentSellingPrice();
        $currentSellingPriceValue = $currentSellingPriceValue - $buyingPriceValue;
        $wagerEntity->setCurrentSellingPrice($currentSellingPriceValue);

        /**
         * Calculate percentage_sold
         * selling_price = selling_percentage
         * buying_price = ? percent
         */
        $sellingPercentageValue = $wagerEntity->getSellingPercentage();
        $sellingPriceValue      = $wagerEntity->getSellingPrice();
        $soldPercentage         = round($buyingPriceValue * $sellingPercentageValue / $sellingPriceValue, 2);
        $totalSoldPercentage    = $wagerEntity->getPercentageSold() + $soldPercentage;
        $wagerEntity->setPercentageSold($totalSoldPercentage);
        $totalAmountSold = $wagerEntity->getAmountSold() + $buyingPriceValue;
        $wagerEntity->setAmountSold($totalAmountSold);

        try {
            $em->persist($wagerEntity);
            $em->flush();

            $object               = new \stdClass();
            $object->wager_id     = $wagerEntity->getId();
            $object->buying_price = $buyingPriceValue;
            $object->bought_at    = time();
            return $this->respondSucceedData($object);
        } catch (\Exception $exception) {
            return $this->respondBad($exception->getMessage());
        }
    }
}