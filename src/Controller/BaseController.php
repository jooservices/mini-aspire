<?php

namespace App\Controller;

use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class BaseController
 */
class BaseController extends AbstractController
{
    const VALID_TOKEN = 'MAEZSTcpIa-ZCD0hLXszbM9FaOqyPizV';

    /**
     * @var TranslatorInterface $trans
     */
    protected TranslatorInterface $trans;

    /**
     * @param TranslatorInterface $translator
     * @required
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->trans = $translator;
    }

    /**
     * Basic ACL checking
     * @param Request $request
     * @return bool|object|JsonResponse
     */
    protected function can(Request $request)
    {
        if (!$request->headers->has('Authorization') || strpos($request->headers->get('Authorization'),
                'Bearer ') !== 0) {
            return $this->respondDenied();
        }

        $authorizationHeader = $request->headers->get('Authorization');
        $token               = substr($authorizationHeader, 7);

        /**
         * Opcode only this token can do delete
         */
        if ($request->getMethod() === 'DELETE' && $token !== self::VALID_TOKEN) {
            return $this->respondDenied();
        }

        return true;
    }

    /**
     * Aka respondError for specific deny reason
     * @param string|null $message
     * @param int $httpCode
     * @return object|JsonResponse
     */
    protected function respondDenied(
        string $message = 'You have no permission',
        int $httpCode = Response::HTTP_FORBIDDEN
    ) {
        return $this->respondError($message, $httpCode);
    }

    /**
     * @param string $message
     * @param int $httpCode
     * @return object|JsonResponse
     */
    protected function respondError(string $message, int $httpCode)
    {
        return $this->json([
            //'error' => true,
            //'message' => $this->trans->trans($message)
            'error' => $message
        ])->setStatusCode($httpCode);
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    protected function respondSucceed(array $data)
    {
        return $this->json([
            'data' => $data,
            'error' => false,
        ]);
    }

    /**
     * @param object $data
     * @return JsonResponse
     */
    protected function respondSucceedData($data)
    {
        return new JsonResponse(json_encode($data), Response::HTTP_CREATED, [], true);
        //return $this->json($data)->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Aka respondError for specific bad request reason
     * @param string|null $message
     * @param int $httpCode
     * @return object|JsonResponse
     */
    protected function respondBad(
        string $message = 'Bad request',
        int $httpCode = Response::HTTP_BAD_REQUEST
    ) {
        return $this->respondError($message, $httpCode);
    }

    /**
     * @param int $userId
     * @return Users|object|JsonResponse
     */
    protected function validateUser(int $userId)
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var Users $user
         */
        if (!$user = $em->getRepository(Users::class)->find($userId)) {
            return $this->respondError('User not found', Response::HTTP_NOT_FOUND);
        }

        return $user;
    }
}
