<?php
/**
 * Created by PhpStorm.
 * User: 67517
 * Date: 27.07.2016
 * Time: 15:10
 */

namespace nwlBundle\Validator\Constraint;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DomainConstraint extends Constraint
{
    public $message = 'This value is not a parsable domain.';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'nwl_domain_validator';
    }
}
