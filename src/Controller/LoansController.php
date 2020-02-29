<?php

namespace App\Controller;

use App\Entity\Transactions;
use App\Entity\Users;
use App\Entity\UsersLoans;
use App\Repository\TransactionsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LoansController
 * @package App\Controller
 * @Route("/loan")
 */
class LoansController extends BaseController
{
    /**
     * Create new loan
     * @Route("/create", methods={"POST"}, name="loan_create")
     * @param Request $request
     * @return Users|object|JsonResponse
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $this->can($request);

        $em = $this->getDoctrine()->getManager();

        if (!$userId = $request->get('user_id')) {
            return $this->respondBad('Missing user_id field');
        }

        // Make sure user is exist
        $userEntity = $this->validateUser($userId);
        if ($userEntity instanceof JsonResponse) {
            return $userEntity;
        }

        $amount = (double)$request->get('amount');

        if ($amount <= UsersLoans::MINIMUM_AMOUNT) {
            return $this->respondBad('Amount can not lower than ' . UsersLoans::MINIMUM_AMOUNT);
        }

        $frequency = (int)$request->get('frequency');
        if ($frequency !== UsersLoans::FREQUENCY_DAILY && $frequency !== UsersLoans::FREQUENCY_MONTHLY && $frequency !== UsersLoans::FREQUENCY_YEARLY) {
            return $this->respondBad('Frequency must be daily or monthly or yearly');
        }

        $duration = (int)$request->get('duration');
        if (!$duration || $duration <= 0) {
            return $this->respondBad('Duration can not lower than 0');
        }

        if ($frequency === UsersLoans::FREQUENCY_YEARLY && $duration < 12) {
            return $this->respondBad('Your loan frequency is yearly but your duration lower than a year');
        }

        $now          = new \DateTime();
        $finishedDate = clone $now;
        // Duration based on month unit
        $finishedDate->add(new \DateInterval('P' . $duration . 'M'));

        $loanEntity = new UsersLoans();
        $loanEntity->setuser($userEntity);
        $loanEntity->setAmount($amount);
        $loanEntity->setFrequency($frequency);
        $loanEntity->setDuration($duration);
        // Interest could be 0
        $loanEntity->setInterest((float)$request->get('interest'));
        $amountLeft = $amount;
        $extraFees  = $request->get('extra_fees');
        if ($extraFees && is_array($extraFees)) {
            $loanEntity->setExtraFees(json_encode($extraFees));
            foreach ($extraFees as $extraFee) {
                /**
                 * @TODO Remove extra fee = 0. Assumed mistypo by input
                 */
                if (!is_numeric($extraFee)) {
                    /**
                     * Assume string is % value
                     * @TODO Validate float number
                     */
                    $extraFee = $amount * (float)$extraFee / 100;
                }

                $amountLeft += $extraFee;
                continue;
            }
        }

        $loanEntity->setAmountLeft($amountLeft);
        $loanEntity->setFinished($finishedDate);

        $em->persist($loanEntity);

        /**
         * Update total loan of user
         * Based on amount left ( which one already plus with fees )
         */
        $userEntity->setLoans($userEntity->getLoans() + $amountLeft);
        $userEntity->setUpdated(new \DateTime());
        $em->persist($userEntity);

        try {
            $em->flush();
            /**
             * @TODO
             * Actually loan is not "effect" if not activated. But for moment we are ignored it
             * Loan also can can't flood create. For real life i believe minimum is daily
             */
            return $this->respondSucceed(['loan' => $loanEntity]);
        } catch (\Exception $exception) {
            return $this->respondBad($exception->getMessage());
        }
    }

    /**
     * @Route("/repayment/{id}", methods={"POST"}, name="loan_repayment")
     * @param int $id
     * @param Request $request
     * @return Users|object|JsonResponse
     */
    public function repayment(int $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var UsersLoans $loan
         */
        if (!$loan = $em->getRepository(UsersLoans::class)->find($id)) {
            return $this->respondError('Loan not found', Response::HTTP_NOT_FOUND);
        }

        /**
         * @TODO Can not make repayment for loan with loan or user be blocked
         */

        /**
         * @var TransactionsRepository $transactionsRepository ;
         */
        $transactionsRepository = $em->getRepository(Transactions::class);

        switch ($loan->getFrequency()) {
            case UsersLoans::FREQUENCY_DAILY;
                $transactions = $transactionsRepository->getTransactionsByDay($id);
                if (!empty($transactions)) {
                    return $this->respondBad('Already paid for today');
                }
                break;
            case UsersLoans::FREQUENCY_MONTHLY;
                $transactions = $transactionsRepository->getTransactionsByMonth($id);
                if (!empty($transactions)) {
                    return $this->respondBad('Already paid in this month');
                }
                break;
            case UsersLoans::FREQUENCY_YEARLY;
                $transactions = $transactionsRepository->getTransactionsByYear($id);
                if (!empty($transactions)) {
                    return $this->respondBad('Already paid in this year');
                }
                break;
        }

        $fullName = $request->get('full_name');
        $amount   = (double)$request->get('amount');

        if (!$fullName || !$amount) {
            return $this->respondBad();
        }

        /**
         * Validate amount
         * @TODO Actually in real life amount is "required" value based on contract. User must pay exactly required value not "any value"
         * For moment by lack of conditions & time. We are opened for any amount
         * The only conditions used
         * - Amount can't larger than amount left
         * - Not duplicate repayment based on frequency
         */

        if ($amount > $loan->getAmountLeft()) {
            return $this->respondBad('Over repayment');
        }

        $transaction = new Transactions();
        $transaction->setLoan($loan);
        $transaction->setFullName($fullName);
        $transaction->setAmount($amount);
        $loan->setAmountLeft($loan->getAmountLeft() - $amount);

        $userEntity = $loan->getUser();
        $userEntity->setLoans($userEntity->getLoans() - $amount);

        $em->persist($transaction);
        $em->persist($loan);
        $em->persist($userEntity);

        try {
            $em->flush();
            return $this->respondSucceed(['transaction' => $transaction]);
        } catch (\Exception $exception) {
            return $this->respondBad($exception->getMessage());
        }
    }
}
