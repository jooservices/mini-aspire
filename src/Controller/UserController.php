<?php

namespace App\Controller;

use App\Entity\Users;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @Route("/user")
 */
class UserController extends BaseController
{
    /**
     * @Route("/create", methods={"POST"}, name="user_create")
     * @param Request $request
     * @return object|JsonResponse
     */
    public function create(Request $request)
    {
        $this->can($request);

        $em = $this->getDoctrine()->getManager();

        $requiredFields = ['full_name', 'first_name', 'last_name', 'email'];

        foreach ($requiredFields as $requiredField) {
            if ($request->get($requiredField)) {
                continue;
            }

            /**
             * @TODO Trans with variable
             */
            return $this->respondBad('Missing ' . $requiredField . ' field');
        }

        $fullName  = $request->get('full_name');
        $firstName = $request->get('first_name');
        $lastName  = $request->get('last_name');
        $email     = $request->get('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->respondBad('Invalid email');
        }

        // Validate exists user
        if ($user = $em->getRepository(Users::class)->findOneBy(['email' => $email])) {
            return $this->respondBad('User is existed');
        }

        $user = new Users();
        $user->setFullName($fullName);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);

        $em->persist($user);

        try {
            $em->flush();
            return $this->respondSucceed(['user' => $user]);
        } catch (\Exception $exception) {
            return $this->respondBad($exception->getMessage());
        }
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="user_get")
     * @param int $id
     * @param Request $request
     * @return Users|object|JsonResponse
     */
    public function read(int $id, Request $request)
    {
        $this->can($request);

        $user = $this->validateUser($id);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        return $this->respondSucceed(['user' => $user]);
    }

    /**
     * @Route("/{id}", methods={"POST"}, name="user_update")
     * @param int $id
     * @param Request $request
     * @return object|JsonResponse
     */
    public function update(int $id, Request $request)
    {
        $this->can($request);

        $em = $this->getDoctrine()->getManager();

        $user = $this->validateUser($id);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        $fullName  = $request->get('full_name');
        $firstName = $request->get('first_name');
        $lastName  = $request->get('last_name');
        $email     = $request->get('email');

        if (!$fullName && !$firstName && !$lastName && !$email) {
            return $this->respondBad();
        }

        if ($email) {
            /**
             * Validate email
             * @TODO Exclude current user
             */
            if ($em->getRepository(Users::class)->findOneBy(['email' => $email])) {
                return $this->respondBad('Email already used. Maybe yourself');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->respondBad('Invalid email');
            }

            $user->setEmail($email);
        }

        if ($fullName) {
            $user->setFullName($fullName);
        }

        if ($firstName) {
            $user->setFirstName($firstName);
        }

        if ($lastName) {
            $user->setLastName($lastName);
        }

        $user->setUpdated(new \DateTime());
        $em->persist($user);
        $em->flush();

        return $this->respondSucceed(['user' => $user]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="user_delete")
     * @param int $id
     * @param Request $request
     * @return object|JsonResponse
     */
    public function delete(int $id, Request $request)
    {
        $this->can($request);

        $em = $this->getDoctrine()->getManager();

        $user = $this->validateUser($id);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        $em->remove($user);
        $em->flush();

        /**
         * @TODO Remove related loans records or archive them
         */

        return $this->respondSucceed(['user' => $user]);
    }

}