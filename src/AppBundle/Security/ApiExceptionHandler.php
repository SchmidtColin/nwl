<?php
/**
 * Created by PhpStorm.
 * User: 72498
 * Date: 26.07.2016
 * Time: 08:45
 */

namespace AppBundle\Security;


use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;

class ApiExceptionHandler implements SubscribingHandlerInterface
{

    /**
     * Return format:
     *
     *      array(
     *          array(
     *              'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
     *              'format' => 'json',
     *              'type' => 'DateTime',
     *              'method' => 'serializeDateTimeToJson',
     *          ),
     *      )
     *
     * The direction and method keys can be omitted.
     *
     * @return array
     */
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException',
                'method' => 'normalizeException',
            ),
        );
    }

    public function normalizeException()
    {
        return array('message' => 'You shall not pass!');
    }
}